# Crisp Wiki Theme for Jekyll

![](https://img.shields.io/badge/license-MIT-blue.svg)
![](https://img.shields.io/badge/powered%20by-jekyll-red.svg)

A simple responsive wiki theme for Jekyll from Crisp.

I use it on my website <https://crispgm.com/wiki/>.

## Installation

### Manual

1. Clone the project.

    ```
    git clone https://github.com/crispgm/wiki.git
    ```

2. Modify `_data/wiki.yml` to add your QAs.

    ```yaml
    - q: What is Jekyll?
      a: It is a static site generator.
    ```

3. Run, debug & build with `jekyll`.

    ```
    # Run
    jekyll serve
    # Build
    jekyll build
    ```

### Gem-based theme (WIP, NOT LAUNCH YET)

1. To install a theme, first, add the theme to your site's `Gemfile`:

    ```
    gem 'jekyll-crisp-wiki-theme'
    ```

2. Save the changes to your `Gemfile`
3. Run the command `bundle install` to install the theme
4. Finally, activate the theme by adding the following to your site's `_config.yml`:

    ```
    theme: jekyll-crisp-wiki-theme
    ```

For more information, please read [https://jekyllrb.com/docs/themes/](https://jekyllrb.com/docs/themes/).

## Sample

[[Sample Site](https://crisp.lol/wiki/)][[Screenshot](/screenshots/screenshot.jpg)]
