# !/bin/bash

rm -rf *.html *.xml page

mkdir page

cd src

php generator.php

echo "Build Success."

