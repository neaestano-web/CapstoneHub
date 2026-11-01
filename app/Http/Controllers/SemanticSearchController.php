<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Research;
use App\Models\SearchLog;
use App\Services\AI\AIService;

class SemanticSearchController extends Controller
{
    public function index()
    {
        return view('semantic-search.index');
    }

    public function search(
        Request $request,
        AIService $ai
    )
    {
        $request->validate([
            'query' => 'required|string'
        ]);

        $query = $request->input('query');

        $queryEmbedding =
            $ai->generateEmbedding($query);

        $researches =
            Research::where('status', 'approved')
                ->with(['embedding', 'category'])
                ->get();

        $results = [];

        foreach ($researches as $research)
        {
            if (!$research->embedding ||
                !$research->embedding->content_embedding)
            {
                continue;
            }

            $storedEmbedding = json_decode(
                $research->embedding->content_embedding,
                true
            );

            $score = $ai->compare(
                $queryEmbedding['embedding'],
                $storedEmbedding
            );

            $similarity = $score['similarity'] ?? 0;

            if ($similarity >= config('similarity.minimum_score', 60))
            {
                $results[] = [
                    'research' => $research,
                    'similarity' => $similarity
                ];
            }
        }

        $results = collect($results)
            ->sortByDesc('similarity')
            ->values();

        SearchLog::create([
            'user_id' => auth()->id(),
            'query' => $query
        ]);

        return view('semantic-search.results', [
            'results' => $results,
            'query' => $query
        ]);
    }
}
