<?php

namespace Spekulatius\KeywordMerge\Tests;

use PHPUnit\Framework\TestCase;

class InUrlPathTest extends TestCase
{
    /**
     * @test
     */
    public function compareTests()
    {
        $kwcmp = new \Spekulatius\KeywordMerge\KeywordComparator();

        // Test cases
        $tests = [
            // Exact match
            [
                'base' => 'https://test.com/seo-tools',
                'compare' => 'seo tools',
            ],

            // Dash instead of space. Should be converted before checking
            [
                'base' => 'https://test.com/seo-tools',
                'compare' => 'seo tools',
            ],

            // More words
            [
                'base' => 'https://test.com/seo-tools-and-toolkit',
                'compare' => 'toolkit',
            ],

            // One missing words is okay, if query is three words or more.
            [
                'base' => 'https://example.com/chrome-seo-tools',
                'compare' => 'chrome seo tools and toolkit',
            ],
        ];

        // Run the tests.
        foreach ($tests as $test) {
            $this->assertTrue(
                $kwcmp->inUrlPath($test['base'], $test['compare']),
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
            // Not matching at all
            [
                'base' => 'https://example.com/not-about-this-topic',
                'compare' => 'Test use cases',
            ],

            // Missing word "Chrome"
            [
                'base' => 'https://example.com/seo-tools',
                'compare' => 'chrome seo tools',
            ],

            // One word off is tolerated at 3+ words, but here it's two ("chrome" & "toolkit")
            [
                'base' => 'https://example.com/seo-tools',
                'compare' => 'chrome seo tools and toolkit',
            ],
        ];

        // Run the tests.
        foreach ($tests as $test) {
            $this->assertFalse(
                $kwcmp->inUrlPath($test['base'], $test['compare']),
                "Case: '${test['base']}' vs. '${test['compare']}'"
            );
        }
    }
}
