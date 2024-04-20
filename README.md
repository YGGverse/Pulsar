# Pulsar

RSS Aggregator for [Gemini Protocol](https://geminiprotocol.net)

Simple RSS feed converter to static Gemtext format, useful for news portals or localhost reading

## Example

* `gemini://[301:23b4:991a:634d::feed]` - [Yggdrasil](https://github.com/yggdrasil-network/yggdrasil-go) instance by YGGverse
  * `gemini://pulsar.yggverse.dedyn.io` - Internet alias

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

## Server

Pulsar comes with build-in [Titan-II](https://github.com/YGGverse/titan-II) server implementation.

It's especially useful for [Yggdrasil](https://github.com/yggdrasil-network/yggdrasil-go) users, who wish to host their feeds using plain IPv6 `0200::/7` addresses without domain in `CN` record. Build-in server contain this feature implemented from the box.

### Setup

* `cd Pulsar` - navigate to the project folder
* `composer update` - download server dependencies with Composer
* `mkdir server/127.0.0.1` - init server location (you can define any other destination, `server` just git ignored)
* `cp example/host.json server/127.0.0.1/host.json` - copy configuration example to the destination folder
* `cd server/127.0.0.1` - navigate to server folder created and generate new self-signed certificate

On example above, certificate could be generated with following command:

```
openssl req -x509 -newkey rsa:4096 -keyout key.rsa -out cert.pem -days 365 -nodes -subj "/CN=127.0.0.1"
```

* _tip: for IPv6 address, just skip square brackets from `CN` value_

### Launch

* `php src/server.php server/127.0.0.1` - supported relative or absolute paths (for systemd service)

Open `gemini://127.0.0.1` in [Gemini browser](https://github.com/kr1sp1n/awesome-gemini#clients)!

### Autostart

Launch server as `systemd` service

Following example means you have Pulsar installed in home directory of `pulsar` user (`useradd -m pulsar`)

1. `sudo nano /etc/systemd/system/pulsar.service` - create new service file by following example:

``` /etc/systemd/system/pulsar.service
[Unit]
After=network.target

[Service]
Type=simple
User=pulsar
ExecStart=/usr/bin/php /home/pulsar/Pulsar/src/server.php /home/pulsar/Pulsar/server/127.0.0.1
StandardOutput=file:/home/pulsar/Pulsar/server/127.0.0.1/debug.log
StandardError=file:/home/pulsar/Pulsar/server/127.0.0.1/error.log
Restart=on-failure

[Install]
WantedBy=multi-user.target
```

2. `sudo systemctl daemon-reload` - reload systemd configuration
3. `sudo systemctl enable pulsar` - enable Pulsar service on system startup
4. `sudo systemctl start pulsar` - start Pulsar server