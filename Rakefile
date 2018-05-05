# coding: utf-8
task default: %w[test]

require "rake/testtask"
require "listen"
require "net/http"
require "uri"
require "json"

def post_to_slack(msg)
  token = ENV["SLACK_WEBHOOK_URL"]
  return if token.nil?
  uri = URI.parse(token)
  request = Net::HTTP::Post.new(uri)
  request.content_type = "application/json"
  request.body = JSON.dump({
    "text" => msg
  })

  req_options = {
    use_ssl: uri.scheme == "https",
  }

  response = Net::HTTP.start(uri.hostname, uri.port, req_options) do |http|
    http.request(request)
  end
end

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
    sh "git submodule update --init --recursive"
  end
end

desc "Ping Slack"
task :slack do
  post_to_slack("Hello, *Slack*!")
end

namespace :site do
  desc "Serve site"
  task :serve do
    Dir.chdir("site") do
      sh "bundle exec jekyll serve --draft"
    end
  end

  desc "Lint"
  task :lint do
    sh "bundle exec scss-lint site/_sass"
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
      sh "bundle exec jekyll build --destination=../gh-pages/"
    end
    # Build resume
    Dir.chdir("resume") do
      Dir.chdir("resume") do
        sh "git stash"
        sh "git pull origin master"
        sh "cp ../data/resume*.yml _data"
        sh "cp ../_config.yml ."
        sh "jekyll build --destination=../../gh-pages/resume/"
        sh "git stash"
        sh "git clean -f"
      end
    end
    # Build wiki
    Dir.chdir("wiki") do
      Dir.chdir("wiki") do
        sh "git stash"
        sh "git pull origin master"
        sh "cp ../data/wiki.yml _data"
        sh "cp ../css/custom.css css"
        sh "cp ../_config.yml ../home/index.html ."
        sh "jekyll build --destination=../../gh-pages/wiki/"
        sh "git stash"
        sh "git clean -f"
      end
    end
    # Push
    Dir.chdir("gh-pages") do
      sh "git add ."
      sh "git commit --allow-empty -m \"Deployed at #{Time.now}\""
      sh "git push origin gh-pages"
    end

    post_to_slack("Ship a new build to *crispgm.com*")
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
      sh "cp data/resume*.yml resume/_data"
      sh "cp _config.yml resume"

      listener = Listen.to("./data") do |modified, added, removed|
        changes = []
        unless modified.empty?
          puts "Changes detected on #{modified}"
          changes += modified
        end
        unless added.empty?
          puts "Changes detected to #{added}"
          changes += added
        end
        unless removed.empty?
          puts "Changes detected to #{removed}"
          changes += removed
        end

        changes.each do |file_name|
          sh "cp ../data/resume*.yml _data"
        end
      end
      listener.start

      Dir.chdir("resume") do
        sh "jekyll serve"
      end
    end
  end

  desc "Update resume version"
  task :update do
    Dir.chdir("resume") do
      Dir.chdir("resume") do
        sh "git pull origin master"
      end

      sh "git add resume/"
      sh "git commit --allow-empty -m \"Bump crispgm/resume at #{Time.now.to_i} [ci skip]\""
      sh "git push"
    end
  end
end

namespace :wiki do
  desc "Serve wiki"
  task :serve do
    Dir.chdir("wiki") do
      sh "cp data/wiki.yml wiki/_data"
      sh "cp css/custom.css wiki/css"
      sh "cp _config.yml home/index.html wiki"

      listener = Listen.to("./data", "./css", "./home") do |modified, added, removed|
        changes = []
        unless modified.empty?
          puts "Changes detected on #{modified}"
          changes += modified
        end
        unless added.empty?
          puts "Changes detected to #{added}"
          changes += added
        end
        unless removed.empty?
          puts "Changes detected to #{removed}"
          changes += removed
        end

        changes.each do |file_name|
          sh "cp ../data/wiki.yml ../css/custom.css ../home/index.html _data"
        end
      end
      listener.start

      Dir.chdir("wiki") do
        sh "jekyll serve"
      end
    end
  end

  desc "Update wiki version"
  task :update do
    Dir.chdir("wiki") do
      Dir.chdir("wiki") do
        sh "git pull origin master"
      end

      sh "git add wiki/"
      sh "git commit --allow-empty -m \"Bump crispgm/wiki at #{Time.now.to_i} [ci skip]\""
      sh "git push"
    end
  end
end
