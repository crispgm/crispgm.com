require "helper"

class TestResume < Minitest::Test
  context "open crispgm.com/resume/" do
    setup do
      @resp = URI.open("https://crispgm.com/resume/")
    end

    should "status equal to 200" do
      assert_equal ["200", "OK"], @resp.status
    end

    should "body be larger than 0" do
      assert_operator 0, :<, @resp.read.length
    end

    should "content type be text/html" do
      assert_equal "text/html; charset=UTF-8", @resp.meta["content-type"]
    end
  end
end
