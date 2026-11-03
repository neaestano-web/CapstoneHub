<?php

return [
    // Studies below this semantic similarity score are not shown in results.
    // Recommended threshold 50%: ensures high precision for both semantic embeddings and lexical matches,
    // while preventing incidental or single-word matches from cluttering results.
    'minimum_score' => env('SIMILARITY_MIN_SCORE', 50),
];
