<?php
declare(strict_types = 1);

/**
 * Tests for the \Maleficarum\Request\Parser\AbstractParser class.
 */

namespace Maleficarum\Request\Tests\Parser;

class AbstractParserTest extends \Maleficarum\Tests\TestCase
{
    /* ------------------------------------ Method: parseGetData START --------------------------------- */
    /**
     * @dataProvider queryDataProvider
     */
    public function testParseGetData($data, $expected) {
        $request = $this
            ->getMockBuilder('Phalcon\Http\Request')
            ->setMethods(['getQuery'])
            ->getMock();
        $request
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn($data);

        $parser = $this
            ->getMockBuilder('Maleficarum\Request\Parser\AbstractParser')
            ->setConstructorArgs([$request])
            ->getMockForAbstractClass();

        $data = $parser->parseGetData();

        $this->assertSame($expected, $data);
    }

    public function queryDataProvider() {
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
