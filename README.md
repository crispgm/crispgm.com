### Crisp Blog and Generator

#### Introduction

This is my github blog, which uses markdown and php to generate a whole blog site.

#### Usage

> get the source

    git clone https://github.com/crispgm/crispgm.github.io.git

> modify blog.php and add your new blog entry info here

     cd crispgm.github.io/src  
     vim blog.php

> create a markdown file with the name in blog.php array
    
    touch foo-bar.md

> run generator script

    php generator.php

> commit to github

    cd ..  
    git add *  
    git commit -m "yourmessage"  
    git push
