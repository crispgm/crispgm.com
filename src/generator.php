<?php
require_once('blog.php');

class CrispBlogGenerator
{
    private static $head = null;
    private static $foot = null;
    private static $comment = null;

    private static function get_head()
    {
        if (self::$head === null) {
            self::$head = file_get_contents('../static/head.html');
        }

        return self::$head;
    }

    private static function get_foot()
    {
        if (self::$foot === null) {
            self::$foot = file_get_contents('../static/foot.html');
        }

        return self::$foot;
    }

    private static function get_comment()
    {
        if (self::$comment === null) {
            self::$comment = file_get_contents('../static/disqus.html');
        }

        return self::$comment;
    }

    private static function get_recommend()
    {
    }

    private static function gen_blogs()
    {
        $blog = new Blog();
        $all_blogs = $blog->getAllBlogs();
        foreach ($all_blogs as $blog_name => $blog_info) {
            $html = '';
            $html .= self::get_head();
            $html = str_replace('{{title}}', "${blog_info['title']} - Crisp Blog", $html);
            $html .= "\n";

            $title = $blog_info['title'];
            $date = $blog_info['date'];
            $markdown = $blog->getBlogHTML($blog_name);

            $html .= '<div class="article">';
            $html .= '<div class="article_head">';
            $html .= "<div class=\"article_title\">$title</div>\n";
            $html .= "<div class=\"article_date\">$date</div>\n";
            $html .= '</div>';
            $html .= "<div class=\"article_main\">$markdown</div>\n";
            $html .= '</div>';
            $html .= "\n";
            $html .= self::get_comment();
            $html .= "\n";

            $html .= self::get_foot();
            $html .= "\n";
            file_put_contents("../page/{$blog_name}.html", $html);
        }
    }

    private static function gen_pages()
    {
        $blog = new Blog();
        $all_pages = $blog->getAllPages();

        foreach ($all_pages as $page_name => $page_info) {
            $html = '';
            $html .= self::get_head();
            $html = str_replace('{{title}}', "${page_info['title']} - Crisp Blog", $html);
            $html .= "\n";

            $title = $page_info['title'];
            $markdown = $blog->getPageHTML($page_name);

            $html .= '<div class="article">';
            $html .= '<div class="article_head">';
            $html .= "<div class=\"article_title\">$title</div>\n";
            $html .= '</div>';
            $html .= "<div class=\"article_main\">$markdown</div>\n";
            $html .= '</div>';

            $html .= self::get_foot();
            $html .= "\n";
            file_put_contents("../page/{$page_name}.html", $html);
        }
    }

    private static function gen_index()
    {
        $blogs_per_page = 1;
        $blog = new Blog($blogs_per_page);
        $total_blog = $blog->getBlogNum();

        for ($i = 1; $i <= $total_blog; $i++){
            $html = '';
            // head
            $html .= self::get_head();
            $html = str_replace('{{title}}', "Home - Crisp Blog", $html);
            $html .= "\n";

            // blog list
            $page_blogs = $blog->getBlogsByPage($i);
            foreach ($page_blogs as $blog_name => $blog_info) {
                $title = $blog_info['title'];
                $date = $blog_info['date'];
                $markdown = $blog_info['markdown'];
                $html .= "<div class=\"article\">\n";
                $html .= "<div class=\"article_head\">\n";
                $html .= "<div class=\"article_title\"><a href=\"/page/{$blog_name}.html\">$title</a></div>\n";
                $html .= "<div class=\"article_date\">$date</div>\n";
                $html .= "</div>\n";
                $html .= "<div class=\"article_main\">\n";
                $html .= $blog->getBlogHTML($blog_name);
                $html .= "<p><a href=\"/archive.html\">More...</a></p>\n";
                $html .= "</div></div>\n";
            }

            // foot
            $html .= self::get_foot();
            $html .= "\n";
            file_put_contents("../index.html", $html);
            break;
        }
    }

