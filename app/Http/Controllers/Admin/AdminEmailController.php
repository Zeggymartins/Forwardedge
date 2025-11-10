<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\Campaigns\PrepareEmailCampaign;
use App\Models\EmailCampaign;
use App\Services\EmailTargetCollector;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

        $search = trim((string) $request->input('q'));
        $filtered = $allContacts;

        if ($search !== '') {
            $filtered = $filtered->filter(function ($contact) use ($search) {
                $haystack = strtolower($contact['email'] . ' ' . ($contact['name'] ?? '') . ' ' . ($contact['source'] ?? ''));
                return Str::contains($haystack, strtolower($search));
            })->values();
        }

        $perPage = 50;
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
        return view('admin.pages.emails.campaigns.create');
    }

    public function campaignsStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:160',
            'subject'    => 'required|string|max:160',
            'subtitle'   => 'nullable|string|max:200',
            'hero_image' => 'nullable|string|max:255',
            'intro'      => 'nullable|string',
            'cta_text'   => 'nullable|string|max:120',
            'cta_link'   => 'nullable|url|max:255',
            'blocks'     => 'required|array|min:1',
            'blocks.*.type' => 'required|string|in:text,list,image,cards',
        ]);

        $blocks = $this->normalizeBlocks($request->input('blocks', []));

        if ($blocks->isEmpty()) {
            return back()->withErrors([
                'blocks' => 'Add at least one content block to this email.',
            ])->withInput();
        }

        EmailCampaign::create([
            'title'      => $validated['title'],
            'subject'    => $validated['subject'],
            'subtitle'   => $validated['subtitle'] ?? null,
            'hero_image' => $validated['hero_image'] ?? null,
            'intro'      => $validated['intro'] ?? null,
            'blocks'     => $blocks->values()->all(),
            'cta_text'   => $validated['cta_text'] ?? null,
            'cta_link'   => $validated['cta_link'] ?? null,
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

    protected function normalizeBlocks(array $blocks): Collection
    {
        return collect($blocks)->map(function ($block) {
            $type = Arr::get($block, 'type');

            if ($type === 'list') {
                $items = collect(preg_split("/\r\n|\r|\n/", (string) Arr::get($block, 'items')))
                    ->map(fn ($line) => trim($line))
                    ->filter()
                    ->values()
                    ->all();

                return [
                    'type'    => 'list',
                    'heading' => Arr::get($block, 'heading'),
                    'body'    => Arr::get($block, 'body'),
                    'items'   => $items,
                    'icon'    => Arr::get($block, 'icon'),
                ];
            }

            if ($type === 'image') {
                return [
                    'type'      => 'image',
                    'heading'   => Arr::get($block, 'heading'),
                    'image_url' => Arr::get($block, 'image_url'),
                    'caption'   => Arr::get($block, 'caption'),
                    'alt'       => Arr::get($block, 'alt'),
                ];
            }

            if ($type === 'cards') {
                $cards = collect(Arr::get($block, 'cards', []))
                    ->map(function ($card) {
                        return [
                            'title' => Arr::get($card, 'title'),
                            'body'  => Arr::get($card, 'body'),
                            'image' => Arr::get($card, 'image'),
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

            // default text block
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
}
