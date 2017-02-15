<?php
declare(strict_types = 1);

/**
 * Tests for the \Maleficarum\Request\Request class.
 */

namespace Maleficarum\Request\Tests;

class RequestTest extends \Maleficarum\Tests\TestCase
{
    /* ------------------------------------ Setup methods START ---------------------------------------- */
    protected function tearDown() {
        parent::tearDown();

        unset($_SERVER['HTTP_CONTENT_TYPE']);
    }
    /* ------------------------------------ Setup methods END ------------------------------------------ */

    /* ------------------------------------ Method: __construct START ---------------------------------- */
    /**
     * @expectedException \Maleficarum\Exception\UnsupportedMediaTypeException
     */
    public function testConstructInvalidContentType() {
        $phalconRequest = $this
            ->getMockBuilder('Phalcon\Http\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $_SERVER['HTTP_CONTENT_TYPE'] = 'application/xml';
        new \Maleficarum\Request\Request($phalconRequest, 'JsonParser');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructInvalidDefaultParser() {
        $phalconRequest = $this
            ->getMockBuilder('Phalcon\Http\Request')
            ->disableOriginalConstructor()
            ->getMock();

        new \Maleficarum\Request\Request($phalconRequest, 'foo');
    }

    /**
     * @dataProvider contentTypeDataProvider
     */
    public function testConstructContentTypeCorrect($contentType, $expected) {
        $phalconRequest = $this
            ->getMockBuilder('Phalcon\Http\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $_SERVER['HTTP_CONTENT_TYPE'] = $contentType;
        $request = new \Maleficarum\Request\Request($phalconRequest, 'JsonParser');
        unset($_SERVER['HTTP_CONTENT_TYPE']);

        $data = $this->getProperty($request, 'data');

        $this->assertSame($expected, $data);
    }

    public function contentTypeDataProvider() {
        return [
            ['application/json', ['url' => [], 'POST' => ['foo' => 'bar'], 'GET' => ['bar' => 'baz']]],
            ['application/x-www-form-urlencoded', ['url' => [], 'POST' => ['bar' => 'baz'], 'GET' => ['baz' => 'qux']]],
        ];
    }

    /**
     * @dataProvider defaultParserDataProvider
     */
    public function testConstructDefaultParser($defaultParser, $expected) {
        $phalconRequest = $this
            ->getMockBuilder('Phalcon\Http\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $request = new \Maleficarum\Request\Request($phalconRequest, $defaultParser);
        $data = $this->getProperty($request, 'data');

        $this->assertSame($expected, $data);
    }

    public function defaultParserDataProvider() {
        return [
            ['JsonParser', ['url' => [], 'POST' => ['foo' => 'bar'], 'GET' => ['bar' => 'baz']]],
            ['UrlParser', ['url' => [], 'POST' => ['bar' => 'baz'], 'GET' => ['baz' => 'qux']]]
        ];
    }
    /* ------------------------------------ Method: __construct END ------------------------------------ */

    /* ------------------------------------ Method: __get START ---------------------------------------- */
    /**
     * @dataProvider getDataProvider
     */
    public function testGet($data, $expected) {
        $phalconRequest = $this
            ->getMockBuilder('Phalcon\Http\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $request = new \Maleficarum\Request\Request($phalconRequest, 'JsonParser');
        $this->setProperty($request, 'data', $data, $expected);

        $this->assertSame($expected, $request->foo);
    }

    public function getDataProvider() {
        return [
            [['GET' => ['foo' => 'foo'], 'POST' => ['foo' => 'bar'], 'url' => ['foo' => 'baz']], 'baz'],
            [['GET' => ['foo' => 'foo'], 'POST' => ['foo' => 'bar'], 'url' => ['bar' => 'baz']], 'bar'],
            [['GET' => ['foo' => 'foo'], 'POST' => ['bar' => 'bar'], 'url' => ['bar' => 'baz']], 'foo'],
            [['GET' => [], 'POST' => [], 'url' => []], null],
        ];
    }
    /* ------------------------------------ Method: __get END ------------------------------------------ */

    /* ------------------------------------ Method: __set START ---------------------------------------- */
    /**
     * @expectedException \RuntimeException
     */
    public function testSet() {
        $phalconRequest = $this
            ->getMockBuilder('Phalcon\Http\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $request = new \Maleficarum\Request\Request($phalconRequest, 'JsonParser');
        $request->foo = 'bar';
    }
    /* ------------------------------------ Method: __set END ------------------------------------------ */

    /* ------------------------------------ Method: attachUrlParams START ------------------------------ */
    public function testAttachUrlParams() {
        $phalconRequest = $this
            ->getMockBuilder('Phalcon\Http\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $request = new \Maleficarum\Request\Request($phalconRequest, 'JsonParser');

        $this->setProperty($request, 'data', []);
        $request->attachUrlParams(['foo' => 'bar']);
        $data = $this->getProperty($request, 'data');

        $this->assertSame(['url' => ['foo' => 'bar']], $data);
    }
    /* ------------------------------------ Method: attachUrlParams END -------------------------------- */

    /* ------------------------------------ Method: getMethod START ------------------------------------ */
    /**
     * @dataProvider methodDataProvider
     */
    public function testGetMethod($method) {
        $phalconRequest = $this
            ->getMockBuilder('Phalcon\Http\Request')
            ->setMethods(['getMethod'])
            ->getMock();

        $request = new \Maleficarum\Request\Request($phalconRequest, 'JsonParser');

        $_SERVER['REQUEST_METHOD'] = $method;
        $this->assertSame($method, $request->getMethod());
        unset($_SERVER['REQUEST_METHOD']);
    }
    
    public function methodDataProvider() {
        return [
            ['GET'],
            ['POST'],
            ['PUT'],
            ['DELETE']
        ];
    }
    /* ------------------------------------ Method: getMethod END -------------------------------------- */

    /* ------------------------------------ Method: getHeaders START ----------------------------------- */
    public function testGetHeaders() {
        $phalconRequest = $this
            ->getMockBuilder('Phalcon\Http\Request')
            ->disableOriginalConstructor()
            ->setMethods(['getHeaders'])
            ->getMock();
        $phalconRequest
            ->expects($this->once())
            ->method('getHeaders')
            ->willReturn(['X-Foo' => 'bar']);

        $request = new \Maleficarum\Request\Request($phalconRequest, 'JsonParser');

        $this->assertSame(['X-Foo' => 'bar'], $request->getHeaders());
    }
    /* ------------------------------------ Method: getHeaders END ------------------------------------- */

    /* ------------------------------------ Method: getHeader START ------------------------------------ */
    public function testGetHeader() {
        $phalconRequest = $this
            ->getMockBuilder('Phalcon\Http\Request')
            ->getMock();

        $request = new \Maleficarum\Request\Request($phalconRequest, 'JsonParser');

        $this->assertSame($request->getHeader('X-Foo'), 'foo');
    }
    /* ------------------------------------ Method: getHeader END -------------------------------------- */

    /* ------------------------------------ Method: getUri START --------------------------------------- */
    /**
     * @dataProvider parametersDataProvider
     */
    public function testGetParameters($method, $data, $expected) {
        $phalconRequest = $this
            ->getMockBuilder('Phalcon\Http\Request')
            ->getMock();

        $request = new \Maleficarum\Request\Request($phalconRequest, 'JsonParser');
        $this->setProperty($request, 'data', $data);

        $this->assertSame($expected, $request->getParameters($method));
    }

    public function parametersDataProvider() {
        return [
            ['GET', ['GET' => ['foo' => 'bar']], ['foo' => 'bar']],
            ['GET', ['GET' => null], null],
            ['POST', ['POST' => ['bar' => 'baz']], ['bar' => 'baz']],
            ['POST', ['POST' => null], null],
        ];
    }
    /* ------------------------------------ Method: getUri END ----------------------------------------- */

    /* ------------------------------------ Method: getUri START --------------------------------------- */
    public function testGetUri() {
        $phalconRequest = $this
            ->getMockBuilder('Phalcon\Http\Request')
            ->getMock();

        $request = new \Maleficarum\Request\Request($phalconRequest, 'JsonParser');

        $this->assertSame('foo', $request->getUri());
    }
    /* ------------------------------------ Method: getUri END ----------------------------------------- */

    /* ------------------------------------ Method: isGet START ---------------------------------------- */
    public function testIsGet() {
        $phalconRequest = $this
            ->getMockBuilder('Phalcon\Http\Request')
            ->disableOriginalConstructor()
            ->setMethods(['isGet'])
            ->getMock();
        $phalconRequest
            ->expects($this->once())
            ->method('isGet')
            ->willReturn(true);

        $request = new \Maleficarum\Request\Request($phalconRequest, 'JsonParser');

        $this->assertTrue($request->isGet());
    }
    /* ------------------------------------ Method: isGet END ------------------------------------------ */

    /* ------------------------------------ Method: isPost START --------------------------------------- */
    public function testIsPost() {
        $phalconRequest = $this
            ->getMockBuilder('Phalcon\Http\Request')
            ->disableOriginalConstructor()
            ->setMethods(['isPost'])
            ->getMock();
        $phalconRequest
            ->expects($this->once())
            ->method('isPost')
            ->willReturn(true);

        $request = new \Maleficarum\Request\Request($phalconRequest, 'JsonParser');

        $this->assertTrue($request->isPost());
    }
    /* ------------------------------------ Method: isPost END ----------------------------------------- */

    /* ------------------------------------ Method: isPut START ---------------------------------------- */
    public function testIsPut() {
        $phalconRequest = $this
            ->getMockBuilder('Phalcon\Http\Request')
            ->disableOriginalConstructor()
            ->setMethods(['isPut'])
            ->getMock();
        $phalconRequest
            ->expects($this->once())
            ->method('isPut')
            ->willReturn(true);

        $request = new \Maleficarum\Request\Request($phalconRequest, 'JsonParser');

        $this->assertTrue($request->isPut());
    }
    /* ------------------------------------ Method: isPut END ------------------------------------------ */

    /* ------------------------------------ Method: isDelete START ------------------------------------- */
    public function testIsDelete() {
        $phalconRequest = $this
            ->getMockBuilder('Phalcon\Http\Request')
            ->disableOriginalConstructor()
            ->setMethods(['isDelete'])
            ->getMock();
        $phalconRequest
            ->expects($this->once())
            ->method('isDelete')
            ->willReturn(true);

        $request = new \Maleficarum\Request\Request($phalconRequest, 'JsonParser');

        $this->assertTrue($request->isDelete());
    }
    /* ------------------------------------ Method: isDelete END --------------------------------------- */
}
