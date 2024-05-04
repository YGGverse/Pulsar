# Pulsar

RSS Aggregator

## Components

* [x] `src/crawler.php` - scan configured RSS feeds and dump results to SQLite (see also [alternative branch](https://github.com/YGGverse/Pulsar/tree/fs))
* [ ] `src/nex.php` - server for [NEX Protocol](https://nightfall.city/nps/info/specification.txt)
* [ ] `src/gemini.php` - server for [Gemini Protocol](https://geminiprotocol.net)

## Example

* `nex://[301:23b4:991a:634d::feed]` - [Yggdrasil](https://github.com/yggdrasil-network/yggdrasil-go) instance by YGGverse

## Usage

1. `apt install git composer php-fpm php-pdo php-mbstring` - install dependencies
2. `git clone https://github.com/YGGverse/Pulsar.git`
3. `cd Pulsar` - navigate project folder
4. `composer update` - grab latest dependencies
5. `cp config/example.json name.json` - setup your feed
6. `php src/crawler.php config=name.json` - crawl feeds configured by `name.json` - manually or using crontab