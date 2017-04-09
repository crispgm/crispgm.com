---
layout: post
type: legacy
title: Olympus Camera Wi-Fi API
date: 2016/05/31 18:06:22 +0800
permalink: /page/olympus-wifi-api.html
tag:
- Olympus
- Camera
- Wi-Fi
- API
- Photography
- Ruby
---

# Background

Since I started my interest on photography, I chose SONY RX100 Mark III initially and now I use a M4/3 interchangeable lens camera, the Olympus E-M5 Mark II.

Cameras nowadays commonly support Wi-Fi sync with mobile apps. And so does Olympus, which provides an OI Share app to do that.

And I found some blog articles about the Wi-Fi protocol or commands of Olympus camera. Most of them are not well maintained, because Olympus camera is a minority choice in Internet world.

Fortunately, I found the [stv0g/libqt-omd](https://github.com/stv0g/libqt-omd) project on GitHub. Though the wiki is down, I could read the source code to figure out the APIs by myself.

# API Specification

The API specification is maintained at [Olympus Wi-Fi API Specification](https://github.com/crispgm/olympia/blob/master/api_specs.md).

Here is a useful gist [https://gist.github.com/mangelajo/6fa005ff3544fecdecfa](https://gist.github.com/mangelajo/6fa005ff3544fecdecfa) and there is an official protocol document in comment.

# Olympia

At last, I came up with an idea that make a web based client for computers built with the APIs, which is named Olympia. Thus, we can transfer and manage photos on our computer.

Here is a list of features:

* Web based UI
* Sync photos easily
* Manage photos both remotely and locally
* Upload or backup to online cloud storage

The project is [crispgm/olympia](https://github.com/crispgm/olympia), written in Ruby.
