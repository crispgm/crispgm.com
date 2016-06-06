all:
	jekyll build
	cp -r _site/* ../
