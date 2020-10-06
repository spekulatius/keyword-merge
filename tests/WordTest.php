<?php

namespace Spekulatius\KeywordMerge\Tests;

use PHPUnit\Framework\TestCase;

class WordTest extends TestCase
{
    /**
     * @test
     */
    public function compareTests()
    {
        $kwcmp = new \Spekulatius\KeywordMerge\KeywordComparator();

        // Test cases
        $tests = [
            // Dash instead of space.
            [
                'base' => 'seo tools',
                'compare' => 'seo-tools',
            ],

            // Captial letters
            [
                'base' => 'Test cases',
                'compare' => 'TEST CASES',
            ],

            // Just differently ordered.
            [
                'base' => 'seo tools firefox',
                'compare' => 'firefox seo tools',
            ],

            // "for" is filtered out
            [
                'base' => 'seo tools firefox',
                'compare' => 'firefox for seo tools',
            ],
        ];

        // Run the tests.
        foreach ($tests as $test) {
            $this->assertTrue(
                $kwcmp->compareWords($test['base'], $test['compare']),
                "Case: '${test['base']}' vs. '${test['compare']}'"
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
            // Additional word
            [
                'base' => 'Test cases',
                'compare' => 'Test use cases',
            ],

            // "Chrome" instead of "Firefox"
            [
                'base' => 'seo tools for firefox',
                'compare' => 'chrome seo tools',
            ],

            // "Projects" != "Project"
            [
                'base' => 'side project ideas',
                'compare' => 'side projects ideas',
            ],
        ];

        // Run the tests.
        foreach ($tests as $test) {
            $this->assertFalse(
                $kwcmp->compareWords($test['base'], $test['compare']),
                "Case: '${test['base']}' vs. '${test['compare']}'"
            );
        }
    }
}
