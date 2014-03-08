### Crisp Blog and Generator

#### Introduction

This is my github blog, which uses markdown and php to generate a whole blog site.

#### Usage

> Get the code

    git clone https://github.com/crispgm/crispgm.github.io.git

> Modify blog.php

     cd crispgm.github.io  
     vim src/blog.php

> And then add your new blog entry info here

     protected $blogs = array(  
        array(  
            'title'    => 'foobar',  
            'markdown' => 'foo-bar.md',  
            'date'     => '2014-03-08',  
        ),  
     );

> Create a markdown file with the name in blog.php array
    
    vim doc/foo-bar.md

> And add the content and save the file.

> Make

    sh make.sh

> Commit to github

    git add *  
    git commit -m "yourmessage"  
    git push
