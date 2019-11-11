---
layout: post
type: legacy
title: Go Module in Action
permalink: /page/go-module-in-action.html
---

For a long time, Golang provides an extremely simple dependency management model. It just depends on Git repos and actually its `master` branch.

If you have experience on concepts and tools like monorepo/Gerrit, you can easily get the point why it was initially designed like this[^1] including the existence of `GOPATH`. That is because **Google** uses monorepo.

There are some advantages claimed[^2] and Golang's dependency management could work on that well.

However, monorepo does not dominate the world. The dominance of open source community is GitHub. In contrast, GitHub follows fork & pull request workflow.

This (Go's dependency management design) leads to lots of issues:

* No tag, no semver. When `master` makes breaking change, it breaks build.
* Not compatible with GitHub's fork model, unless you make some hack on Git remote[^3].

To make it easier, there are some efforts:

* [Glide](https://glide.sh/)
* [Gopkg.in](http://labix.org/gopkg.in)
* [Govendor](https://github.com/kardianos/govendor)
* [Go Dep](https://github.com/golang/dep)

By these tools, we may handle vendors better. But these tools are not officialy supported and it needs commit all vendor packages to repository! Can you imagine a frontend or Node.js project commits its `node_modules`? This is ridiculous.

## Go Module

I don't know the decision process, but Golang team finally launched Go Module in 1.11, which embraces the non-monorepo part of the world.

* No `GOPATH`, and now we can clone to anywhere.
* `go get` inside a repo is not global, it works only at local.
* No `vendor`
* Dependencies are defined in `go.mod` with git tags, commit hashes, and semver.

## HOW TO

1. Install or upgrade to Go >=1.11 (currently 1.12)
2. Enable Go Module, edit `.bashrc` or `.zshrc`:

    ```sh
    export GO111MODULE=on
    ```

3. Initialize Go Module

    ```sh
    go mod init .
    ```

## Commands

* Add missing and remove unused modules

```sh
go mod tidy
```

* Add new module

```sh
go get github.com/crispgm/go-g
```

* Update a module

```sh
go get -u github.com/crispgm/go-g
```

* Update all modules

```sh
go get -u
```

* Get specific version of module

```sh
go get github.com/crispgm/go-g@master
go get github.com/crispgm/go-g@1.0.0
go get github.com/crispgm/go-g@617f32e
```

* Make vendored copy of dependencies

```sh
go mod vendor
```

I highly **not** recommend this, since getting rid of vendor is one of the major advantages of Go Module.

## Editor's Support

### VSCode

There is a [Go modules support](https://github.com/Microsoft/vscode-go/wiki/Go-modules-support-in-Visual-Studio-Code) guide in [vscode-go](https://github.com/microsoft/vscode-go) plugin. It is actually very easy that we just need to enable Language Server for go by adding one line to `settings.json`:

```json
"go.useLanguageServer": true
```

## Troubles Encountered

* "Dependency's Dependency"

One of our in-house library depends on [satori/go.uuid](https://github.com/satori/go.uuid), with traditional `go get` it means depend on `master`. But Go Module got the latest tag by default. So the solution should be:

```shell
go get -u github.com/satori/go.uuid@master
```

* Got "ambiguous import: found github.com/ugorji/go/codec in multiple modules"

It is actually caused by [gin-gonic/gin](https://github.com/gin-gonic/gin) (the web framework we use) and there is a [solution](https://github.com/gin-gonic/gin/issues/1673):

Add the following to `go.mod` to solve the trouble.

```sh
replace github.com/ugorji/go v1.1.4 => github.com/ugorji/go/codec v0.0.0-20190204201341-e444a5086c43
```

## Conclusion

Go Module is not a silver bullet, and it is more a compatibility than a fix. But for me, it is great improvement.

---

[^1]: <http://benjvi.com/2016/07/05/Dependency-Management-In-Golang>
[^2]: <https://en.wikipedia.org/wiki/Monorepo>
[^3]: <https://dev.to/loderunner/working-with-forks-in-go-3ab6>
