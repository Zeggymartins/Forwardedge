<?php

namespace App\Console\Commands;

use App\Mail\ScholarshipApologyMail;
use App\Models\ScholarshipApplication;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class SendAutoRejectApology extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scholarships:send-auto-reject-apology {--dry-run : Only show who would receive the email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an apology email to applicants who were auto-rejected by mistake.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $affected = ScholarshipApplication::query()
            ->where(function ($q) {
                $q->where('auto_decision', 'reject')
                    ->orWhere('admin_notes', 'like', '%auto%reject%')
                    ->orWhere('decision_notes', 'like', '%reject%')
                    ->orWhere('admin_notes', 'like', '%reset from auto-reject%');
            })
            ->get();

        $recipients = $affected
            ->map(function ($app) {
                $formData = $app->form_data ?? [];
                $contactEmail = Arr::get($formData, 'contact.email') ?? Arr::get($formData, 'email');
                $name = Arr::get($formData, 'contact.name') ?? ($app->user->name ?? null);
                $email = strtolower(trim($app->user->email ?? $contactEmail ?? ''));

                return $email ? ['email' => $email, 'name' => $name] : null;
            })
            ->filter()
            ->unique('email')
            ->values();

        if ($recipients->isEmpty()) {
            $this->info('No recipients matched auto-reject conditions.');
            return Command::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->info('Dry run: would send apology to ' . $recipients->count() . ' recipient(s).');
            foreach ($recipients as $row) {
                $this->line('- ' . $row['email'] . ($row['name'] ? " ({$row['name']})" : ''));
            }
            return Command::SUCCESS;
        }

        $this->info('Sending apology to ' . $recipients->count() . ' recipient(s)...');

        $recipients->each(function ($row) {
            Mail::to($row['email'])->queue(new ScholarshipApologyMail($row['name']));
        });

        $this->info('Queued apology emails.');

        return Command::SUCCESS;
    }
}
