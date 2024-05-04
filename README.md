# Pulsar

RSS Aggregator

## Components

* [x] `src/crawler.php` - scan configured RSS feeds and dump results to SQLite (see also [FS branch](https://github.com/YGGverse/Pulsar/tree/fs))
* [ ] `src/nex.php` - server for [NEX Protocol](https://nightfall.city/nps/info/specification.txt)
* [ ] `src/gemini.php` - server for [Gemini Protocol](https://geminiprotocol.net)

## Example

* `nex://[301:23b4:991a:634d::feed]` - [Yggdrasil](https://github.com/yggdrasil-network/yggdrasil-go) instance by YGGverse

## Usage

1. `git clone https://github.com/YGGverse/Pulsar.git`
2. `cp config/example.json name.json` - setup your feed
3. `php src/crawler.php name.json` - grab feeds manually or using crontabdes