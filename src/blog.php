<?php

require_once('Michelf/Markdown.php');

use \Michelf\Markdown;

class Blog{
	protected $blog_num_per_page;
	
	protected $blogs = array(
		'mac-mail' => array(
			'title'    => 'Mac Mail无法退出',
			'markdown' => 'mac-mail.md',
			'date'     => '2014/01/10',
		),
		'shell-tips' => array(
			'title'    => 'shell两个小问题解决方法',
			'markdown' => 'shell-tips.md',
			'date'     => '2013/12/30',
		),
		'inspiring-songs' => array(
			'title'    => 'Land of Hope and Glory',
			'markdown' => 'inspiring-songs.md',
			'date'     => '2013/11/25',
		),
		'guangzhou-fc' => array(
			'title'    => '恒大夺冠',
			'markdown' => 'guangzhou-fc.md',
			'date'     => '2013/11/09',
		),
		'mac-tips' => array(
			'title'    => 'mac终端小技巧合集',
			'markdown' => 'mac-tips.md',
			'date'     => '2013/11/03',
		),
		'service-caller' => array(
			'title'    => '自制chrome调用贴吧service插件正式发布',
			'markdown' => 'service-caller.md',
			'date'     => '2013/11/01',
		),
		'awk-scripts' => array(
			'title' => 'AWK日志统计脚本',
			'markdown' => 'awk-scripts.md',
			'date' => '2013/07/20',
		),
		'my-teachers' => array(
			'title'    => '回忆一下教过我的老师',
			'markdown' => 'my-teachers.md',
			'date'     => '2010/06/05',
		),
		'our-duty' => array(
			'title'    => '我们的使命',
			'markdown' => 'our-duty.md',
			'date'     => '2008/05/18',
		),
		'touching-words' => array(
			'title'    => '一句感人的话 ',
			'markdown' => 'touching-words.md',
			'date'     => '2007/07/01',
		),
		'funny-chat' => array(
			'title'    => '很奇葩',
			'markdown' => 'funny-chat.md',
			'date'     => '2009/07/28',
		),
		'funny-physics' => array(
			'title'    => '物理题真是有意思~~',
			'markdown' => 'funny-physics.md',
			'date'     => '2006/05/14',
		),
	);
	
	protected $pages = array(
		// pages
		'about' => array(
			'title'    => 'About',
			'markdown' => 'about.md',
		),
		'profile' => array(
			'title'    => 'My Profile',
			'markdown' => 'my-profile.md',
		),
		'project' => array(
			'title'    => 'My Project',
			'markdown' => 'my-projects.md',
		),
	);
	
	public function __construct($blog_num_per_page = 10)
	{
		$this->blog_num_per_page = $blog_num_per_page > 0 ? $blog_num_per_page : 10;
	}
	
	public function getAllBlogs()
	{
		return $this->blogs;
	}
	
	public function getBlogNum()
	{
		$blog_count = 0;
		foreach ( $this->blogs as $blog_info )
			$blog_count++;
		return $blog_count;
	}
	
	public function getBlogsByPage($page = 1)
	{
		$blog_start = ($page - 1) * $this->blog_num_per_page;
		$cur_blog_num = 0;
		$pivot = 0;
		$page_blogs = array();
		foreach ( $this->blogs as $blog_name => $blog_info ){
			if ( $pivot === $blog_start ){
				$page_blogs[$blog_name] = $blog_info;
				$cur_blog_num++;
			}
			else{
				$pivot++;
			}
			if ( $cur_blog_num === $this->blog_num_per_page ){
				break;
			}
		}
		return $page_blogs;
	}
	
	public function getBlogInfo($blog_name)
	{
		if ( !isset($this->blogs[$blog_name]) )
			return false;
		return $this->blogs[$blog_name];
	}
	
	public function getBlogHTML($blog_name)
	{
		if ( !($blog_info = $this->getBlogInfo($blog_name)) )
			return false;
		
		$md_file = $blog_info['markdown'];
		$md_path = "doc/$md_file";
		if ( !is_file($md_path) )
			return false;

		$markdown = file_get_contents($md_path);
		return Markdown::defaultTransform($markdown);
	}

	public function getPageInfo($page_name)
	{
		if ( !isset($this->pages[$page_name]) )
			return false;
		return $this->pages[$page_name];
	}

	public function getPageHTML($page_name)
	{
		if ( !($page_info = $this->getPageInfo($page_name)) )
			return false;
		
		$md_file = $page_info['markdown'];
		$md_path = "doc/$md_file";
		if ( !is_file($md_path) )
			return false;

		$markdown = file_get_contents($md_path);
		return Markdown::defaultTransform($markdown);
	}
}
