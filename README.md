### Crisp Blog and Generator

#### Introduction

This is my github blog, which uses markdown and php to generate a whole blog site.

#### Usage

> get the source

    git clone https://github.com/crispgm/crispgm.github.io.git

> modify blog.php and add your new blog entry info here

     cd crispgm.github.io  
     vim src/blog.php

     protected $blogs = array(  
        array(  
            'title'    => 'foobar',  
            'markdown' => 'foo-bar.md',  
            'date'     => '2014-03-08',  
        ),  
     );

> create a markdown file with the name in blog.php array
    
    vim doc/foo-bar.md

add the content and save the file.

> make

    sh make.sh

> commit to github

    git add *  
    git commit -m "yourmessage"  
    git push
