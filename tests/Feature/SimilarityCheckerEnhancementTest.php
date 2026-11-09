<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Http\Controllers\SimilarityController;
use App\Services\AI\AIService;
use ReflectionClass;

class SimilarityCheckerEnhancementTest extends TestCase
{
    private SimilarityController $controller;
    private ReflectionClass $reflection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new SimilarityController();
        $this->reflection = new ReflectionClass(SimilarityController::class);
    }

    private function invokeMethod(string $name, array $args = [])
    {
        $method = $this->reflection->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($this->controller, $args);
    }

    public function test_keyword_extraction_filters_stopwords_and_generic_fillers()
    {
        $text = "JHCSC Capstone Project: A Web-Based Research Repository System for JH Cerilles State College Using PHP and MySQL";
        $keywords = $this->invokeMethod('extractKeywords', [$text]);

        // Generic academic and institutional fillers should be stripped
        $this->assertNotContains('jhcsc', $keywords);
        $this->assertNotContains('system', $keywords);
        $this->assertNotContains('project', $keywords);
        $this->assertNotContains('for', $keywords);
        $this->assertNotContains('and', $keywords);

        // Core domain keywords should be preserved
        $this->assertContains('mysql', $keywords);
        $this->assertContains('php', $keywords);
    }

    public function test_textual_similarity_suppresses_generic_coincidences()
    {
        $query = "Web-Based Online Library Management System";
        $field = "Web-Based Agricultural Soil Management System";

        $inputKw = $this->invokeMethod('extractKeywords', [$query]);
        $inputBg = $this->invokeMethod('extractBigrams', [$query]);

        $res = $this->invokeMethod('computeTextualSimilarity', [$query, $field, $inputKw, $inputBg]);

        // Shared words like 'web', 'based', 'management', 'system' should be filtered out or penalized
        // Score must not be inflated (> 40%)
        $this->assertLessThan(40.0, $res['score']);
    }

    public function test_textual_similarity_rewards_domain_phrases_and_direct_keywords()
    {
        $query = "Early Flood Warning Alert System Using Ultrasonic Sensors";
        $field = "Arduino-Based River Flood Warning Alert and Monitoring with Ultrasonic Sensor Depth Tracking";

        $inputKw = $this->invokeMethod('extractKeywords', [$query]);
        $inputBg = $this->invokeMethod('extractBigrams', [$query]);

        $res = $this->invokeMethod('computeTextualSimilarity', [$query, $field, $inputKw, $inputBg]);

        // Strong multi-word match ("flood warning", "ultrasonic sensors")
        $this->assertGreaterThan(50.0, $res['score']);
        $this->assertContains('flood', $res['shared_keywords']);
        $this->assertContains('warning', $res['shared_keywords']);
        $this->assertContains('ultrasonic', $res['shared_keywords']);
    }

    public function test_objectives_parsing_separates_general_and_specific()
    {
        $objectives = "General Objective: To develop an automated student attendance logger. Specific Objectives: 1. To interface RFID hardware. 2. To build an SMS notification module. 3. To test real-time accuracy.";
        $parsed = $this->invokeMethod('parseObjectives', [$objectives]);

        $this->assertStringContainsString('attendance logger', $parsed['general']);
        $this->assertStringContainsString('RFID', $parsed['specific']);
        $this->assertStringContainsString('SMS notification', $parsed['specific']);
    }

    public function test_calibrated_scoring_recognizes_paraphrased_semantic_matches()
    {
        // High semantic (80%) but low textual (20%) due to different wording (e.g. inundation alert vs flood warning)
        $calibrated = $this->invokeMethod('calibrateCombinedScore', [80.0, 20.0, 0.60, 0.40, 1]);

        // Must reward semantic paraphrasing without pulling the score down excessively
        $this->assertGreaterThanOrEqual(70.0, $calibrated);
    }

    public function test_calibrated_scoring_dampens_false_positives()
    {
        // Low semantic (25%) despite apparent textual keyword match (50%)
        $calibrated = $this->invokeMethod('calibrateCombinedScore', [25.0, 50.0, 0.60, 0.40, 2]);

        // Must dampen the score below threshold (< 35%)
        $this->assertLessThan(35.0, $calibrated);
    }

    public function test_explanations_generate_meaningful_insights()
    {
        $research = new \App\Models\Research([
            'title' => 'IoT Flood Early Warning System',
        ]);
        $category = new \App\Models\ResearchCategory(['name' => 'Environmental Technology']);
        $research->setRelation('category', $category);

        $bestEval = [
            'name' => 'Research Title',
            'combined' => 84.5,
            'semantic' => 88.0,
            'textual' => 79.0,
        ];
        $sharedKw = ['flood', 'warning', 'ultrasonic'];
        $inputKw = ['flood', 'warning', 'ultrasonic', 'lorawan', 'solar'];

        $simExp = $this->invokeMethod('generateSimilarityExplanation', [
            'IoT Flood and River Inundation Alert',
            $research,
            'Research Title',
            $bestEval,
            $sharedKw
        ]);

        $this->assertStringContainsString('Research Title', $simExp);
        $this->assertStringContainsString('flood', $simExp);
        $this->assertStringContainsString('Semantic: 88%', $simExp);

        $diffExp = $this->invokeMethod('generateDifferencesExplanation', [
            'IoT Flood and River Inundation Alert',
            $research,
            'Research Title',
            $bestEval,
            $inputKw,
            $sharedKw
        ]);

        $this->assertStringContainsString('Environmental Technology', $diffExp);
        $this->assertStringContainsString('lorawan', $diffExp);
    }
}
