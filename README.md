# Pulsar

RSS Aggregator

## Components

* [x] `src/crawler.php` - scan configured RSS feeds and dump results to SQLite (see also [alternative branch](https://github.com/YGGverse/Pulsar/tree/fs))
* [ ] `src/nex.php` - server for [NEX Protocol](https://nightfall.city/nps/info/specification.txt)
* [ ] `src/gemini.php` - server for [Gemini Protocol](https://geminiprotocol.net)

## Example

* `nex://[301:23b4:991a:634d::feed]` - [Yggdrasil](https://github.com/yggdrasil-network/yggdrasil-go) instance by YGGverse

## Install

1. `apt install git composer php-fpm php-pdo php-mbstring` - install system dependencies
2. `git clone https://github.com/YGGverse/Pulsar.git` - get latest Pulsar version
3. `cd Pulsar` - navigate project folder
4. `composer update` - install application dependencies
5. `cp config/example.json name.json` - setup your feed

## Launch

* `php src/crawler.php config=name.json` - crawl feeds configured by `name.json` - manually or using crontab

### Arguments

* `config` - relative (to `config` folder) or absolute path to configuration file