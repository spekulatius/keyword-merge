<?php

namespace Spekulatius\KeywordMerge\Tests;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * @test
     */
    public function compareTests()
    {
        $kwcmp = new \Spekulatius\KeywordMerge\KeywordComparator();

        $this->assertFalse(
            $kwcmp->matchesWord('tbilisi georgia', 'is tbilisi the capital of georgia?')
        );

        $this->assertTrue(
            $kwcmp->containsWord('tbilisi georgia', 'is tbilisi the capital of georgia?')
        );

        $this->assertTrue(
            $kwcmp->similarWord('tbilisi georgia', 'georgia tbilisi')
        );



        $this->assertSame(
            $kwcmp->matchesWords('tbilisi georgia', ['is tbilisi the capital of georgia?']),
            []
        );

        $this->assertSame(
            $kwcmp->containsWords('tbilisi georgia', ['is tbilisi the capital of georgia?']),
            ['is tbilisi the capital of georgia?']
        );

        $this->assertSame(
            $kwcmp->similarWords('tbilisi georgia', ['is tbilisi the capital of georgia?']),
            []
        );
    }
}
