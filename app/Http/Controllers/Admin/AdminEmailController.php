<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\Campaigns\PrepareEmailCampaign;
use App\Models\EmailCampaign;
use App\Services\EmailTargetCollector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class AdminEmailController extends Controller
{
    public function __construct(protected EmailTargetCollector $collector)
    {
    }

    public function contacts(Request $request)
    {
        $allContacts = $this->collector->all();
        $totalContacts = $allContacts->count();
        $sourceBreakdown = $allContacts->groupBy('source')->map->count()->sortDesc();

        $perPageOptions = [25, 50, 100, 200];
        $perPage = (int) $request->input('per_page', 50);
        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = 50;
        }

        $search = trim((string) $request->input('q'));
        $filtered = $allContacts;

        if ($search !== '') {
            $filtered = $filtered->filter(function ($contact) use ($search) {
                $haystack = strtolower($contact['email'] . ' ' . ($contact['name'] ?? '') . ' ' . ($contact['source'] ?? ''));
                return Str::contains($haystack, strtolower($search));
            })->values();
        }

        $page = LengthAwarePaginator::resolveCurrentPage();
        $paginated = new LengthAwarePaginator(
            $filtered->slice(($page - 1) * $perPage, $perPage)->values(),
            $filtered->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('admin.pages.emails.contacts', [
            'contacts' => $paginated,
            'totalContacts' => $totalContacts,
            'sourceBreakdown' => $sourceBreakdown,
            'search' => $search,
            'sourceLabels' => $this->collector->availableSources(),
            'perPage' => $perPage,
            'perPageOptions' => $perPageOptions,
        ]);
    }

    public function campaignsIndex()
    {
        $campaigns = EmailCampaign::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.pages.emails.campaigns.index', compact('campaigns'));
    }

    public function campaignsCreate()
    {
        return view('admin.pages.emails.campaigns.create', [
            'sourceOptions' => $this->collector->availableSources(),
        ]);
    }

    public function campaignsStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:160',
            'subject'    => 'required|string|max:160',
            'subtitle'   => 'nullable|string|max:200',
            'hero_image' => 'nullable|image|max:4096',
            'intro'      => 'nullable|string',
            'cta_text'   => 'nullable|string|max:120',
            'cta_link'   => 'nullable|url|max:255',
            'cta_email_param' => 'nullable|string|max:40|regex:/^[a-zA-Z0-9_\\-]+$/',
            'blocks'     => 'required|array|min:1',
            'blocks.*.type' => 'required|string|in:text,list,image,cards',
            'audience_sources' => 'nullable|array',
            'audience_sources.*' => 'string',
            'include_emails' => 'nullable|string|max:10000',
            'exclude_emails' => 'nullable|string|max:10000',
        ]);

        $blocks = $this->normalizeBlocks($request, $request->input('blocks', []));

        if ($blocks->isEmpty()) {
            return back()->withErrors([
                'blocks' => 'Add at least one content block to this email.',
            ])->withInput();
        }

        $selectedSources = array_values(array_unique($validated['audience_sources'] ?? []));
        $includeEmails = $this->parseEmailList($request->input('include_emails'));
        $excludeEmails = $this->parseEmailList($request->input('exclude_emails'));

        if (empty($selectedSources) && empty($includeEmails)) {
            return back()->withErrors([
                'audience_sources' => 'Select at least one audience source or add manual emails to send to.',
            ])->withInput();
        }

        $heroPath = $request->hasFile('hero_image')
            ? $request->file('hero_image')->store('campaigns/hero', 'public')
            : null;

        EmailCampaign::create([
            'title'      => $validated['title'],
            'subject'    => $validated['subject'],
            'subtitle'   => $validated['subtitle'] ?? null,
            'hero_image' => $heroPath,
            'intro'      => $validated['intro'] ?? null,
            'blocks'     => $blocks->values()->all(),
            'cta_text'   => $validated['cta_text'] ?? null,
            'cta_link'   => $validated['cta_link'] ?? null,
            'cta_email_param' => $validated['cta_email_param'] ?? null,
            'audience_sources' => !empty($selectedSources) ? $selectedSources : [],
            'include_emails' => !empty($includeEmails) ? $includeEmails : null,
            'exclude_emails' => !empty($excludeEmails) ? $excludeEmails : null,
            'status'     => 'draft',
            'user_id'    => Auth::id(),
        ]);

        return redirect()
            ->route('admin.emails.campaigns.index')
            ->with('success', 'Campaign saved. You can send it whenever you are ready.');
    }

    public function campaignsShow(EmailCampaign $campaign)
    {
        $campaign->loadCount([
            'recipients as failed_count' => fn ($query) => $query->where('status', 'failed'),
            'recipients as skipped_count' => fn ($query) => $query->where('status', 'skipped'),
        ])->load([
            'recipients' => fn ($query) => $query->latest()->take(100),
            'user',
        ]);

        $recentRecipients = $campaign->recipients()->latest()->paginate(25);

        return view('admin.pages.emails.campaigns.show', compact('campaign', 'recentRecipients'));
    }

    public function campaignsSend(EmailCampaign $campaign): RedirectResponse
    {
        if ($campaign->status === 'sending') {
            return back()->with('info', 'This campaign is already sending.');
        }

        $campaign->forceFill([
            'status' => 'sending',
            'sent_count' => 0,
            'total_count' => 0,
            'last_error' => null,
        ])->save();

        PrepareEmailCampaign::dispatch($campaign->id, true);

        return back()->with('success', 'Campaign queued. Emails will start going out in the background.');
    }

    public function campaignsRetry(EmailCampaign $campaign): RedirectResponse
    {
        if ($campaign->status !== 'failed') {
            return back()->with('info', 'Only failed campaigns can be retried.');
        }

        $campaign->forceFill([
            'status' => 'sending',
            'last_error' => null,
        ])->save();

        PrepareEmailCampaign::dispatch($campaign->id, false);

        return back()->with('success', 'Retry started. We will re-send to the pending addresses.');
    }

    protected function normalizeBlocks(Request $request, array $blocks): Collection
    {
        return collect($blocks)->map(function ($block, $index) use ($request) {
            $type = Arr::get($block, 'type');

            if ($type === 'list') {
                $items = $this->normalizeCampaignListItems(Arr::get($block, 'items'));

                return [
                    'type'    => 'list',
                    'heading' => Arr::get($block, 'heading'),
                    'body'    => Arr::get($block, 'body'),
                    'items'   => $items,
                    'icon'    => Arr::get($block, 'icon'),
                ];
            }

            if ($type === 'image') {
                $path = $this->storeCampaignImage($request, "blocks.$index.image_file", 'campaigns/blocks');
                if (!$path && filled(Arr::get($block, 'existing_image'))) {
                    $path = Arr::get($block, 'existing_image');
                }

                return [
                    'type'      => 'image',
                    'heading'   => Arr::get($block, 'heading'),
                    'image_url' => $path,
                    'caption'   => Arr::get($block, 'caption'),
                    'alt'       => Arr::get($block, 'alt'),
                ];
            }

            if ($type === 'cards') {
                $cards = collect(Arr::get($block, 'cards', []))
                    ->map(function ($card, $cardIndex) use ($request, $index) {
                        $imagePath = $this->storeCampaignImage($request, "blocks.$index.cards.$cardIndex.image_file", 'campaigns/cards');
                        if (!$imagePath && filled(Arr::get($card, 'existing_image'))) {
                            $imagePath = Arr::get($card, 'existing_image');
                        }

                        return [
                            'title' => Arr::get($card, 'title'),
                            'body'  => Arr::get($card, 'body'),
                            'image' => $imagePath,
                        ];
                    })
                    ->filter(function ($card) {
                        return filled($card['title']) || filled($card['body']);
                    })
                    ->values()
                    ->all();

                return [
                    'type'    => 'cards',
                    'heading' => Arr::get($block, 'heading'),
                    'body'    => Arr::get($block, 'body'),
                    'cards'   => $cards,
                ];
            }

            return [
                'type'    => 'text',
                'heading' => Arr::get($block, 'heading'),
                'body'    => Arr::get($block, 'body'),
                'accent'  => Arr::get($block, 'accent'),
            ];
        })->filter(function ($block) {
            if ($block['type'] === 'image') {
                return filled($block['image_url']);
            }

            if ($block['type'] === 'cards') {
                return !empty($block['cards']);
            }

            return filled($block['heading']) || filled($block['body']) || !empty($block['items'] ?? []);
        });
    }

    protected function normalizeCampaignListItems(mixed $value): array
    {
        if (is_array($value)) {
            return collect($value)
                ->map(fn ($item) => trim((string) $item))
                ->filter()
                ->values()
                ->all();
        }

        if (is_string($value)) {
            return collect(preg_split("/\r\n|\r|\n/", $value))
                ->map(fn ($item) => trim((string) $item))
                ->filter()
                ->values()
                ->all();
        }

        return [];
    }

    protected function storeCampaignImage(Request $request, string $dotKey, string $directory): ?string
    {
        if ($file = $request->file($dotKey)) {
            return $file->store($directory, 'public');
        }

        return null;
    }

    protected function parseEmailList(?string $input): array
    {
        if (!$input) {
            return [];
        }

        $normalized = str_replace(["\r", ';'], ["\n", ','], $input);
        $parts = preg_split('/[\\n,]+/', $normalized);

        return collect($parts)
            ->map(function ($line) {
                $line = trim($line);
                if ($line === '') {
                    return null;
                }

                if (preg_match('/<([^>]+)>/', $line, $matches)) {
                    $line = $matches[1];
                }

                return strtolower($line);
            })
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();
    }
}
