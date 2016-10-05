# coding: utf-8
task default: %w[site]

desc "Init"
task :init do
  Dir.chdir("site") do
    sh "bundle install"
  end
  Dir.chdir("resume") do
    sh "git submodule update --init --recursive"
  end
end

desc "Serve site"
task :site do
  Dir.chdir("site") do
    sh "bundle exec jekyll serve"
  end
end

desc "Serve site with draft"
task :draft do
  Dir.chdir("site") do
    sh "bundle exec jekyll serve --draft"
  end
end

desc "Serve resume"
task :resume do
  Dir.chdir("resume") do
    sh "cp data/resume.yml resume/_data"
    sh "cp _config.yml resume"
    Dir.chdir("resume") do
      sh "jekyll serve"
    end
  end
end

desc "Build jekyll and push to gh-pages branch"
task :build do
  # Clone to gh-pages
  unless File.exist?("gh-pages")
    sh "git clone -b gh-pages https://github.com/crispgm/crispgm.com.git gh-pages"
  end
  # Checkout and pull
  Dir.chdir("gh-pages") do
    sh "git checkout gh-pages"
    sh "git pull origin gh-pages"
  end
  # Build site
  Dir.chdir("site") do
    sh "bundle exec jekyll build --destination=../gh-pages"
  end
  # Build resume
  Dir.chdir("resume") do
    Dir.chdir("resume") do
      sh "git stash"
      sh "git pull origin master"
      sh "cp ../data/resume.yml _data"
      sh "cp ../_config.yml ."
      sh "jekyll build --destination=../../gh-pages/resume/"
      sh "git stash"
    end
  end
  # Build others
    # TODO
  # Push
  Dir.chdir("gh-pages") do
    sh "git add ."
    sh "git commit --allow-empty -m \"Deployed at #{Time.now}\""
    sh "git push origin gh-pages"
  end
end