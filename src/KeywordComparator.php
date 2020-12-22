<?php

namespace Spekulatius\KeywordMerge;

class KeywordComparator
{
    /**
     * A list of characters to ignore.
     *
     * @var array
     */
    protected $ignore_characters = [
        // Searches with one or more spaces added
        ' ',

        // Searches for social media handles and hash tags
        '@',
        '#',

        // Searches with dash instead of spaces...
        '-',
    ];

    /**
     * A list of words to ignore.
     *
     * @var array
     */
    protected $ignore_words = [
        'for',
        'of',
        'to',
    ];

    /**
     * List processing for matchesWord.
     *
     * @param string $reference_keywords
     * @param array $compare_keywords
     * @return array
     */
    public function matchesWords(string $reference_keywords, array $compare_keywords)
    {
        $result = [];

        // Compare each of the keywords
        foreach ($compare_keywords as $compare_keyword) {
            $result[$compare_keyword] = $this->matchesWord($compare_keyword);
        }

        return $result;
    }

    /**
     * Checks if a set of words is matched within a string.
     * Splits the words on spaces and returns true, if the string is mostly the same.
     *
     * @param string $reference_keyword
     * @param string $compare_keyword
     * @return bool
     */
    public function matchesWord(string $reference_keyword, string $compare_keyword)
    {
        // Prepare the keywords
        $reference_keywords = $this->prepareWords($reference_keyword);
        $compare_keywords = $this->prepareWords($compare_keyword);

        // See if all words are contained
        $result = array_diff(
            $reference_keywords,
            array_intersect(
                $reference_keywords,
                preg_split('/[\s]+/', $compare_keywords)
            )
        );

        // Check if most words are contained.
        return
            count($reference_keywords) === count($result) ||
            count($reference_keywords) > 3 && count($reference_keywords) === 1+count($result);
    }

    /**
     * List processing for containsWord.
     *
     * @param string $reference_keywords
     * @param array $compare_keywords
     * @return array
     */
    public function containsWords(string $reference_keywords, array $compare_keywords)
    {
        $result = [];

        // Compare each of the keywords
        foreach ($compare_keywords as $compare_keyword) {
            $result[$compare_keyword] = $this->containsWord(
                $reference_keywords,
                $compare_keyword
            );
        }

        return $result;
    }

    /**
     * Checks if a set of words is used within a string.
     * Splits the words on spaces and returns true, if all or most are included.
     *
     * @param string $reference_keyword
     * @param string $compare_keyword
     * @return bool
     */
    public function containsWord(string $reference_keyword, string $compare_keyword)
    {
        // Prepare the keywords
        $reference_keywords = $this->prepareWords($reference_keyword);
        $compare_keywords = $this->prepareWords($compare_keyword);

        // See if all words are contained
        $result = array_diff(
            $reference_keywords,
            array_intersect(
                $reference_keywords,
                preg_split('/[\s]+/', $compare_keywords)
            )
        );

        // Check if most words are contained.
        return
            count($reference_keywords) === count($result) ||
            count($reference_keywords) > 3 && count($reference_keywords) === 1+count($result);
    }

    /**
     *
     *
     * @param string $reference_keywords
     * @param array $compare_keywords
     * @param float $base_threshold = 2
     * @param float $length_threshold = 1
     * @return array
     */
    public function compareSimilarWords(
        string $reference_keywords,
        array $compare_keywords,
        float $base_threshold = 2,
        float $length_threshold = 1
    ) {
        $result = [];

        // Compare each of the keywords
        foreach ($compare_keywords as $compare_keyword) {
            $result[$compare_keyword] = $this->compareSimilarWord(
                $reference_keywords,
                $compare_keyword
            );
        }

        return $result;
    }

    /**
     * Uses the levenshtein to compare similarity. Difference to the default
     *  levenshtein is the length configurable options to allow more flexibility.
     *
     * @param string $reference_keyword
     * @param string $compare_keyword
     * @param float $base_threshold = 2
     * @param float $length_threshold = 1
     * @return bool
     */
    public function compareSimilarWord(
        string $reference_keyword,
        string $compare_keyword,
        float $base_threshold = 2,
        float $length_threshold = 1
    ) {
        // Define a custom keyword length-specific threshold
        $keyword_threshold = $base_threshold + (mb_strlen($reference_keyword)/10)*$length_threshold;

        // Calculate the difference between the strings and compare it.
        // For superlong queries we just assume it's not similar.
        return (strlen($reference_keyword) < 128 && strlen($compare_keyword) < 128) ?
            (levenshtein($reference_keyword, $compare_keyword) < $keyword_threshold) : false;
    }

    /**
     * Ignore certain characters and words. Then splits the string into keywords.
     *
     * @param string $keyword
     * @return array
     */
    protected function prepareWords(string $keyword)
    {
        $result = [];

        // Ignore certain characters and split the keywords
        $keyword = str_replace($this->ignore_characters, ' ', strtolower($keyword));
        $keyword = preg_split('/[\s]+/', $keyword);

        // Remove some generic words
        $result = array_diff($diff, $this->ignore_words);

        return $result;
    }
}
