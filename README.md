# Pulsar

RSS aggregator for different protocols

See also [alternative branch](https://github.com/YGGverse/Pulsar/tree/fs) to generate static `gemtext` from feeds.

## Features

* [x] `src/crawler.php` - scan configured RSS feeds and dump results to SQLite
* [ ] `src/cleaner.php` - auto clean deprecated records
* [x] `src/server.php` - server launcher for different protocols:
  * [x] [NEX](https://nightfall.city/nex/info/specification.txt) - based on [Ratchet](https://github.com/ratchetphp/Ratchet) `IoServer` asynchronous socket library
  * [ ] [Gemini](https://geminiprotocol.net)

## Example

* `nex://[301:23b4:991a:634d::feed]` - [Yggdrasil](https://github.com/yggdrasil-network/yggdrasil-go) instance by YGGverse

## Install

1. `apt install git composer php-fpm php-sqlite3 php-pdo php-mbstring` - install system dependencies
2. `git clone https://github.com/YGGverse/Pulsar.git` - get latest Pulsar version
3. `cd Pulsar` - navigate project folder
4. `composer update` - install application dependencies
5. `cp config/example.json name.json` - setup your feed

## Crawler

* `php src/crawler.php config=name.json` - crawl feeds configured by `name.json` - manually or using crontab
  * `config` - relative (to `config` folder) or absolute path to configuration file

## Server

Launch as many servers as wanted, for different protocols and configurations (provided as the arguments)

* `php src/server.php protocol=nex config=name.json` - launch `nex` protocol server with `name.json` config
  * `config` - relative (`config` folder) or absolute path to configuration file
  * `protocol` - server protocol, supported options:
    * `nex` - [NEX Protocol](https://nightfall.city/nex/info/specification.txt)
