<?php

namespace Spekulatius\KeywordMerge\Tests;

use PHPUnit\Framework\TestCase;

class CharacterTest extends TestCase
{
    /**
     * @test
     */
    public function compareTests()
    {
        $kwcmp = new \Spekulatius\KeywordMerge\KeywordComparator();

        // Test cases
        $tests = [
            // Capitalisation shouldn't make a difference
            [
                'base' => 'Test cases',
                'compare' => 'TEST CASES',
            ],

            // Dash should be filtered out
            [
                'base' => 'seo tools',
                'compare' => 'seo-tools',
            ],

            // Same words lead to same characters, no diff.
            [
                'base' => 'seo tools firefox',
                'compare' => 'firefox seo tools',
            ],

            // No idea as 's' is already included.
            [
                'base' => 'side project ideas',
                'compare' => 'side projects ideas',
            ],
        ];

        // Run the tests one by one.
        foreach ($tests as $test) {
            $this->assertTrue(
                $kwcmp->compareCharacters($test['base'], $test['compare']),
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
            // "Chrome" != "Firefox"
            [
                'base' => 'seo tools firefox',
                'compare' => 'seo tools chrome',
            ],
        ];

        // Run the tests.
        foreach ($tests as $test) {
            $this->assertFalse(
                $kwcmp->compareCharacters($test['base'], $test['compare']),
                "Case: '${test['base']}' vs. '${test['compare']}'"
            );
        }
    }
}