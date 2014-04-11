### Crisp Blog and Generator

#### Introduction

This is my github blog, which uses markdown and php to generate a static blog site.

#### Dependencies

* php 5.4 or later
* php-markdown 1.4(already in the project)

#### Usage

> Get the code

    git clone https://github.com/crispgm/crispgm.github.io.git

> Modify blog.php

    cd crispgm.github.io  
    vim src/blog.php

> Add your new blog entry's information here

    protected $blogs = array(  
        array(  
            'title'    => 'foobar',  
            'markdown' => 'foo-bar.md',  
            'date'     => '2014-03-08',  
        ),  
    );

> Create a markdown file with the name your specified
    
    vim doc/foo-bar.md

> Add the content and save the file

    \#\#\# hello, markdown

> Make

    sh make.sh

> Commit to github

    git add *  
    git commit -m "yourmessage"  
    git push

