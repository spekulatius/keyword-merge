<?php

namespace Spekulatius\KeywordMerge\Tests;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * @test
     */
    public function set1()
    {
        $kwcmp = new \Spekulatius\KeywordMerge\KeywordComparator();

        $this->assertFalse(
            $kwcmp->matchesWord('tbilisi georgia', 'is tbilisi the capital of georgia?')
        );

        $this->assertTrue(
            $kwcmp->containsWord('tbilisi georgia', 'is tbilisi the capital of georgia?')
        );

        $this->assertTrue(
            $kwcmp->similarWord('tbilisi georgia', 'georgias tbilisi')
        );
    }

    /**
     * @test
     */
    public function set2()
    {
        $kwcmp = new \Spekulatius\KeywordMerge\KeywordComparator;

        $this->assertSame(
            [],
            $kwcmp->matchesWords('tbilisi georgia', ['is tbilisi the capital of georgia?'])
        );

        $this->assertSame(
            ['is tbilisi the capital of georgia?'],
            $kwcmp->containsWords('tbilisi georgia', ['is tbilisi the capital of georgia?'])
        );

        $this->assertSame(
            ['georgias tbilisi'],
            $kwcmp->similarWords('tbilisi georgia', ['georgias tbilisi'])
        );
    }

    /**
     * @test
     */
    public function set3()
    {
        $kwcmp = new \Spekulatius\KeywordMerge\KeywordComparator;

        $this->assertFalse(
            $kwcmp->inUrlPath('https://example.com/cats-are-awesome', 'seo tools')
        );

        $this->assertTrue(
            $kwcmp->inUrlPath('https://example.com/seo-tools', 'seo tools')
        );

        $this->assertTrue(
            $kwcmp->inUrlPath('https://example.com/chrome-seo-tools', 'chrome seo tools and toolkit')
        );
    }
}
