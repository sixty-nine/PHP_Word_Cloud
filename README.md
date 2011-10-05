# Wordle-like clickable cloud of words in PHP

## Usage

See `demo/example.php`

### Frequency table filters

When building the frequency table from your words, you can specify filters that will alter or reject the words.

The built-in filters are:

 * `FTF_RemoveShortWords` will reject words shorter than the given size
 * `FTF_RemoveTrailingPunctuation` will remove points, colons, semi-colons, and interrogation or exclamation marks
   from the end of the words.
 * `FTF_RemoveUnwantedCharacters` will remove characters from a given list from the words.
 * `FTF_RemoveUnwantedWords` will reject words based on a blacklist.

You can create your own filter by implementing the interface `FrequencyTable\FrequencyTableFilterInterface`.

## Running the tests

    phpunit -c lib/src/Dreamcraft/WordCloud/Tests/phpunit.xml


## About

Inspired by http://www.wordle.net/

Author:

 * Daniel Barsotti / info [at] dreamcraft [dot] ch

Contributors:

 * jaskra
 * mrahmadt


## License

This source file is subject to the MIT license that is bundled  with this source code in the file LICENSE.

