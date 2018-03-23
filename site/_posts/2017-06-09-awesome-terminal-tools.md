---
layout: post
type: programming
title: Awesome Terminal Tools
permalink: /page/awesome-terminal-tools.html
tags:
- Awesome
- Terminal
- Tools
- Go
- Node.js
---

This article recommends awesome (and even funny) terminal tools, both executables and libraries.

I first posted this on Oct 22th, 2015. And it is renewed on Mar 23rd, 2018.

## Softwares

### [tldr](https://tldr-pages.github.io/)

📚 Simplified and community-driven man pages.

```shell
$ tldr ps

ps

Information about running processes.

- List all running processes:
    ps aux

- List all running processes including the full command string:
    ps auxww

- Search for a process that matches a string:
    ps aux | grep string
```

### [lock-cli](https://github.com/sindresorhus/lock-cli)

Lock your system from the command-line. Support macOS, Linux, and Windows.

```shell
$ lock
```

Then, your OS will be locked.

### [Gotty](https://github.com/yudai/gotty)

An awesome live broadcasting tool, which output your terminal, written with Go.

![](https://raw.githubusercontent.com/yudai/gotty/master/screenshot.gif)

### [Wego](https://github.com/schachmat/wego)

Weather forecasting with ASCII GUI written in Go.

![](https://camo.githubusercontent.com/c3d2b92671f1ded5d5a9a9ebafdc836527f97269/687474703a2f2f7363686163686d61742e6769746875622e696f2f7765676f2f7765676f2e676966)

### [Qrcode-terminal](https://github.com/gtanner/qrcode-terminal)

Generate and display QR code with Node.js.

![](https://camo.githubusercontent.com/1b87ab92f230c35ff19abf2449e0fd52bea3f124/68747470733a2f2f7261772e6769746875622e636f6d2f6774616e6e65722f7172636f64652d7465726d696e616c2f6d61737465722f6578616d706c652f62617369632e706e67)

### [Douban.fm](https://github.com/turingou/douban.fm)

Console based [Douban FM](https://douban.fm/) client, written in Node.js. It runs on Mac but not very stable.

![](https://camo.githubusercontent.com/ca0a75a041cb65d1ad9dddc2e44b1c52903db7d0/687474703a2f2f7777312e73696e61696d672e636e2f6c617267652f36316666306465337477316563696a3364713830626a32306d3430657a3735752e6a7067)

### [ProxyChains](https://github.com/rofl0r/proxychains-ng)

Socks and HTTP proxy for terminal. And we can use `proxychains` to boost speed of some services and get across the wall.

```
$ proxychains4 git push
```

### [coinmon](https://github.com/bichenkk/coinmon)

💰 Cryptocurrency price ticker CLI.

```
$ coinmon
```

![](https://raw.githubusercontent.com/bichenkk/coinmon/master/screenshot.png)

## Libraries

### [Terminal Table](https://github.com/tj/terminal-table)

_Terminal Table is a fast and simple, yet feature rich ASCII table generator written in Ruby._

Simple but useful gem for Ruby. `terminal-table` is really powerful and easy-to-use to build and display tables in console based GUI.

This is the official example of `terminal-table`:

```
rows = []
rows << ['One', 1]
rows << ['Two', 2]
rows << ['Three', 3]
table = Terminal::Table.new :rows => rows

# > puts table
#
# +-------+---+
# | One   | 1 |
# | Two   | 2 |
# | Three | 3 |
# +-------+---+
```

## macOS Specifics

### [MAS](https://github.com/mas-cli/mas)

_Mac App Store command line interface._

It is designed for scripting and automation. However, I really like and need this awesome tool to update with terminal instead of the AppStore GUI, which fails frequently in China mainland.

```
$ mas list

724472954 Manico (2.3.2)
623795237 Ulysses (2.8.2)
411246225 Caffeine (1.1.1)
409203825 Numbers (4.1.1)
836500024 WeChat (2.2.8)
928871589 Noizio (1.5)
409201541 Pages (6.1.1)
784801555 Microsoft OneNote (15.34)
409183694 Keynote (7.1.1)
410628904 Wunderlist (3.4.6)
973134470 Be Focused (1.6.2)
```

```
$ mas upgrade

Upgrading 1 outdated application:
WeChat (2.2.8)
==> Downloading WeChat
==> Installed WeChat
```

### [m-cli](https://github.com/rgcr/m-cli)

_Swiss Army Knife for macOS!_ By `m-cli`, it enables you to control everything of Mac with commands.

```
$ m 

  Swiss Army Knife for macOS ! 


usage:  m [OPTIONS] COMMAND [help]

    OPTIONS
        --update        update m-cli to the latest version
        --uninstall     uninstall m-cli

    COMMANDS:
        help
        battery
        bluetooth
        dir
        disk
        display
        dns
        dock
        finder
        firewall
        gatekeeper
        group
        hostname
        info
        itunes
        lock
        network
        nosleep
        notification
        ntp
        printer
        restart
        safeboot
        screensaver
        service
        shutdown
        sleep
        timezone
        trash
        update
        user
        volume
        vpn
        wallpaper
        wifi
```

```
$ m battery status
Now drawing from 'AC Power'
 -InternalBattery-0 (id=3997795)	100%; charged; 0:00 remaining present: true
```
