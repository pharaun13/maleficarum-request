<?php

\Maleficarum\Ioc\Container::register('Maleficarum\Request\Parser\JsonParser', function () {
    $parser = $this
        ->getMockBuilder('Maleficarum\Request\Parser\JsonParser')
        ->setMethods(['parsePostData', 'parseGetData'])
        ->disableOriginalConstructor()
        ->getMock();

    $parser
        ->expects($this->once())
        ->method('parsePostData')
        ->willReturn(['foo' => 'bar']);
    $parser
        ->expects($this->once())
        ->method('parseGetData')
        ->willReturn(['bar' => 'baz']);

    return $parser;
});


\Maleficarum\Ioc\Container::register('Maleficarum\Request\Parser\UrlParser', function () {
    $parser = $this
        ->getMockBuilder('Maleficarum\Request\Parser\UrlParser')
        ->setMethods(['parsePostData', 'parseGetData'])
        ->disableOriginalConstructor()
        ->getMock();

    $parser
        ->expects($this->once())
        ->method('parsePostData')
        ->willReturn(['bar' => 'baz']);
    $parser
        ->expects($this->once())
        ->method('parseGetData')
        ->willReturn(['baz' => 'qux']);

    return $parser;
});

