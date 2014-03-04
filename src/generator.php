<?php
require_once('blog.php');

class Generator
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

    private static function gen_blogs()
    {
    	$blog = new Blog($blogs_per_page);
    	$all_blogs = $blog->getAllBlogs();
    	foreach ($all_blogs as $blog_name => $blog_info) {
    		$html = '';
    		$html .= self::get_head();
            $html .= "\n";

            $title = $blog_info['title'];
			$type = $blog_info['type'];
			$date = $blog_info['date'];
			$markdown = $blog->getBlogHTML($blog_name);

			$html .= '<div class="article">';
			$html .= '<div class="article_head">';
			$html .= "<div class=\"article_title\">$title</div>\n";
			$html .= "<div class=\"article_date\">$date</div>\n";
			$html .= '</div>';
			$html .= "<div class=\"article_main\">$markdown</div>\n";
			$html .= '</div>';

            $html .= self::get_comment();
            $html .= "\n";

            $html .= self::get_foot();
            $html .= "\n";
            file_put_contents("../page/{$blog_name}.html", $html);
    	}
    }

    private static function gen_pages()
    {
    	$blog = new Blog($blogs_per_page);
    	$all_pages = $blog->getAllPages();
    	foreach ($all_pages as $blog_name => $blog_info) {
    		$html = '';
    		$html .= self::get_head();
            $html .= "\n";

            $title = $blog_info['title'];
			$type = $blog_info['type'];
			$date = $blog_info['date'];
			$markdown = $blog->getBlogHTML($blog_name);

			$html .= '<div class="article">';
			$html .= '<div class="article_head">';
			$html .= "<div class=\"article_title\">$title</div>\n";
			$html .= '</div>';
			$html .= "<div class=\"article_main\">$markdown</div>\n";
			$html .= '</div>';

            $html .= self::get_foot();
            $html .= "\n";
            file_put_contents("../page/{$blog_name}.html", $html);
    	}
    }

    private static function gen_index()
    {
        $page_num = 1;
        $blogs_per_page = 6;
        $blog = new Blog($blogs_per_page);
        $total_blog = $blog->getBlogNum();
        $total_page = intval(ceil($total_blog/$blogs_per_page));

        for ($i = $page_num; $i <= $total_page; $i++){
            $html = '';
            // head
            $html .= self::get_head();
            $html .= "\n";

            // blog list
            $page_blogs = $blog->getBlogsByPage($i);
            foreach ($page_blogs as $blog_name => $blog_info) {
                $title = $blog_info['title'];
                $date = $blog_info['date'];
                $markdown = $blog_info['markdown'];
                $html .= "<div class=\"article\">\n";
                $html .= "<div class=\"article_head\">\n";
                $html .= "<div class=\"article_title\"><a href=\"http://crispgm.github.io/page/{$blog_name}.html\">$title</a></div>\n";
                $html .= "<div class=\"article_date\">$date</div>\n";
                $html .= "</div>\n";
                $html .= "<div class=\"article_main\">\n";
                $html .= $blog->getBlogHTML($blog_name);
                $html .= "</div></div>\n";
            }

            // page
            $np = $i + 1;
            $pp = $i - 1;
            if ($pp >= 1 || $np<=$total_page) {
                $html .= "<nav id=\"page_num\">";
                if ($pp >= 1) {
                    if ($pp === 1) {
                        $html .= "<a href=\"http://crispgm.github.io/index.html\" id=\"prev\">Prev</a>";
                    }
                    else{
                        $html .= "<a href=\"http://crispgm.github.io/index-{$pp}.html\" id=\"prev\">Prev</a>";
                    }
                }
                if ($np <= $total_page) {
                    $html .= "<a href=\"http://crispgm.github.io/index-{$np}.html\" id=\"next\">Next</a>";
                }
                $html .= "</nav>";
            }

            // foot
            $html .= self::get_foot();
            $html .= "\n";
            if ($i === 1) {
                file_put_contents("../index.html", $html);
            }
            else{
                file_put_contents("../index-{$i}.html", $html);
            }
        }
    }

    public static function generate()
    {
        self::gen_index();
        self::gen_blogs();
        self::gen_pages();
    }

}

Generator::generate();