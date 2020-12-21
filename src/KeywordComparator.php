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
     * Checks if the difference in complete words is only a filler word such as 'for'.
     *
     * @param string $base_keyword
     * @param string|array $compare_keywords
     * @return bool
     */
    public function compareWords(string $base_keyword, $compare_keywords)
    {
        // Check if we are comparing one or multiple keywords
        if (!is_array($compare_keywords)) {
            $compare_keywords = [$compare_keywords];
        }

        // Prepare the base keyword
        $base_keyword = strtolower($base_keyword);
        $base_keyword = str_replace($this->ignore_characters, ' ', $base_keyword);
        $base_keyword = preg_split('/[\s]+/', $base_keyword);

        // Compare each of the keywords
        $result = [];
        foreach ($compare_keywords as $compare_keyword) {
            // Prepare the compare keyword
            $prepared_compare_keyword = $compare_keyword;
            $prepared_compare_keyword = strtolower($prepared_compare_keyword);
            $prepared_compare_keyword = str_replace($this->ignore_characters, ' ', $prepared_compare_keyword);

            // Identify the difference of the strings
            $diff = array_diff(
                preg_split('/[\s]+/', $prepared_compare_keyword),
                $base_keyword
            );

            // Remove common words which might make a different keyword, but usually aren't.
            $final_diff = array_diff($diff, $this->ignore_words);
            $similar = (count($final_diff) === 0);

            // Add the results to the array - depending on string|array at the start.
            if (count($compare_keywords) === 1) {
                $result[$compare_keyword] = $similar;
            } else if ($similar) {
                $result[] = $compare_keyword;
            }
        }

        return (count($compare_keywords) === 1) ? reset($result) : $result;
    }

    /**
     * Checks how similar the keywords based on the levenshtein algorithm
     *
     * @param string $base_keyword
     * @param string|array $compare_keywords
     * @param float $base_threshold = 2,
     * @param float $length_threshold = 1
     * @return bool
     */
    public function compareSimilarity(
        string $base_keyword,
        $compare_keywords,
        float $base_threshold = 2,
        float $length_threshold = 1
    ) {
        // Check if we are comparing one or multiple keywords
        if (!is_array($compare_keywords)) {
            $compare_keywords = [$compare_keywords];
        }

        // Remove ignore chars from the base keyword.
        // These shouldn't affect the result overly, but allow for tighter limits.
        $base_keyword = str_replace($this->ignore_characters, ' ', $base_keyword);

        // Compare each of the keywords
        $result = [];
        foreach ($compare_keywords as $compare_keyword) {
            // Remove some chars here as well.
            $prepared_compare_keyword = str_replace($this->ignore_characters, ' ', $compare_keyword);

            // Define a custom keyword length-specific threshold
            $keyword_threshold = $base_threshold + (mb_strlen($prepared_compare_keyword)/10)*$length_threshold;

            // Calculate the difference between the strings and compare it.
            // For superlong queries we just assume it's not similar.
            $similar = (strlen($base_keyword) < 128 && strlen($prepared_compare_keyword) < 128) ?
                (levenshtein($base_keyword, $prepared_compare_keyword) < $keyword_threshold) : false;

            // Arrange the result
            if (count($compare_keywords) === 1) {
                $result[$compare_keyword] = $similar;
            } else if ($similar) {
                $result[] = $compare_keyword;
            }
        }

        return (count($compare_keywords) === 1) ? reset($result) : $result;
    }

    /**
     * Checks if certain words are contained within a string.
     *
     * This is intended to check against URLs etc., if the contain certain strings.
     *
     * @param string $base_keywords
     * @param string $compare_keyword
     * @return bool
     */
    public function containWords(string $base_keywords, ?string $compare_keyword)
    {
        // Prepare the base keyword
        $base_keywords = strtolower($base_keywords);
        $base_keywords = str_replace($this->ignore_characters, ' ', $base_keywords);
        $base_keywords = preg_split('/[\s]+/', $base_keywords);

        // Compare each of the keywords
        $result = [];
        foreach ($base_keywords as $base_keyword) {
            // Check if all the keywords are in the string.
            if (false !== mb_strpos($compare_keyword, $base_keyword)) {
                $result[$base_keyword] = true;
            }
        }

        // All containing or if 3+ words all but one.
        return (
            count($base_keywords) === count($result) ||
            count($base_keywords) > 3 && count($base_keywords) === 1+count($result)
        );
    }
}
