# coding: utf-8
require "rake/testtask"
require "listen"


task default: %w[test]

Rake::TestTask.new do |t|
  t.libs << "test"
  t.test_files = FileList["test/test_*.rb"]
  t.verbose = true
end

desc "Init"
task :init do
  Dir.chdir("site") do
    sh "bundle install"
  end
  Dir.chdir("resume") do
    sh "bundle install"
  end
end

namespace :site do
  desc "Serve site"
  task :serve do
    Dir.chdir("site") do
      sh "bundle exec jekyll serve --draft --future --livereload"
    end
  end

  desc "Lint"
  task :lint do
    sh "bundle exec scss-lint site/_sass"
  end

  desc "Build jekyll and push to gh-pages branch"
  task :build do
    # Update master branch
    sh "git checkout master"
    sh "git pull --rebase"
    # Clone to gh-pages
    unless File.exist?("gh-pages")
      sh "git clone --depth=1 -b gh-pages https://github.com/crispgm/crispgm.com.git gh-pages"
    end
    # Checkout and pull
    Dir.chdir("gh-pages") do
      sh "git checkout gh-pages"
      sh "git reset --hard"
      sh "git pull origin gh-pages"
    end
    # Build site
    Dir.chdir("site") do
      sh "bundle exec jekyll build --destination=../gh-pages/"
    end
    # Build resume
    Dir.chdir("resume") do
      sh "bundle exec jekyll build --destination=../gh-pages/resume/ --trace"
    end
    # Build wedding invitation
    # unless File.exists?("wedding-invitation")
    #   sh "git clone https://github.com/crispgm/wedding-invitation.git"
    # end
    # Dir.chdir("wedding-invitation") do
    #   sh "git reset --hard"
    #   sh "git pull --rebase"
    #   sh "bundle exec jekyll build --destination=../gh-pages/wedding-invitation/"
    # end
    # Push
    Dir.chdir("gh-pages") do
      sh "git config --global user.name 'crispbot'"
      sh "git config --global user.email 'crispgm@users.noreply.github.com'"
      sh "git add ."
      sh "git commit --allow-empty -m \"Deployed at #{Time.now}\""
      GITHUB_TOKEN = ENV["GITHUB_TOKEN"]
      sh "git remote set-url --push origin https://crispgm:#{GITHUB_TOKEN}@github.com/crispgm/crispgm.com"
      sh "git push origin gh-pages"
    end
  end

  desc "Evaluate views on different devices"
  task :evaluate do
    Dir.chdir("integrated-test") do
      sh "mkdir -p screenshots"
      sh "npm run evaluate"
      sh "open ./screenshots"
    end
  end
end

namespace :resume do
  desc "Serve resume"
  task :serve do
    Dir.chdir("resume") do
      sh "bundle exec jekyll serve"
    end
  end
end
