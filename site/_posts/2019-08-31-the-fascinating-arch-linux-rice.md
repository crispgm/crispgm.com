---
layout: post
type: programming
title: The Fascinating Arch Linux RICE
permalink: /page/the-fascinating-arch-linux-rice.html
---

Recently, I dive into Linux the third time. The first time is in my childhood, I was attracted by the magazines, which introduced me the hacker culture. The second time was weird, I wanted to focus on study instead of PC gaming.

This time, it’s totally different. [/r/unixporn](https://www.reddit.com/r/unixporn/) and [/r/MechanicalKeyboards](https://www.reddit.com/r/MechanicalKeyboards) attracted me. The most fascinating part of that is RICE or RICE culture.

## RICE

RICE is not the food rice. Instead, it is the acronym of Race Inspired Cosmetic Enhancements. Let me look up in the dictionary:

> Parts put on cars to make them look fast, when they have no internal tuning, and are actually slow as hell. [^1]

The definition is quite funny. It is exact the same behavior as we make a great UI for dev and feel as if we get better efficiency.

Just as I remember, my first deep experience on RICE is in the MMORPG game -- World of Warcraft. WoW is an epic Blizzard game, with great storyline and gaming experience.

Another fascinating part of WoW is the customizable user interface. For WoW, it provides a simple UI, even lacking of basic functions on quests, timers and so on. But its APIs are full pledged. Developers make lots of WoW plugins. And gamers composite the plugins into their own user interface. It’s a typical RICE art and I like to watch FD (first down) videos because of their great UI in different styles. And I spent a lot of time compositing my own one.

My WoW Interfaces:

![my-wow-interface-1]({{ "/image/rice-wow-interface1.jpg" | absolute_url }})
![my-wow-interface-2]({{ "/image/rice-wow-interface2.jpg" | absolute_url }})

## Linux RICE

Mac/macOS are great hardware and software. But Apple seems never really create the soil for RICE. Apple’s idea is to create great product where hardware and software are seamless and easy to use for normal users. RICE has nothing to do with easy to use by its definition. And macOS has long time been an authority software and closed ecosystem, so that we can hardly make deep customization into system core.

And it’s similar to Windows. RICE should only be great in the Linux world.

Try Ubuntu?

Ubuntu may be the most popular Linux distribution. I was once an Ubuntu user. It is a great distribution, which is maybe the first beginner-friendly Linux distribution. It is a success to get big population within developers and is pre-installed on some cheaper PCs.

But to some extent, it is a failure. For developers, they may use Linux a lot but they may not actually use a Linux desktop. Most of them live in Terminal or even access Linux remotely with SSH from either Windows or macOS. The basic setup of IT companies is MacBook with real or virtual Linux box. The desktop is just not friendly enough. For others, it is simply hard to find Linux either user-friendly or productive. e.g. The [LiMux Project](https://www.techrepublic.com/article/linux-to-windows-10-why-did-munich-switch-and-why-does-it-matter/).

For Unix/Linux RICE, I found ArchLinux and i3 are their major choices on operating system, though Ubuntu sometimes be mentioned.

### Arch Linux

When I was an early Linux enthusiastic, I heard about these main distros. e.g. Redhat/Fedora, Debian, SUES, Slackware, Ubuntu, Gentoo, and also Arch.

[Arch Linux](https://www.archlinux.org/) has the best home page (IMO) and wiki of Linux distros in the world. The color scheme of its home page win at its beginning.

![arch-linux-org]({{ "/image/rice-arch-linux-org.png" | absolute_url }})

Arch Linux chooses blue as major color, which is vivid and shows the sense of technology. I kept on copying the color scheme when I was building my own home page. Even though today, I still feel it is a good color scheme.

Let’s head to Arch Linux’s philosophy. I have no idea of these minimal style distros since you can hardly handle shell/terminal from the beginning. But as soon as I get _solid_ skills on that, I appreciate its idea: [^2]

- Simplicity, minimalism, and code elegance
- Installed as a minimal base system, configured by the user upon which their own ideal environment is assembled by installing only what is required or desired for their unique purposes.
- Pacman package manager
- Rolling-release upgrades model

As soon as I read Arch’s philosophy, I know that the second point is the most important reason why it is popular in RICE culture. And Pacman makes it even easier and powerful.

It is somehow like Gentoo Linux but seems to be a lot easier, because the Gentoo packages and base system are built directly from source code but Arch tends to make user build packages quicker.[^3]

And finally, it is like you make your own system and are responsible for yourself. You choose what you need and configure as you like based on what you can do. No surprise, no weird parts, so it is totally in control.

The Arch Linux Wiki is so good that I can easily follow the [Installation guide](https://wiki.archlinux.org/index.php/Installation_guide) and it is done.

It is super easy to install packages with Pacman. Everything is there and Arch also has a repository (ArchLinux User Repository) for user packages. For example, it is like a non-GUI operating system and we need some more to setup a GUI system.

```shell
# Login Manager
sudo pacman -s lightdm lightdm-gtk-greeter
# X Window
sudo pacman -S xorg-server xorg-apps xorg-xinit
```

### i3

i3 is a popular tiling window manager. Tiling window manager? It’s a kind of window manager, where all windows lay on the desktop like tiles or panes, and you just do not move windows by dragging title panel. Instead, it’s controlled by keys. You can easily move around and resize the windows.

i3-gaps is an i3 fork, which is almost the same and adds more features such as gaps between windows, that’s why it is called i3-gaps.

```
sudo pacman -S i3-gaps
```

After installing X Window and i3-gaps, I got an empty desktop while I can open a terminal with `Mod+Enter`.

### Alacritty

To work with tiling window manager, we need a minimal style terminal. It may not provide either multi-tabs support or multi-panes support.

urxvt, kitty, and st are common picks. I tried urxvt, which is actually rxvt-unicode, but I have problems with Asian language displays.

Then I found that [Alacritty](https://github.com/jwilm/alacritty) is the right one for me. It is high-performance, GPU powered and easy configuration.

```
sudo pacman -S alacritty
```

![alacritty]({{ "/image/rice-alacritty.png" | absolute_url }})

### i3wm-themer

For i3 starters, [i3wm-themer](https://github.com/unix121/i3wm-themer) should be mentioned. It is a CLI tool setups simple and minimalistic desktop themes for you. It is best for newcomers to setup a basic theme and have a panoramic and basic concepts on what composite a capable desktop.

i3wm-themer integrates i3-gaps, Polybar, Nitrogen, Rofi, rxvt-unicode, various fonts and others.

__Polybar__ is to help users create awesome status bars. It provides a collection of modules. e.g. Text label, sound volume, Wi-Fi status, date & time, and etc.

__Rofi__ is an application launcher, a replacement of dmenu. dmenu is the default one for X but Rofi seems to be more popular. And there are also Alfred-like application launchers, but for most scenario, Rofi is enough.

__Nitrogen__ is basically a background setter.

And as aforementioned, I replace rxvt-unicode with Alacritty.

Based on i3wm-themer, I have finally done my first Arch Linux RICE. All configuration files of those parts are stored under `$XDG_CONFIG_HOME`.

![screenshot]({{ "/image/rice-screenshot.jpg" | absolute_url }})

### Other Applications

__Ranger__ is a file manager and previewer with terminal interface, and navigation is similar to Vim key bindings.

__vimiv__ may not be a well-known software but it worth a try. There are a variety choices of image viewers and managers. vimiv is a Ranger but for images. It is fast and works with GIF animations. A vim-style image manager is so minimalistic.

__Firefox__ is a well-known browser. It is real free software and does not compromise on privacy, comparing to Google. That’s the reason why I did not choose Chrome or Chromium.

### Obstacles

RICE on Arch Linux and maybe just Linux could be interesting but it also has obstacles. There are common parts, however, every component actually follows its own choice. e.g.:

- Bash or Zsh uses dot rc files
- Most modern softwares use XDG directories
- GUI apperances are configured under `.Xresources` or using GTK (or Qt) themes

And to make things much more difficult, they have their own file specifications.

I have to write Shell Script, JSON, XML, YAML, INI, and other private configuration codes.

For tools like Vim, it’s actually Vim Language and we have to learn a lot to make it powerful together with plugins. Luckily, Vim configuration is another topic that I have already handled before.

That's the cost of freedom. We as users have the freedom to choose everything and software programmers also have.

## Conclusion

I think RICE is the art of building things special for personal use and habits, and with our own tastes and personalities. I know that not everyone is interested or has the passion on RICE. Once I dived into that, I have had a lot of happiness.

My Arch Linux dotfiles: [https://github.com/crispgm/arch-linux-dotfiles](https://github.com/crispgm/arch-linux-dotfiles)

---

[^1]: [Urban Dictionary: rice](https://www.urbandictionary.com/define.php?term=rice&defid=955541)
[^2]: [https://www.archlinux.org/about/](https://www.archlinux.org/about/)
[^3]: [Arch compared to other distributions](https://wiki.archlinux.org/index.php/Arch_compared_to_other_distributions#Gentoo/Funtoo_Linux)
