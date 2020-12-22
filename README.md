# PHP Keyword Comparator / Merger

An helper to compare similarity of keywords or list of keyword.

You will still need to a make a final call which helper(s) to use and how to configure them.

This package is intended to be used with [PHPScraper](https://github.com/spekulatius/phpscraper).

## Sponsors

This project is sponsored by:

<a href="https://bringyourownideas.com" target="_blank" rel="noopener noreferrer"><img src="https://bringyourownideas.com/images/byoi-logo.jpg" height="100px"></a>

Want to sponsor this project? [Contact me](https://peterthaleikis.com/contact).

## Install

```bash
composer require spekulatius/keyword-merge
```

## At a glance

Here are a few impressions on the way the library works:

```php
$kwp = new Spekulatius\KeywordMerge\KeywordComparator;

$kwp->matchesWord('tbilisi georgia', 'is tbilisi the capital of georgia?');
// false

$kwp->containsWord('tbilisi georgia', 'is tbilisi the capital of georgia?');
// true

$kwp->similarWord('tbilisi georgia', 'georgias tbilisi');
// true
```

You call also use arrays with correlated methods:

```php
$kwp = new Spekulatius\KeywordMerge\KeywordComparator;

$kwp->matchesWords('tbilisi georgia', 'is tbilisi the capital of georgia?');
// ['is tbilisi the capital of georgia?' => false]

$kwp->containsWords('tbilisi georgia', 'is tbilisi the capital of georgia?');
// ['is tbilisi the capital of georgia?' => true]

$kwp->similarWords('tbilisi georgia', 'is tbilisi the capital of georgia?');
// ['is tbilisi the capital of georgia?' => true]
```

## Related Links

 - [Simple performance test between PHPScraper and Python3 BeautifulSoup](https://github.com/spekulatius/link-scraping-test-beautifulsoup-vs-phpscraper)
 - [Example of a keyword length distribution using PHPScraper](https://github.com/spekulatius/phpscraper-keyword-length-distribution-example)
 - [Keyword Scraping Example using PHPScraper](https://github.com/spekulatius/phpscraper-keyword-scraping-example)
