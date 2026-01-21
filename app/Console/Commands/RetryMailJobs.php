<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class RetryMailJobs extends Command
{
    protected $signature = 'queue:retry-mail {batch=10} {--delay=300} {--once}';
    protected $description = 'Retry failed mail jobs in batches with optional delay in seconds';

    public function handle(): int
    {
        $batchSize = max(1, (int) $this->argument('batch'));
        $delay = max(0, (int) $this->option('delay'));

        $runOnce = (bool) $this->option('once');

        if ($runOnce) {
            $this->retryBatch($batchSize);
            return 0;
        }

        while (true) {
            $count = $this->retryBatch($batchSize);

            if ($count === 0) {
                $this->info('All failed mail jobs retried.');
                break;
            }

            if ($delay > 0) {
                $this->info("Waiting {$delay} seconds before next batch...");
                sleep($delay);
            }
        }

        return 0;
    }

    private function retryBatch(int $batchSize): int
    {
        $uuids = DB::table('failed_jobs')
            ->where('payload', 'like', '%App\\\\Mail%')
            ->orderBy('failed_at')
            ->limit($batchSize)
            ->pluck('uuid')
            ->filter()
            ->values()
            ->all();

        if (empty($uuids)) {
            $this->info('No failed mail jobs found.');
            return 0;
        }

        $this->info('Retrying jobs: ' . implode(', ', $uuids));
        Artisan::call('queue:retry', ['id' => $uuids]);
        $this->output->write(Artisan::output());

        return count($uuids);
    }
}
