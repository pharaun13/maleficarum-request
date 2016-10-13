<?php
/**
 * The purpose of this class is to provide an abstract layer to accessing request data.
 */

namespace Maleficarum\Request;

class Request
{
    const PARSER_JSON = 'JsonParser';
    const PARSER_URL = 'UrlParser';
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';

    /**
     * Internal storage for the delegation request object
     *
     * @var \Phalcon\Http\Request|null
     */
    private $phalconRequest = null;

    /**
     * Internal storage for request data fetched from the phalcon object.
     *
     * @var array|null
     */
    private $data = null;

    /**
     * Internal storage for default request parser
     * 
     * @var string|null
     */
    private $defaultParser = null;

    /* ------------------------------------ Magic methods START ---------------------------------------- */
    /**
     * Initialize a new instance of the request object.
     *
     * @param \Phalcon\Http\Request $phalconRequest
     * @param string $defaultParser
     * 
     * @throws \Maleficarum\Request\Exception\UnsupportedMediaTypeException
     */
    public function __construct(\Phalcon\Http\Request $phalconRequest, $defaultParser) {
        // set delegations
        $this->setRequestDelegation($phalconRequest);
        $this->setDefaultParser($defaultParser);
        $parser = $this->getParser();

        // set data
        $this->setData([
            'url' => [],
            self::METHOD_POST => $parser->parsePostData(),
            self::METHOD_GET => $parser->parseGetData()
        ]);
    }

    /**
     * Fetch a request param.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     * @return string|null
     */
    public function __get($name) {
        // try post data first
        if (isset($this->getData()['url'][$name])) {
            return $this->getData()['url'][$name];
        } elseif (isset($this->getData()[self::METHOD_POST][$name])) {
            return $this->getData()[self::METHOD_POST][$name];
        } elseif (isset($this->getData()[self::METHOD_GET][$name])) {
            return $this->getData()[self::METHOD_GET][$name];
        }

        return null;
    }

    /**
     * Enforce the requests Read-Only policy.
     *
     * @param string $name
     * @param string $val
     *
     * @throws \RuntimeException
     */
    public function __set($name, $val) {
        throw new \RuntimeException('Request data is Read-Only. \Maleficarum\Request\Http\Request::__set()');
    }
    /* ------------------------------------ Magic methods END ------------------------------------------ */

    /* ------------------------------------ Request methods START -------------------------------------- */
    /**
     * Attach URL parameters
     *
     * @param array $params
     *
     * @return \Maleficarum\Request\Request
     */
    public function attachUrlParams(array $params) {
        $data = $this->getData();
        $data['url'] = $params;

        return $this->setData($data);
    }

    /**
     * Get http method.
     *
     * @return string
     */
    public function getMethod() {
        return $this->getRequestDelegation()->getMethod();
    }

    /**
     * Fetch all request headers.
     *
     * @return array
     */
    public function getHeaders() {
        return $this->getRequestDelegation()->getHeaders();
    }

    /**
     * Fetch a specified header from the request.
     *
     * @param string $name
     *
     * @return string
     */
    public function getHeader($name) {
        return $this->getRequestDelegation()->getHeader($name);
    }

    /**
     * Fetch parameters of given method
     *
     * @param string $method
     *
     * @return array|null
     */
    public function getParameters($method = self::METHOD_GET) {
        $data = $this->getData();

        if ($method === self::METHOD_GET && isset($data[self::METHOD_GET])) {
            return $data[self::METHOD_GET];
        }

        if ($method === self::METHOD_POST && isset($data[self::METHOD_POST])) {
            return $data[self::METHOD_POST];
        }

        return null;
    }

    /**
     * Fetch current request URI.
     *
     * @return string
     */
    public function getUri() {
        return $this->getRequestDelegation()->getURI();
    }

    /**
     * Check if this request has a GET method.
     *
     * @return bool
     */
    public function isGet() {
        return $this->getRequestDelegation()->isGet();
    }

    /**
     * Check if this request has a POST method.
     *
     * @return bool
     */
    public function isPost() {
        return $this->getRequestDelegation()->isPost();
    }

    /**
     * Check if this request has a PUT method.
     *
     * @return bool
     */
    public function isPut() {
        return $this->getRequestDelegation()->isPut();
    }

    /**
     * Check if this request has a DELETE method.
     *
     * @return bool
     */
    public function isDelete() {
        return $this->getRequestDelegation()->isDelete();
    }

    /**
     * Get parser object
     * 
     * @return \Maleficarum\Request\Parser\AbstractParser
     * 
     * @throws \Maleficarum\Request\Exception\UnsupportedMediaTypeException
     */
    private function getParser() {
        $parserClass = $this->determineParser();
        if (empty($parserClass)) {
            throw new \Maleficarum\Request\Exception\UnsupportedMediaTypeException('Provided Content-Type is not supported. \Maleficarum\Request\Http\Request::getParser()');
        }

        $fqn = 'Maleficarum\Request\Parser\\' . $parserClass;
        /** @var \Maleficarum\Request\Parser\AbstractParser $parser */
        $parser = new $fqn($this->getRequestDelegation());

        return $parser;
    }

    /**
     * Determine request parser
     *
     * @return null|string
     */
    private function determineParser() {
        $contentType = $this->getHeader('Content-Type');
        $parserClass = null;

        preg_match('/^application\/json/', $contentType) and $parserClass = self::PARSER_JSON;
        preg_match('/^application\/x-www-form-urlencoded/', $contentType) and $parserClass = self::PARSER_URL;

        empty($contentType) and $parserClass = $this->getDefaultParser();

        return $parserClass;
    }
    /* ------------------------------------ Request methods END ---------------------------------------- */

    /* ------------------------------------ Setters & Getters START ------------------------------------ */
    /**
     * Set the request delegation instance.
     *
     * @param \Phalcon\Http\Request $pr
     *
     * @return \Maleficarum\Request\Request
     */
    private function setRequestDelegation(\Phalcon\Http\Request $pr) {
        $this->phalconRequest = $pr;

        return $this;
    }

    /**
     * Fetch the current request object that we delegate to.
     *
     * @return \Phalcon\Http\Request
     */
    private function getRequestDelegation() {
        return $this->phalconRequest;
    }

    /**
     * Set data
     *
     * @param array $data
     *
     * @return \Maleficarum\Request\Request
     */
    private function setData(array $data) {
        $this->data = $data;

        return $this;
    }

    /**
     * Fetch current request data.
     *
     * @return array
     */
    private function getData() {
        return $this->data;
    }

    /**
     * Get defaultParser
     *
     * @return string|null
     */
    private function getDefaultParser() {
        return $this->defaultParser;
    }

    /**
     * Set defaultParser
     *
     * @param string|null $defaultParser
     *
     * @return Request
     */
    private function setDefaultParser($defaultParser) {
        if (!in_array($defaultParser, [self::PARSER_JSON, self::PARSER_URL], true)) {
            throw new \InvalidArgumentException('Invalid parser provided. \Maleficarum\Request\Request::setDefaultParser()');
        }

        $this->defaultParser = $defaultParser;

        return $this;
    }
    /* ------------------------------------ Setters & Getters END -------------------------------------- */
}
