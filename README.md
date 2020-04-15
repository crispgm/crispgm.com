# [crispgm.com](crispgm.com)

[![](https://travis-ci.org/crispgm/crispgm.com.svg)](https://travis-ci.org/crispgm/crispgm.com)
[![Netlify Status](https://api.netlify.com/api/v1/badges/3cb069fc-ecc9-4da8-8ad1-435a9a75bee7/deploy-status)](https://app.netlify.com/sites/crispgm/deploys)

Source of [crispgm.com](https://crispgm.com/). Also available at: <https://crisp.dev>.

## Theme

The theme is called [Minimal](https://github.com/crispgm/minimal). It basically works but not exactly the same as this site.

## Dev

There are 3 Jekyll sites here and will be built into `gh-pages` branch.

### Shallow Clone

This is somehow huge. Use a shallow clone may save much time:

```shell
$ git clone git@github.com:crispgm/crispgm.com.git --depth 1
```

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