    private static function gen_rss_feed()
    {
        $blog = new Blog();
        $all_blogs = $blog->getAllBlogs();
        $pivot = 0;
        $xml = "<?xml version=\"1.0\"?>\n";
        $xml .= "<rss version=\"2.0\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\"
                    xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\"
                    xmlns:dc=\"http://purl.org/dc/elements/1.1/\"
                    xmlns:atom=\"http://www.w3.org/2005/Atom\"
                    xmlns:sy=\"http://purl.org/rss/1.0/modules/syndication\"
                    xmlns:slash=\"http://purl.org/rss/1.0/modules/slash/\">\n";
        $xml .= "<channel>\n";
        $xml .= "<title>Crisp Blog</title>\n";
        $xml .= "<link>http://crispgm.com</link>\n";
        $xml .= "<description>Crisp 个人博客</description>\n";
        $xml .= "<atom:link href=\"http://crispgm.com/rss.xml\" rel=\"self\" type=\"application/rss+xml\"/>";
        $xml .= "<author>Crisp</author>";
        $xml .= "<language>zh-cn</language>\n";
        $buildDate = date('r');
        $xml .= "<lastBuildDate>{$buildDate}</lastBuildDate>\n";
        foreach ($all_blogs as $blog_name => $blog_info)
        {
            $title    = $blog_info['title'];
            $date     = $blog_info['date'];
            $markdown = $blog_info['markdown'];
            $url      = "http://crispgm.com/page/{$blog_name}.html";
            $html     = $blog->getBlogHTML($blog_name);
            $html     = str_replace("\n", "", $html);
            $pubDate  = date('r', strtotime($date));
            $xml .= "<item>\n";
            $xml .= "<title><![CDATA[$title]]></title>\n";
            $xml .= "<link>$url</link>\n";
            $xml .= "<guid isPermaLink=\"true\">$url</guid>\n";
            //$xml .= "<description></description>\n";
            $xml .= "<pubDate>$pubDate</pubDate>\n";
            $xml .= "<content:encoded><![CDATA[$html]]></content:encoded>\n";
            $xml .= "</item>\n";
            if ($pivot++ === 20)
            {
                break;
            }
        }
        $xml .= "</channel>\n";
        $xml .= "</rss>\n";

        file_put_contents('../rss.xml', $xml);
    }

    private static function gen_archive()
    {
        $page_num = 1;
        $blogs_per_page = 6;
        $blog = new Blog($blogs_per_page);
        $blogs = $blog->getAllBlogs();

        $html = self::get_head();
        $html = str_replace('{{title}}', "Archive - Crisp Blog", $html);
        $html .= "\n";
        $html .= "<div class=\"article\">\n";
        $html .= "<div class=\"article_head\">\n";
        $html .= "<div class=\"article_title\">Blog Archive</div>\n";
        $html .= "<div class=\"article_date\"></div>\n";
        $html .= "</div>\n";
        $html .= "<div class=\"archive_main\">\n";
        foreach ($blogs as $blog_name => $blog_info) {
            $title = $blog_info['title'];
            $date = $blog_info['date'];
            $html .= "<div class=\"archive_item\">\n";
            $html .= "<span class=\"archive_date\">$date</span><span class=\"archive_title\"><a href=\"/page/{$blog_name}.html\">$title</a></span>\n";
            $html .= "</div>\n";
        }
        $html .= "</div>\n";
        $html .= "<div id=\"archive_subscribe\"><a href=\"/rss.xml\" target=\"_blank\">Subscribe Blog Updates</a></div>\n";
        $html .= "</div>\n";
        $html .= self::get_foot();
        $html .= "\n";
        file_put_contents('../archive.html', $html);
    }

    public static function generate()
    {
        self::gen_index();
        self::gen_blogs();
        self::gen_pages();
        self::gen_archive();
        self::gen_rss_feed();
    }
}

CrispBlogGenerator::generate();
