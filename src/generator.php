<?php
require_once('blog.php');

class Generator{

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

	private static function gen_index()
	{
		$buffer = '';
		$buffer .= self::get_head();
		$buffer .= "\n";
		$buffer .= self::get_foot();
		$buffer .= "\n";

		file_put_contents('../index.html', $buffer);

	}

	public static function generate()
	{
		self::gen_index();
	}
}

Generator::generate();