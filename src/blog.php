<?php

include('Parsedown.php');

class Blog{
    protected $blog_num_per_page;

    protected $blogs = array(
        'period-tracker-app' => array(
            'title'    => 'Minimalism Period Tracker',
            'markdown' => 'period-tracker-app.md',
            'date'     => '2015/03/27',
            'tags'     => array('app', 'iOS'),
        ),
        'my-favorite-app' => array(
            'title'    => '我喜欢的应用',
            'markdown' => 'my-favorite-app.md',
            'date'     => '2014/10/26',
            'tags'     => array('app'),
        ),
        'mac-screenshot-path' => array(
            'title'    => 'Mac截图修改保存路径',
            'markdown' => 'mac-screenshot-path.md',
            'date'     => '2014/09/20',
            'tags'     => array('Mac'),
        ),
        'mysql-update-multirows' => array(
            'title'    => 'MySQL更新多条记录的不同值',
            'markdown' => 'mysql-update-multirows.md',
            'date'     => '2014/09/12',
            'tags'     => array('MySQL'),
        ),
        'php-casting-vs-intval' => array(
            'title'    => '[翻译]PHP: int(强制转换) vs. intval()',
            'markdown' => 'php-casting-vs-intval.md',
            'date'     => '2014/09/03',
            'tags'     => array('PHP'),
        ),
        'moon-and-stars' => array(
            'title'    => '[转]看月亮和看星星',
            'markdown' => 'moon-and-stars.md',
            'date'     => '2014/08/17',
            'tags'     => array('短文'),
        ),
        'mac-dns-flush' => array(
            'title'    => 'Mac OSX 10.9 Mavericks清除DNS缓存',
            'markdown' => 'mac-dns-flush.md',
            'date'     => '2014/05/03',
            'tags'     => array('Mac', 'DNS'),
        ),
        'vim-youcompleteme' => array(
            'title'    => 'YouCompleteMe Installation Guide on Mac',
            'markdown' => 'vim-ycm.md',
            'date'     => '2014/04/20',
            'tags'     => array('vim', 'Mac'),
        ),
        'fiddler-proxy' => array(
            'title'    => 'Fiddler网络调试工具',
            'markdown' => 'fiddler-proxy.md',
            'date'     => '2014/03/21',
            'tags'     => array('fiddler', 'debugging'),
        ),
        'mac-sound' => array(
            'title'    => 'Macbook合盖子或者休眠后没声音',
            'markdown' => 'mac-sound.md',
            'date'     => '2014/03/08',
            'tags'     => array('Mac'),
        ),
        'mac-mail' => array(
            'title'    => 'Mac Mail无法退出',
            'markdown' => 'mac-mail.md',
            'date'     => '2014/01/10',
            'tags'     => array('Mac'),
        ),
        'shell-tips' => array(
            'title'    => 'shell两个小问题解决方法',
            'markdown' => 'shell-tips.md',
            'date'     => '2013/12/30',
            'tags'     => array('Shell'),
        ),
        'inspiring-songs' => array(
            'title'    => 'Land of Hope and Glory',
            'markdown' => 'inspiring-songs.md',
            'date'     => '2013/11/25',
            'tags'     => array('Song'),
        ),
        'guangzhou-fc' => array(
            'title'    => '恒大夺冠',
            'markdown' => 'guangzhou-fc.md',
            'date'     => '2013/11/09',
            'tags'     => array('Soccer', '广州恒大'),
        ),
        'mac-tips' => array(
            'title'    => 'mac终端小技巧合集',
            'markdown' => 'mac-tips.md',
            'date'     => '2013/11/03',
            'tags'     => array('Mac', 'Terminal'),
        ),
        'service-caller' => array(
            'title'    => '自制chrome调用贴吧service插件正式发布',
            'markdown' => 'service-caller.md',
            'date'     => '2013/11/01',
            'tags'     => array('Chrome'),
        ),
        'awk-scripts' => array(
            'title'    => 'awk日志统计脚本',
            'markdown' => 'awk-scripts.md',
            'date'     => '2013/07/20',
            'tags'     => array('awk'),
        ),
        // 'my-teachers' => array(
        //     'title'    => '回忆一下教过我的老师',
        //     'markdown' => 'my-teachers.md',
        //     'date'     => '2010/06/05',
        //     'tags'     => array('短文'),
        // ),
        // 'our-duty' => array(
        //     'title'    => '我们的使命',
        //     'markdown' => 'our-duty.md',
        //     'date'     => '2008/05/18',
        //     'tags'     => array('短文'),
        // ),
        // 'funny-chat' => array(
        //     'title'    => '很奇葩',
        //     'markdown' => 'funny-chat.md',
        //     'date'     => '2009/07/28',
        //     'tags'     => array('短文'),
        // ),
        // 'funny-physics' => array(
        //     'title'    => '物理题真是有意思~~',
        //     'markdown' => 'funny-physics.md',
        //     'date'     => '2006/05/14',
        //     'tags'     => array('短文'),
        // ),
    );

    protected $pages = array(
        // pages
        'about' => array(
            'title'    => 'About',
            'markdown' => 'about.md',
        ),
        'resume' => array(
            'title'    => 'My Resume',
            'markdown' => 'resume.md',
        ),
        '404' => array(
            'title'    => 'Page Not Found',
            'markdown' => '404.md',
        ),
    );

    protected $_parsedown = null;

    public function __construct($blog_num_per_page = 10)
    {
        $this->blog_num_per_page = $blog_num_per_page > 0 ? $blog_num_per_page : 10;
        $this->_parsedown = new Parsedown();
    }

    public function getAllBlogs()
    {
        return $this->blogs;
    }

    public function getAllPages()
    {
        return $this->pages;
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
        $md_path = "../doc/$md_file";
        if ( !is_file($md_path) )
            return false;

        $markdown = file_get_contents($md_path);
        return $this->_parsedown->text($markdown);
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
        $md_path = "../doc/$md_file";
        if ( !is_file($md_path) )
            return false;

        $markdown = file_get_contents($md_path);
        return $this->_parsedown->text($markdown);
    }
}
