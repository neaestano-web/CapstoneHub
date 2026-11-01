<?php

return [
    // Studies below this semantic similarity score are not shown in results.
    // Strictly filter results to only display studies with 60% and above semantic alignment.
    'minimum_score' => env('SIMILARITY_MIN_SCORE', 60),
];
