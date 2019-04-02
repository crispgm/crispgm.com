# [crispgm.com](crispgm.com)

[![](https://travis-ci.org/crispgm/crispgm.com.svg)](https://travis-ci.org/crispgm/crispgm.com)
[![Netlify Status](https://api.netlify.com/api/v1/badges/3cb069fc-ecc9-4da8-8ad1-435a9a75bee7/deploy-status)](https://app.netlify.com/sites/crispgm/deploys)

Source of [crispgm.com](https://crispgm.com/). Also available at: <https://crisp.dev> and <https://crisp.lol>.

Status Page: <http://stats.pingdom.com/nmfhsd4gur4u>

## Dev

There are 3 Jekyll sites here and will be built into `gh-pages` branch.

### Serve

```shell
# serve site
$ rake site:serve
# serve wiki
$ rake wiki:serve
# serve resume
$ rake resume:serve
# build
$ rake site:build
```

### Lint & Tests

```shell
# evaluate on different devices
$ rake site:evaluate
# lint scss
$ rake site:lint
# smoke test
$ rake test
```

## License

* All blogs are licensed under a CC BY-NC-ND 4.0 license.
* Site source is licensed under MIT License.
