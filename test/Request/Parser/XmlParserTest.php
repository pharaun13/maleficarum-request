<?php
declare(strict_types = 1);

/**
 * Tests for the \Maleficarum\Request\Parser\XmlParser class.
 */

namespace Maleficarum\Request\Tests\Parser;

class XmlParserTest extends \Maleficarum\Tests\TestCase {

    /* ------------------------------------ Method: getRawPostPayload START --------------------------------- */

    /**
     * @dataProvider getRawPostPayloadDataProvider
     */
    public function testGetRawPostPayload($raw, $expected) {
        $request = $this
            ->getMockBuilder('Phalcon\Http\Request')
            ->setMethods(['getRawBody'])
            ->getMock();

        $request
            ->expects($this->once())
            ->method('getRawBody')
            ->willReturn($raw);

        $parser = new \Maleficarum\Request\Parser\XmlParser($request);
        $parsed = $parser->getRawPostPayload();

        $this->assertSame($expected, $parsed);
    }
    
    public function getRawPostPayloadDataProvider(): array
    {
        return [
            [
                '<?xml version="1.0" encoding="UTF-8"?>
                 <root>
                    <first testAttributeName=" testAttributeValue">
                        <second>testValue'. "\n" .'</second>
                        <third></third>
                    </first>
                 </root>
                ',
                [
                    'root' => [
                        'first' => [
                            '@attributes' => [
                                'testAttributeName' => ' testAttributeValue',
                            ],
                            'second' => [
                                '@value' => 'testValue' . "\n",
                            ],
                            'third' => []
                        ]
                    ]
                ]
            ]
        ];
    }

    /* ------------------------------------ Method: getRawPostPayload END ----------------------------------- */

    /* ------------------------------------ Method: parsePostData START --------------------------------- */

    /**
     * @dataProvider parsePostDataDataProvider
     */
    public function testParsePostData($raw, $expected) {
        $request = $this
            ->getMockBuilder('Phalcon\Http\Request')
            ->setMethods(['getRawBody'])
            ->getMock();

        $request
            ->expects($this->once())
            ->method('getRawBody')
            ->willReturn($raw);

        $parser = new \Maleficarum\Request\Parser\XmlParser($request);
        $parsed = $parser->parsePostData();

        $this->assertSame($expected, $parsed);
    }

    public function parsePostDataDataProvider(): array
    {
        return [
            [
                '<?xml version="1.0" encoding="UTF-8"?>
                 <root>
                    <first testAttributeName=" testAttributeValue">
                        <second>testValue'. "\n" .'</second>
                        <third></third>
                    </first>
                 </root>
                ',
                [
                    'root' => [
                        'first' => [
                            '@attributes' => [
                                'testAttributeName' => 'testAttributeValue',
                            ],
                            'second' => [
                                '@value' => 'testValue',
                            ],
                            'third' => []
                        ]
                    ]
                ]
            ]
        ];
    }

    /* ------------------------------------ Method: parsePostData END ----------------------------------- */

}
