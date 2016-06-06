#
# Copyright (c) David Zhang, 2016
# MIT License
#

module Jekyll

  class TagsList < Liquid::Tag

    def initialize(tag_name, text, tokens)
      super
      @text = text
      @threshold = 1
      # time, count
      # asc, desc
      @order_by = 0
      @sort_by = 0
      @tag_link = ''
      @show_count = true
    end

    def render(context)
      tags = context.registers[:site].tags.map do |tag, posts|
        [tag, posts.count] if posts.count >= @threshold
      end

      html = ""

      tags.each do |tag, count|
        count_html = "<div class=\"tag_item_count\">#{count}</div>" if @show_count
        html << "<div class=\"tag_item\"><div class=\"tag_item_name\">#{tag}</div>#{count_html}</div>\n"
      end

      html
    end
  end

end

Liquid::Template.register_tag('tags_list', Jekyll::TagsList)
