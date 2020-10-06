<?php

namespace Spekulatius\KeywordMerge\Tests;

use PHPUnit\Framework\TestCase;

class LevenshteinTest extends TestCase
{
    /**
     * @test
     */
    public function compareTests()
    {
        $kwcmp = new \Spekulatius\KeywordMerge\KeywordComparator();

        // Test cases
        $tests = [
            // Dash instead of space
            [
                'base' => 'seo tools',
                'compare' => 'seo-tools',
            ],

            // Capitalization
            [
                'base' => 'seo tools',
                'compare' => 'SEO tools',
            ],

            // Same words, no diff.
            [
                'base' => 'SEO Tools',
                'compare' => 'SEO Tools',
            ],

            // Both should lead to the same result
            [
                'base' => 'pug mixins',
                'compare' => 'pug mixin',
            ],
            [
                'base' => 'pug mixin',
                'compare' => 'pug mixins',
            ],

            // Minor difference
            [
                'base' => 'side project ideas',
                'compare' => 'side projects ideas',
            ],
        ];

        // Run the tests.
        foreach ($tests as $test) {
            $this->assertTrue(
                $kwcmp->compareSimilarity($test['base'], $test['compare']),
                "Case: '${test['base']}' vs. '${test['compare']}'".levenshtein($test['base'],$test['compare'])
            );
        }
    }

    /**
     * @test
     */
    public function negativeCompareTests()
    {
        $kwcmp = new \Spekulatius\KeywordMerge\KeywordComparator();

        // Test cases
        $tests = [
            // Additional word with dash
            [
                'base' => 'Test cases',
                'compare' => 'Test use-cases',
            ],

            // Additional word
            [
                'base' => 'seo tools for firefox',
                'compare' => 'firefox seo tools',
            ],

            // Different word order
            [
                'base' => 'seo tools firefox',
                'compare' => 'firefox seo tools',
            ],
        ];

        // Run the tests.
        foreach ($tests as $test) {
            $this->assertFalse(
                $kwcmp->compareSimilarity($test['base'], $test['compare']),
                "Case: '${test['base']}' vs. '${test['compare']}'".levenshtein($test['base'],$test['compare'])
            );
        }
    }
}
