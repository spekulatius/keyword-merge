<?php

namespace Spekulatius\KeywordMerge\Tests;

use PHPUnit\Framework\TestCase;

class ArrayTest extends TestCase
{
    /**
     * @test
     */
    public function compareTests()
    {
        $kwcmp = new \Spekulatius\KeywordMerge\KeywordComparator();

        // Test cases
        $tests = [
            [
                'base' => 'seo tools',
                'compare' => [
                    'seo-tools',
                    'seo toolz',
                    'tools for seo',
                    'seo toolkit',
                    'seo tools kit',
                    'test tools for seo',
                    'seo testing tools',
                    'chrome is a browser',
                ],

                // Expected results for each test:
                'matchesWords' => [
                    'seo-tools',
                    'tools for seo',
                ],
                'containsWords' => [
                    'seo-tools',
                    'tools for seo',
                    'seo tools kit',
                    'test tools for seo',
                    'seo testing tools',
                ],
                'similarWords' => [
                    'seo-tools',
                    'seo toolz',
                ],
            ],
        ];

        // Run the tests.
        foreach ($tests as $test) {
            $this->assertSame(
                $kwcmp->matchesWords($test['base'], $test['compare']),
                $test['matchesWords'],
                "Matches Case: '${test['base']}' isn't working as expected."
            );

            $this->assertSame(
                $kwcmp->containsWords($test['base'], $test['compare']),
                $test['containsWords'],
                "Contains Case: '${test['base']}' isn't working as expected."
            );

            $this->assertSame(
                $kwcmp->similarWords($test['base'], $test['compare']),
                $test['similarWords'],
                "Levenshtein Case: '${test['base']}' isn't working as expected."
            );
        }
    }
}
