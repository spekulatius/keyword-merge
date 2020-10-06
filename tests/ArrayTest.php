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
                    'tools for seo',
                    'seo toolkit',
                    'seo tools kit',
                    'test tools for seo',
                    'seo testing tools',
                    'chrome is a browser',
                ],

                // Expected results for each test:
                'expected_characters' => [
                    'seo-tools',
                ],
                'expected_words' => [
                    'seo-tools',
                    'tools for seo',
                ],
                'expected_levenshtain' => [
                    'seo-tools',
                    'seo toolkit',
                ],
            ],
        ];

        // Run the tests.
        foreach ($tests as $test) {
            $this->assertSame(
                $kwcmp->compareCharacters($test['base'], $test['compare']),
                $test['expected_characters'],
                "Characters Case: '${test['base']}' isn't working as expected."
            );


            $this->assertSame(
                $kwcmp->compareWords($test['base'], $test['compare']),
                $test['expected_words'],
                "Word Case: '${test['base']}' isn't working as expected."
            );


            $this->assertSame(
                $kwcmp->compareSimilarity($test['base'], $test['compare']),
                $test['expected_levenshtain'],
                "Levenshtain Case: '${test['base']}' isn't working as expected."
            );
        }
    }
}
