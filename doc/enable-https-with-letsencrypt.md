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

TODO

### Get Certificate

As the ```letsencrypt-nginx``` is not fully developed, I choose ```certonly``` to generate SSL certificate and configure nginx manually.

```
    ./letsencrypt-auto certonly --webroot -w /path/to/webroot --email admin@example.com -d example.com
```

### Nginx Configuration

```
    ssl_certificate      /etc/letsencrypt/live/crispgm.com/fullchain.pem;
    ssl_certificate_key  /etc/letsencrypt/live/crispgm.com/privkey.pem;

    ssl_session_cache    shared:SSL:1m;
    ssl_session_timeout  1440m;
     
    ssl_ciphers "ECDHE-ECDSA-AES128-GCM-SHA256 ECDHE-ECDSA-AES256-GCM-SHA384 ECDHE-ECDSA-AES128-SHA ECDHE-ECDSA-AES256-SHA ECDHE-ECDSA-AES128-SHA256 ECDHE-ECDSA-AES256-SH    A384 ECDHE-RSA-AES128-GCM-SHA256 ECDHE-RSA-AES256-GCM-SHA384 ECDHE-RSA-AES128-SHA ECDHE-RSA-AES128-SHA256 ECDHE-RSA-AES256-SHA384 DHE-RSA-AES128-GCM-SHA256 DHE-RSA-AES256-GCM    -SHA384 DHE-RSA-AES128-SHA DHE-RSA-AES256-SHA DHE-RSA-AES128-SHA256 DHE-RSA-AES256-SHA256 EDH-RSA-DES-CBC3-SHA";
    ssl_prefer_server_ciphers  on;
```

### Performance

Actually, [crispgm.com](https://crispgm.com) is a full static site. Almost no difference on performance. :D

## Conclusion

Safe journey on [crispgm.com](https://crispgm.com) :)
