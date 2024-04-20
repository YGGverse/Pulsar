# Pulsar

RSS Aggregator for [Gemini Protocol](https://geminiprotocol.net)

Simple RSS feed converter to static Gemtext format, useful for news portals or localhost reading

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

Resulting files could be placed to any local folder (for personal reading) or shared with others (using [gmid](https://github.com/omar-polo/gmid), [twins](https://code.rocket9labs.com/tslocum/twins) or any other [server](https://github.com/kr1sp1n/awesome-gemini#servers))

## Server

Pulsar comes with build-in [Titan-II](https://github.com/YGGverse/titan-II) server implementation.

It's especially useful for [Yggdrasil](https://github.com/yggdrasil-network/yggdrasil-go) users, who wish to host their feeds using plain IPv6 `0200::/7` addresses as the `CN` record. Build-in server contain this feature implemented from the box.

### Setup

* `cd Pulsar` - navigate to the project folder
* `composer update` - download server dependencies with Composer
* `mkdir server/127.0.0.1` - init server location (you can define any other destination, but `server` one is just git ignored)
* `cp example/host.json server/127.0.0.1/host.json` - copy configuration example to the destination folder
* `cd server/127.0.0.1` - navigate to server folder created and generate new self-signed certificate

On example above, certificate could be generated with following command:

```
openssl req -x509 -newkey rsa:4096 -keyout key.rsa -out cert.pem -days 365 -nodes -subj "/CN=127.0.0.1"
```

* _tip: for IPv6 address, just skip square brackets from `CN` value_

### Launch

* `php src/server.php server/127.0.0.1` - supported relative or absolute paths for systemd service

Open `gemini://127.0.0.1` in [Gemini browser](https://github.com/kr1sp1n/awesome-gemini#clients)!