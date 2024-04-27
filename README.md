# Pulsar

RSS Aggregator for [Gemini Protocol](https://geminiprotocol.net)

Simple RSS feed converter to static Gemtext format, useful for news portals or localhost reading

## Example

* `nex://[301:23b4:991a:634d::feed]/index.gmi` - [Yggdrasil](https://github.com/yggdrasil-network/yggdrasil-go) instance by YGGverse

## Usage

1. `git clone https://github.com/YGGverse/Pulsar.git`
2. `cp example/crawler.json crawler.json` - setup your feed locations
3. `php src/crawler.php` - grab feeds manually or using crontab

## Config

Configuration file supports multiple feed channels with custom settings:

* `source` - string, filepath or URL to the valid RSS feed
* `target` - string, relative or absolute path to Gemtext dumps
* `item`
  * `limit` - integer, how many items to display on page generated
  * `template` - string, custom pattern for feed item, that supports following macros
    * `{nl}` - new line separator
    * `{link}` - item link
    * `{guid}` - item guid
    * `{pubDate}` - item pubDate, soon with custom time format e.g. `{pubDate:Y-m-d H:s}`
    * `{title}` - item title
    * `{description}` - item description

Resulting files could be placed to any local folder (for personal reading) or shared with others (using [gmid](https://github.com/omar-polo/gmid), [twins](https://code.rocket9labs.com/tslocum/twins) or any other [server](https://github.com/kr1sp1n/awesome-gemini#servers) for `gemtext` statics)