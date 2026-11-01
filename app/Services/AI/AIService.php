<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AIService
{
    private string $url;

    public function __construct()
    {
        $this->url = rtrim((string) config('services.ai.url', env('AI_SERVICE_URL', 'http://127.0.0.1:5000')), '/');
    }

    /**
     * Generate text embedding from the Python AI service.
     * Caches embeddings for 24 hours to eliminate duplicate CPU-heavy embedding calls.
     *
     * @param string $text
     * @return array
     */
    public function generateEmbedding(
        string $text
    ): array
    {
        $text = trim($text);
        if ($text === '') {
            return [];
        }

        $cacheKey = 'emb_' . md5($text);

        return Cache::remember($cacheKey, 86400, function () use ($text) {
            $response = Http::timeout(15)->post(
                $this->url.'/embedding',
                [
                    'text' => $text
                ]
            );

            return $response->json() ?? [];
        });
    }

    /**
     * In-memory Cosine Similarity computation in PHP.
     * Replaces hundreds of slow HTTP round-trips to Python with microsecond in-memory calculation.
     *
     * @param array $embedding1 Vector A
     * @param array $embedding2 Vector B
     * @return array ['similarity' => float] Percentage score (0 - 100)
     */
    public function compare(
        array $embedding1,
        array $embedding2
    ): array
    {
        $count = count($embedding1);
        if ($count === 0 || $count !== count($embedding2)) {
            return ['similarity' => 0.0];
        }

        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < $count; $i++) {
            $a = (float) $embedding1[$i];
            $b = (float) $embedding2[$i];
            $dotProduct += $a * $b;
            $normA += $a * $a;
            $normB += $b * $b;
        }

        if ($normA <= 0.0 || $normB <= 0.0) {
            return ['similarity' => 0.0];
        }

        $similarity = ($dotProduct / (sqrt($normA) * sqrt($normB))) * 100.0;

        return [
            'similarity' => round($similarity, 2),
        ];
    }

    /**
     * Classify research text into matching categories and project types.
     *
     * @param string $text Combined title + abstract + objectives
     * @param array  $categories List of category names
     * @param array  $projectTypes List of ['name' => ..., 'category' => ...]
     * @return array ['categories' => [...], 'project_types' => [...]]
     */
    public function classifyCategory(
        string $text,
        array $categories,
        array $projectTypes = []
    )
    {
        return Http::timeout(30)->post(
            $this->url.'/classify',
            [
                'text' => $text,
                'categories' => $categories,
                'project_types' => $projectTypes,
                'minimum_confidence' => config('classification.minimum_confidence', 40),
            ]
        )->json();
    }

    /**
     * Convert a DOCX file to PDF via the Python AI service.
     *
     * @param string $inputPath  Absolute path to the DOCX file
     * @param string $outputPath Absolute path for the output PDF
     * @return bool Whether the conversion succeeded
     */
    public function convertDocxToPdf(string $inputPath, string $outputPath): bool
    {
        $response = Http::timeout(120)->post(
            $this->url.'/convert-to-pdf',
            [
                'input_path' => $inputPath,
                'output_path' => $outputPath,
            ]
        );

        return $response->successful() && ($response->json('success') === true);
    }

    /**
     * Merge multiple documents (PDF or DOCX) into a single combined PDF via the Python AI service.
     *
     * @param array  $inputPaths  List of absolute paths to files in sequential order
     * @param string $outputPath Absolute path for the merged output PDF
     * @return bool Whether the merge succeeded
     */
    public function mergeDocuments(array $inputPaths, string $outputPath): bool
    {
        try {
            $response = Http::timeout(180)->post(
                $this->url.'/merge-documents',
                [
                    'input_paths' => array_values($inputPaths),
                    'output_path' => $outputPath,
                ]
            );

            return $response->successful() && ($response->json('success') === true);
        } catch (\Exception $e) {
            return false;
        }
    }
}
