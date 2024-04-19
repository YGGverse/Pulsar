# Pulsar

RSS Aggregator for [Gemini Protocol](https://geminiprotocol.net)

Simple RSS feed converter to static Gemtext format, useful for news portals or localhost usage.

## Usage

1. `git clone https://github.com/YGGverse/Pulsar.git`
2. `cp example/config.json config.json` - setup your feeds there!
3. `php src/crawler.php` - crontab schedule

## Config

Configuration file supports multiple feed channels with custom configurations:

* `source` - string, filepath or URL to the valid RSS feed
* `target` - string, relative or absolute path to Gemtext dumps
* `item`
  * `limit` - integer, how many items to display
  * `template` - string, custom pattern for feed item, that supports following macros
    * `{nl}` - new line separator
    * `{link}` - item link
    * `{guid}` - item guid
    * `{pubDate}` - item pubDate, soon with custom time format e.g. `{pubDate:Y-m-d H:s}`
    * `{title}` - item title
    * `{description}` - item description

Resulting files could be generated to the any folder for personal reading on localhost, or shared with others using [gmid](https://github.com/omar-polo/gmid), [twins](https://code.rocket9labs.com/tslocum/twins) or any other [Gemini server](https://github.com/kr1sp1n/awesome-gemini#servers).

## Instances

Coming soon!