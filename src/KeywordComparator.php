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

        // Searches for social media handles
        '@',

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
     * Checks if the difference is in the range of single characters and removes common cases
     *
     * @param string $base_keyword
     * @param string|array $compare_keywords
     * @param int $tolerance = 2
     * @return bool
     */
    public function compareCharacters(
        string $base_keyword,
        $compare_keywords,
        int $tolerance = 2
    ) {
        // Check if we are comparing one or multiple keywords
        if (!is_array($compare_keywords)) {
            $compare_keywords = [$compare_keywords];
        }

        // Compare each of the keywords
        $base_keyword_characters = str_split(strtolower($base_keyword));
        $result = [];
        foreach ($compare_keywords as $compare_keyword) {
            // Identify the difference of the strings
            $diff = array_diff(
                str_split(strtolower($compare_keyword)),
                $base_keyword_characters
            );

            // Remove common characters which might make a different keyword, but actually aren't.
            // If almost nothing left - keywords must be very similar
            $final_diff = array_diff($diff, $this->ignore_characters);
            $similar = (count($final_diff) < $tolerance);

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
     * @param float $threshold = 4
     * @return bool
     */
    public function compareSimilarity(
        string $base_keyword,
        $compare_keywords,
        float $threshold = 4
    ) {
        // Check if we are comparing one or multiple keywords
        if (!is_array($compare_keywords)) {
            $compare_keywords = [$compare_keywords];
        }

        // Compare each of the keywords
        $result = [];
        foreach ($compare_keywords as $compare_keyword) {
            // Calculate the difference between the strings and compare it.
            $similar = (levenshtein($base_keyword, $compare_keyword) < $threshold);

            // Arrange the result
            if (count($compare_keywords) === 1) {
                $result[$compare_keyword] = $similar;
            } else if ($similar) {
                $result[] = $compare_keyword;
            }
        }

        return (count($compare_keywords) === 1) ? reset($result) : $result;
    }
}
