---
layout: post
type: programming
title: Neovim (0.5) Is Overpowering
permalink: /page/neovim-is-overpowering.html
---

I have heard about Neovim for a long time. I can say that I really appreciate and adopt the ideas of Neovim --
it’s more progressive, embraces open source community, and tries to make Neovim approachable to more developers.

However, for its functionality, I didn’t see much difference between Vim. There are various reasons.
Firstly, Neovim is a drop-in Vim, so even the plugins could compat each other without much modification,
if it is not implemented with Vim or Neovim specific feature.
And Neovim somehow pushes Vim to evolve and release similar improvement. e.g., asynchronous job.
For an end-user without much deeper knowledge, the biggest differences might be  `XDG_PATH` based directory and `init.vim` file name.

Until then, I found Neovim 0.5 and try Neovim nightly because of [ThePrimeagen](https://github.com/ThePrimeagen){:target="_blank"} and [tjdevries](https://github.com/tjdevries){:target="_blank"}, which impacted me with significant differences and power.

## Modern Editor Technologies

Neovim 0.5 brings in new features that important to a modern code editor:
- A built-in LSP (Language Server Protocol) client
- Tree-sitter syntax engine
- Lua API improvements

### Built-in LSP Client

There are great language plugins in Vim ecology, but it is not easy to handle.
For each language, users may need to get a plugin for it and configure a lot. There was not a unified way to make it.
But language server does.

[Language Server Protocol](https://en.wikipedia.org/wiki/Language_Server_Protocol) is created by Microsoft, RedHat and Codenvy,
which provides language features (e.g. completion, navigation, formatting, and so on) through JSON-based RPC protocol,
so IDEs and editors are able to follow a unified protocol for different languages.

Vim use `cscope`  to handle these features but it is not good for every language and not intelligent enough.

There are popular Vim LSP clients include [ale](https://github.com/dense-analysis/ale){:target="_blank"},
[coc.vim](https://github.com/neoclide/coc.nvim){:target="_blank"},
[LanguageClient-neovim](https://github.com/autozimu/LanguageClient-neovim){:target="_blank"},
and [vim-lsp](https://github.com/prabirshrestha/vim-lsp){:target="_blank"}, which tries to bring language servers to Vim/Neovim.
But Neovim 0.5 embeds a built-in LSP client by itself instead of plugins.
Because [LSP is built for editors and “we can have nice things”]([https://www.youtube.com/watch?v=ArwDgvYEZYk){:target="_blank"}.

We just install any language servers we want. Then, code completion and a bunch of goto features are ready for us.

And a notice here, before moving to Neovim, I have tried LSP with Visual Studio Code.
It overall works well but for huge repos, there is performance issue.
When you open repo with tons of codes (e.g. Thrift generated codes),
language server can be very slow at first and cost much CPU time.

### [tree-sitter](https://github.com/tree-sitter/tree-sitter)

Tree-sitter is a parser generator tool and Neovim uses it to build syntax tree, in order to understand source code better.
As a result, now Neovim provides better highlight
(here is a [Gallery of tree-sitter powered features in Neovim](https://github.com/nvim-treesitter/nvim-treesitter/wiki/Gallery))
and it can also be used to create awesome feature, e.g. syntax-based text object.

Showcase of nvim-treesiter:
![nvim-treesiter](https://raw.githubusercontent.com/nvim-treesitter/nvim-treesitter/master/assets/example-cpp.png)

And I added [highlights patches](https://github.com/crispgm/nord-vim/blob/develop/colors/nord.vim#L306)
for my favortie colorscheme with support on Tree-sitter symbols:
```vim
"+-----------+
" treesitter +
"+-----------+

call s:hi("TSError" , s:nord11_gui, "", s:nord11_term, "", "", "")
call s:hi("TSPunctDelimiter", s:nord6_gui, "", s:nord6_term, "", "", "")
call s:hi("TSPunctBracket", s:nord6_gui, "", s:nord6_term, "", "", "")
call s:hi("TSPunctSpecial", s:nord6_gui, "", s:nord6_term, "", "", "")
call s:hi("TSConstant", s:nord4_gui, "", "NONE", "", "", "")
call s:hi("TSConstBuiltin", s:nord4_gui, "", "NONE", "", "", "")
" ... more
```

### Lua API Improvements

Neovim has embraced Lua since v0.2.1 and and it keeps improving.
From now on, we can setup Neovim with `init.lua` over `init.vim`.
Here is a great [article about init.lua migration](https://oroques.dev/notes/neovim-init/).

But the Lua parts are not that ready for production. I think it’s the vim API part.
We can do config Neovim with `init.lua`, but it looks not so native. e.g.:
* Calling to vim command is literally command string
* Config options is not that easy
* No native auto-commands support

There are several pull requests WIP on GitHub to make it easier and more user friendly.
If you chose to migrate to `init.lua` now, you would have faced refactoring though it would not be very hard.

## Nightly Plugins

Though Neovim 0.5 is still nightly, a bunch of plugins are made by awesome enthusiasts and contributors,
which leverage the power of the new features.

### [nvim-treesitter](https://github.com/nvim-treesitter/nvim-treesitter)

Though neovim embeds tree-sitter, it is not out-of-box for users. We need this to install the languages.

Installation:
```vim
Plug 'nvim-treesitter/nvim-treesitter', {'do': ':TSUpdate'}
Plug 'nvim-treesitter/playground'
```

And `:TSInstall [language]` to install a language parser. Sometimes, you may not notice what tree-sitter does.
Just try `:TSPlaygroudToggle`, you will have a clearer understanding of tree-sitter.

### [nvim-lspconfig](https://github.com/neovim/nvim-lspconfig)

It is similar to nvim-treesitter but for LSP client,
which is needed for install and communicate with language servers of each language.

Installation:
```vim
Plug 'neovim/nvim-lspconfig'
```

The language servers are needed to install externally on system level with any package manager you like.
I use Homebrew, `go get` and npm/yarn all together.
Follow [`CONFIG.md`](https://github.com/neovim/nvim-lspconfig/blob/master/CONFIG.md){:target="_blank"} for details of each language server.

Take Go and `gopls` as example:

Install `gopls`:
```shell
GO111MODULE=on go get golang.org/x/tools/gopls@latest
```

Then setup with `lspconfig`:
```lua
require'lspconfig'.gopls.setup{}
```

When you open a file, `:LspInfo` to know whether a LSP client is attached.

### [nvim-compe](https://github.com/hrsh7th/nvim-compe)

nvim-compe is an auto completion plugin for nvim. It completes based on LSP, buffers, and snippets.
It works with Neovim’s native LSP client.

![nvim-compe]({{ "/image/nvim-compe.png" | absolute_url }}){:style="width: 640px;"}

Installation:
```vim
Plug 'hrsh7th/nvim-compe'
```

There are also other choices. But I personally recommend `nvim-compe` for its good functionality and design,
with both well-organized and well-written source codes.

### [Telescope](https://github.com/nvim-telescope/telescope.nvim)

Last but the most, Telescope is one of the killer apps of Neovim nightly. It is similar to fzf, but with better user interface. We can do file navigation, live grep, buffer navigation, help tag/keymaps viewer and many more with Telescope.

```vim
" dependencies
Plug 'nvim-lua/popup.nvim'
Plug 'nvim-lua/plenary.nvim'
" telescope
Plug 'nvim-telescope/telescope.nvim'
```

Find file in Telescope:

![nvim-telescope]({{ "/image/nvim-telescope.png" | absolute_url }})

With Telescope, I leave FZF and rarely use file tree plugin again.

## Conclusion

I often come across with YouTube videos with ~~clickbait~~ titles like "configure Vim or Neovim like VSCode" (no offence).
As it says, VSCode is an awesome editor and set a standard for editors.

Neovim 0.5 is overpowering as the title says.
Give Neovim 0.5 a try, you will find that it is not difficult to make it as powerful as VSCode.
And I am also looking forward to seeing more nice things with Neovim.
