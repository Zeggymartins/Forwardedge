<?php

namespace App\Console\Commands;

use App\Models\ScholarshipApplication;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetAutoRejectScholarships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scholarships:reset-auto-rejects';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset applications that were auto-rejected back to pending for manual review.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $query = ScholarshipApplication::query()
            ->where('auto_decision', 'reject')
            ->where('status', 'rejected');

        $total = (clone $query)->count();

        if ($total === 0) {
            $this->info('No auto-rejected applications found.');
            return Command::SUCCESS;
        }

        DB::transaction(function () use ($query) {
            $query->update([
                'status' => 'pending',
                'auto_decision' => 'pending',
                'rejected_at' => null,
                'admin_notes' => 'Reset from auto-reject to pending for manual review.',
            ]);
        });

        $this->info("Reset {$total} application(s) from auto-rejected to pending.");

        return Command::SUCCESS;
    }
}
