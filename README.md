# Pulsar

RSS Aggregator for different protocols

## Features

* [x] `src/crawler.php` - scan configured RSS feeds and dump results to SQLite DB (see also [alternative branch](https://github.com/YGGverse/Pulsar/tree/fs))
* [ ] `src/cleaner.php` - auto clean deprecated records in database
* [x] `src/server.php` - server launcher with multiple host support, based on [Ratchet](https://github.com/ratchetphp/Ratchet) asynchronous socket library
  * [x] [NEX Protocol](https://nightfall.city/nps/info/specification.txt)
  * [ ] [Gemini Protocol](https://geminiprotocol.net)

## Example

* `nex://[301:23b4:991a:634d::feed]` - [Yggdrasil](https://github.com/yggdrasil-network/yggdrasil-go) instance by YGGverse

## Install

1. `apt install git composer php-fpm php-pdo php-mbstring` - install system dependencies
2. `git clone https://github.com/YGGverse/Pulsar.git` - get latest Pulsar version
3. `cd Pulsar` - navigate project folder
4. `composer update` - install application dependencies
5. `cp config/example.json name.json` - setup your feed

## Crawler

* `php src/crawler.php config=name.json` - crawl feeds configured by `name.json` - manually or using crontab
  * `config` - relative (to `config` folder) or absolute path to configuration file

## Server

* `php src/server.php protocol=NPS config=name.json` - launch `NPS` server configured by `name.json`
  * `protocol` - supported options:
    * `NPS`
  * `config` - relative (to `config` folder) or absolute path to configuration file