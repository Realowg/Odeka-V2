<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\UploadMediaController;

class CleanupTempFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $hoursOld;

    /**
     * Create a new job instance.
     */
    public function __construct($hoursOld = 24)
    {
        $this->hoursOld = $hoursOld;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        UploadMediaController::cleanupOldTempFiles($this->hoursOld);
    }
}
