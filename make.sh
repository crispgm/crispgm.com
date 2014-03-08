cd src
php generator.php

echo "Build Success."

cd ..

git add -A

echo "git add"

git commit -m "rebuild"

echo "git commit"

git push

echo "push"
