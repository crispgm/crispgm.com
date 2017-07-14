---
layout: post
type: legacy
title: Enable HTTPS with Let's Encrypt
date: 2015/12/07 23:35:00 +0800
permalink: /page/enable-https-with-letsencrypt.html
tags:
- https
- letsencrypt
- nginx
---

[crispgm.com](https://crispgm.com) has enabled HTTPS as its default protocol, with the power of [Let's Encrypt](https://letsencrypt.org/).

## Why HTTPS?

When properly configured, an HTTPS connection guarantees three things:

* __Confidentiality.__ The visitor’s connection is encrypted, obscuring URLs, cookies, and other sensitive metadata.

* __Authenticity.__ The visitor is talking to the “real” website, and not to an impersonator or through a “man-in-the-middle”.

* __Integrity.__ The data sent between the visitor and the website has not been tampered with or modified.

A plain HTTP connection can be easily monitored, modified, and impersonated.

> Quoted from [https://https.cio.gov/faq/#what-information-does-https-protect?](https://https.cio.gov/faq/#what-information-does-https-protect?)

## About Let's Encrypt

> [Let’s Encrypt](https://letsencrypt.org/) is a free, automated, and open certificate authority (CA), run for the public’s benefit. Let’s Encrypt is a service provided by the Internet Security Research Group (ISRG).

Contribute to [letsencrypt](https://github.com/letsencrypt) on GitHub.

## About ACME

### ACME Protocol

[https://github.com/ietf-wg-acme/acme](https://github.com/ietf-wg-acme/acme)  
[https://github.com/letsencrypt/acme-spec](https://github.com/letsencrypt/acme-spec)

### Boulder

Boulder is an ACME-based CA, written in Go.

[https://github.com/letsencrypt/boulder](https://github.com/letsencrypt/boulder)

## Practice

### Documentation

[https://letsencrypt.readthedocs.org/](https://letsencrypt.readthedocs.org/)

### Get Certificate

As the ```letsencrypt-nginx``` is not fully developed, I choose ```certonly``` to generate SSL certificate and configure nginx manually.

```
./letsencrypt-auto certonly --webroot -w /path/to/webroot --email admin@example.com -d example.com
```

### Nginx Configuration

Configure nginx.conf

```
ssl_certificate      /etc/letsencrypt/live/crispgm.com/fullchain.pem;
ssl_certificate_key  /etc/letsencrypt/live/crispgm.com/privkey.pem;

ssl_session_timeout  1440m;     
```

Others are as default.

### Certificate Renewal

Let’s Encrypt CA issues short lived certificates (90 days). Make sure you renew the certificates at least once in 3 months.

### Performance

Actually, [crispgm.com](https://crispgm.com) is a full static site. Almost no difference on performance. :D

## In The End

Safe journey on [crispgm.com](https://crispgm.com) :)
