<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TranslationService;

class TranslationsSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:sync
                            {--locale= : Specific locale to sync (e.g., en, fr)}
                            {--group= : Specific group to sync (e.g., admin, odeka)}
                            {--force : Force sync even if translations exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync translations from PHP language files to database';

    protected $translationService;

    /**
     * Create a new command instance.
     */
    public function __construct(TranslationService $translationService)
    {
        parent::__construct();
        $this->translationService = $translationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $locale = $this->option('locale');
        $group = $this->option('group');

        $this->info('Starting translation sync...');
        $this->newLine();

        if ($locale) {
            $this->info("Syncing locale: {$locale}");
        }

        if ($group) {
            $this->info("Syncing group: {$group}");
        }

        $this->newLine();

        try {
            $result = $this->translationService->syncFromFiles($locale, $group);

            // Display results
            $this->newLine();
            $this->info('Sync completed successfully!');
            $this->newLine();

            $this->table(
                ['Metric', 'Count'],
                [
                    ['Synced (new)', $result['synced']],
                    ['Skipped (existing)', $result['skipped']],
                    ['Errors', count($result['errors'])],
                ]
            );

            // Display errors if any
            if (!empty($result['errors'])) {
                $this->newLine();
                $this->error('Errors occurred during sync:');
                foreach ($result['errors'] as $error) {
                    $this->line("  - {$error}");
                }
            }

            $this->newLine();
            $this->comment('💡 Tip: Visit Admin → Translations to manage your translations via the web interface.');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Sync failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}