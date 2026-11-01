<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Research;
use App\Models\ResearchEmbedding;
use App\Services\AI\AIService;

class GenerateEmbeddings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'embeddings:generate {--force : Force regenerate all embeddings}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate missing AI embeddings for approved researches';

    /**
     * Execute the console command.
     */
    public function handle(AIService $ai)
    {
        $this->info('Starting AI Embedding generation process...');

        $force = $this->option('force');

        $query = Research::where('status', 'approved');

        if (!$force) {
            $query->whereDoesntHave('embedding', function($q) {
                $q->whereNotNull('title_embedding')
                  ->whereNotNull('content_embedding')
                  ->whereNotNull('objective_embedding');
            });
        }

        $researches = $query->get();

        if ($researches->isEmpty()) {
            $this->info('No researches found that need embedding generation.');
            return;
        }

        $this->info("Found {$researches->count()} researches to process.");
        $bar = $this->output->createProgressBar($researches->count());

        foreach ($researches as $research) {
            try {
                $titleEmbedding = $ai->generateEmbedding($research->title);

                $content = $research->title . ' ' . $research->objectives . ' ' . $research->abstract;
                $contentEmbedding = $ai->generateEmbedding($content);

                $objectiveEmbedding = $ai->generateEmbedding($research->objectives);

                ResearchEmbedding::updateOrCreate(
                    ['research_id' => $research->id],
                    [
                        'title_embedding' => json_encode($titleEmbedding['embedding']),
                        'content_embedding' => json_encode($contentEmbedding['embedding']),
                        'objective_embedding' => json_encode($objectiveEmbedding['embedding'])
                    ]
                );

                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\nFailed to generate embedding for Research ID {$research->id}. Ensure AI Python service is running.");
                $this->error($e->getMessage());
                return;
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info('Embedding generation completed successfully!');
    }
}
