<?php
/*
 * @Date         : 2022-03-02 14:49:25
 * @LastEditors  : Jack Zhou <jack@ks-it.co>
 * @LastEditTime : 2022-03-02 17:22:16
 * @Description  : 
 * @FilePath     : /recruitment-php-code-test/tests/App/DemoTest.php
 */

namespace Test\App;

use App\App\Demo;
use App\Service\AppLogger;
use App\Util\HttpRequest;
use PHPUnit\Framework\TestCase;


class DemoTest extends TestCase
{

    public function test_foo()
    {
        $req = $this->createStub(HttpRequest::class);

        $demo = new Demo(new \StdClass, $req);

        $this->assertEquals('bar', $demo->foo());
    }

    public function test_get_user_info()
    {
        $req = $this->createMock(HttpRequest::class);

        $req->method('get')
            ->with('http://some-api.com/user_info')
            ->willReturn('{"error":0,"data":{"id": 1, "username": "hello world"}}');

        $demo = new Demo(new \StdClass, $req);

        $this->assertEquals([
            'id' => 1,
            'username' => "hello world",
        ], $demo->get_user_info());
    }

    public function test_get_user_info_fetch_error()
    {
        $req = $this->createMock(HttpRequest::class);

        $req->method('get')
            ->with('http://some-api.com/user_info')
            ->willReturn('{"error":404}');

        $logger = $this->createMock(AppLogger::class);

        $logger->expects($this->once())
            ->method('error')
            ->with("fetch data error.");

        $demo = new Demo($logger, $req);

        $res = $demo->get_user_info();

        $this->assertNull($res);
    }

    public function test_set_req()
    {
        $req = $this->createStub(HttpRequest::class);
        $req2 = $this->createStub(HttpRequest::class);

        $demo = new Demo(new \StdClass, $req);
        $demo->set_req($req2);

        $ref = new \ReflectionClass($demo);
        $prop = $ref->getProperty('_req');
        $prop->setAccessible(true);

        $this->assertSame($req2, $prop->getValue($demo));
    }
}