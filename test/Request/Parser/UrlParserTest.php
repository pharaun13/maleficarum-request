<?php
declare(strict_types = 1);

/**
 * Tests for the \Maleficarum\Request\Parser\UrlParser class.
 */

namespace Maleficarum\Request\Tests\Parser;

class UrlParserTest extends \Maleficarum\Tests\TestCase
{
    /* ------------------------------------ Method: parseGetData START --------------------------------- */
    /**
     * @dataProvider postDataProvider
     */
    public function testParsePostData($data, $expected) {
        $request = $this
            ->getMockBuilder('Phalcon\Http\Request')
            ->setMethods(['getPost'])
            ->getMock();
        $request
            ->expects($this->once())
            ->method('getPost')
            ->willReturn($data);

        $parser = new \Maleficarum\Request\Parser\UrlParser($request);

        $data = $parser->parsePostData();

        $this->assertSame($expected, $data);
    }

    public function postDataProvider() {
        return [
            [
                [
                    'foo' => '<strong>foo</strong>',
                    'bar' => '"bar"',
                    'baz' => '\'baz\''
                ],
                [
                    'foo' => 'foo',
                    'bar' => '"bar"',
                    'baz' => '\'baz\''
                ]
            ],
            [
                [
                    'foo' => "\t" . 'foo' . "\t",
                    'bar' => "\n" . 'bar' . "\n",
                    'baz' => "\r" . 'baz' . "\r",
                    'qux' => "\0" . 'qux' . "\0",
                    'quux' => "\x0B" . 'quux' . "\x0B",
                    'corge' => ' corge ',
                ],
                [
                    'foo' => 'foo',
                    'bar' => 'bar',
                    'baz' => 'baz',
                    'qux' => 'qux',
                    'quux' => 'quux',
                    'corge' => 'corge',
                ]
            ]
        ];
    }
    /* ------------------------------------ Method: parseGetData END ----------------------------------- */
}
