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

        // Allow questions with "?" to pass
        '?',
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
        'and',
        'with',
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
            if ($this->matchesWord($reference_keywords, $compare_keyword)) {
                $result[] = $compare_keyword;
            }
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
            $compare_keywords,
            $reference_keywords
        );

        // Check if most words are contained.
        return
            count($result) === 0 ||
            count($result) === 1 && count($reference_keywords) > 3;
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
            if ($this->containsWord($reference_keywords, $compare_keyword)) {
                $result[] = $compare_keyword;
            }
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
            array_intersect($reference_keywords, $compare_keywords)
        );

        // Check if most words are contained.
        return
            count($result) === 0 ||
            count($result) === 1 && count($reference_keywords) > 3;
    }

    /**
     * List processing for similarWord.
     *
     * @param string $reference_keywords
     * @param array $compare_keywords
     * @param float $base_threshold = 2
     * @param float $length_threshold = 1
     * @return array
     */
    public function similarWords(
        string $reference_keywords,
        array $compare_keywords,
        float $base_threshold = 2,
        float $length_threshold = 1
    ) {
        $result = [];

        // Compare each of the keywords
        foreach ($compare_keywords as $compare_keyword) {
            $isSimilar = $this->similarWord(
                $reference_keywords,
                $compare_keyword,
                $base_threshold,
                $length_threshold
            );

            if ($isSimilar) {
                $result[] = $compare_keyword;
            }
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
    public function similarWord(
        string $reference_keyword,
        string $compare_keyword,
        float $base_threshold = 1,
        float $length_threshold = .5
    ) {
        // Define a custom keyword length-specific threshold
        $keyword_threshold = $base_threshold + (mb_strlen($reference_keyword)/10)*$length_threshold;

        // Prepare the keywords
        $reference_keywords = $this->prepareWords($reference_keyword);
        $compare_keywords = $this->prepareWords($compare_keyword);

        // Sort and re-join to get the words in the same order.
        sort($reference_keywords);
        sort($compare_keywords);
        $reference_keyword = join(' ', $reference_keywords);
        $compare_keyword = join(' ', $compare_keywords);

        // Calculate the difference between the strings and compare it.
        // For superlong queries we just assume they are not similar.
        return
            (strlen($reference_keyword) > 128 || strlen($compare_keyword) > 128) ? false :
            (levenshtein($reference_keyword, $compare_keyword) < $keyword_threshold);
    }

    /**
     * Checks if a set of keywords is contained in a URL path (not domain).
     *
     * @param string $url
     * @param string $compare_keywords
     * @return bool
     */
    public function inUrlPath(string $url, string $compare_keywords)
    {
        // get the path
        $path = parse_url(strtolower($url), PHP_URL_PATH);

        // Prepare the keywords
        $compare_keywords = $this->prepareWords($compare_keywords);

        // Create a list of words not contained
        $missing = [];
        foreach ($compare_keywords as $compare_keyword) {
            if (mb_strpos($path, $compare_keyword) === false) {
                $missing[] = $compare_keyword;
            }
        }

        // Check if most words are contained.
        return
            count($missing) === 0 ||
            count($missing) === 1 && count($compare_keywords) > 3;
    }

    /**
     * List processing for inUrlPath.
     *
     * @param string $url
     * @param array $compare_keywords
     * @return array
     */
    public function inUrlPaths(string $url, array $compare_keywords)
    {
        $result = [];

        // Compare each of the keywords
        foreach ($compare_keywords as $compare_keyword) {
            if ($this->inUrlPath($url, $compare_keyword)) {
                $result[] = $compare_keyword;
            }
        }

        return $result;
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
        $keywords = str_replace($this->ignore_characters, ' ', strtolower($keyword));
        $keywords = preg_split('/[\s]+/', $keywords);

        // Remove some generic words
        $result = array_diff($keywords, $this->ignore_words);

        return $result;
    }
}
