<?php
/**
 * The purpose of this class is to provide an abstract layer to accessing request data.
 */
declare (strict_types=1);

namespace Maleficarum\Request;

class Request {
    
    /* ------------------------------------ Class Property START --------------------------------------- */
    
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

    /**
     * Internal storage for available request parsers
     * 
     * @var array
     */
    private static $availableParsers = [
        self::PARSER_JSON,
        self::PARSER_URL
    ];

    /* ------------------------------------ Class Property END ----------------------------------------- */

    /* ------------------------------------ Magic methods START ---------------------------------------- */
    
    /**
     * Initialize a new instance of the request object.
     *
     * @param \Phalcon\Http\Request $phalconRequest
     * @param string $defaultParser
     */
    public function __construct(\Phalcon\Http\Request $phalconRequest, string $defaultParser) {
        // set delegations
        $this->setRequestDelegation($phalconRequest);
        $this->setDefaultParser($defaultParser);

        $contentType = $phalconRequest->getHeader('Content-Type');
        $parser = $this->determineParser($contentType, $defaultParser);
        $parser = $this->getParser($parser, $phalconRequest);

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
     * @return mixed
     */
    public function __get(string $name) {
        $data = $this->getData();

        // try post data first
        if (isset($data['url'][$name])) {
            return $data['url'][$name];
        } elseif (isset($data[self::METHOD_POST][$name])) {
            return $data[self::METHOD_POST][$name];
        } elseif (isset($data[self::METHOD_GET][$name])) {
            return $data[self::METHOD_GET][$name];
        }

        return null;
    }

    /**
     * Enforce the requests Read-Only policy.
     * 
     * @param string $name
     * @param mixed $val
     * @return void
     * @throws \RuntimeException
     */
    public function __set(string $name, $val) {
        throw new \RuntimeException(sprintf('Request data is Read-Only. \%s::__set()', static::class));
    }
    
    /* ------------------------------------ Magic methods END ------------------------------------------ */

    /* ------------------------------------ Class Methods START ---------------------------------------- */
    
    /**
     * Attach URL parameters
     * 
     * @param array $params
     * @return \Maleficarum\Request\Request
     */
    public function attachUrlParams(array $params) : \Maleficarum\Request\Request {
        $data = $this->getData();
        $data['url'] = $params;
        $this->setData($data);

        return $this;
    }

    /**
     * Get http method.
     *
     * @return string
     */
    public function getMethod() : string {
        return $this->getRequestDelegation()->getMethod();
    }

    /**
     * Fetch all request headers.
     *
     * @return array
     */
    public function getHeaders() : array {
        return $this->getRequestDelegation()->getHeaders();
    }

    /**
     * Fetch a specified header from the request.
     *
     * @param string $name
     * @return string
     */
    public function getHeader(string $name) : string {
        return $this->getRequestDelegation()->getHeader($name);
    }

    /**
     * Fetch parameters of given method
     *
     * @param string $method
     * @return mixed
     */
    public function getParameters(string $method = self::METHOD_GET) {
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
    public function getUri() : string {
        return $this->getRequestDelegation()->getURI();
    }

    /**
     * Check if this request has a GET method.
     *
     * @return bool
     */
    public function isGet() : bool {
        return $this->getRequestDelegation()->isGet();
    }

    /**
     * Check if this request has a POST method.
     *
     * @return bool
     */
    public function isPost() : bool {
        return $this->getRequestDelegation()->isPost();
    }

    /**
     * Check if this request has a PUT method.
     *
     * @return bool
     */
    public function isPut() : bool {
        return $this->getRequestDelegation()->isPut();
    }

    /**
     * Check if this request has a PATCH method.
     *
     * @return bool
     */
    public function isPatch() : bool {
        return $this->getRequestDelegation()->isPatch();
    }

    /**
     * Check if this request has a DELETE method.
     *
     * @return bool
     */
    public function isDelete() : bool {
        return $this->getRequestDelegation()->isDelete();
    }

    /**
     * Get parser object
     * 
     * @param string|null $parserClass
     * @param \Phalcon\Http\Request $request
     * @return \Maleficarum\Request\Parser\AbstractParser
     * @throws \Maleficarum\Exception\UnsupportedMediaTypeException
     */
    private function getParser(string $parserClass = null, \Phalcon\Http\Request $request) : \Maleficarum\Request\Parser\AbstractParser {
        if (empty($parserClass)) {
            throw new \Maleficarum\Exception\UnsupportedMediaTypeException(sprintf('Provided Content-Type is not supported. \%s::getParser()', static::class));
        }

        /** @var \Maleficarum\Request\Parser\AbstractParser $parser */
        $parser = \Maleficarum\Ioc\Container::get('Maleficarum\Request\Parser\\' . $parserClass, [$request]);

        return $parser;
    }

    /**
     * Determine request parser
     * 
     * @param string $contentType
     * @param string $defaultParser
     * @return string|null
     */
    private function determineParser(string $contentType, string $defaultParser) {
        $parserClass = null;

        preg_match('/^application\/json/', $contentType) and $parserClass = self::PARSER_JSON;
        preg_match('/^application\/x-www-form-urlencoded/', $contentType) and $parserClass = self::PARSER_URL;

        empty($contentType) and $parserClass = $defaultParser;

        return $parserClass;
    }
    
    /* ------------------------------------ Class Methods END ------------------------------------------ */

    /* ------------------------------------ Setters & Getters START ------------------------------------ */
    
    /**
     * Set the request delegation instance.
     *
     * @param \Phalcon\Http\Request $phalconRequest
     * @return \Maleficarum\Request\Request
     */
    private function setRequestDelegation(\Phalcon\Http\Request $phalconRequest) : \Maleficarum\Request\Request {
        $this->phalconRequest = $phalconRequest;

        return $this;
    }

    /**
     * Fetch the current request object that we delegate to.
     *
     * @return \Phalcon\Http\Request|null
     */
    private function getRequestDelegation() {
        return $this->phalconRequest;
    }

    /**
     * Set data
     *
     * @param array $data
     * @return \Maleficarum\Request\Request
     */
    private function setData(array $data) : \Maleficarum\Request\Request {
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
     * Set defaultParser
     *
     * @param string $defaultParser
     * @return \Maleficarum\Request\Request
     * @throws \InvalidArgumentException
     */
    private function setDefaultParser(string $defaultParser) : \Maleficarum\Request\Request {
        if (!in_array($defaultParser, self::$availableParsers, true)) {
            throw new \InvalidArgumentException(sprintf('Invalid parser provided. \%s::setDefaultParser()', static::class));
        }

        $this->defaultParser = $defaultParser;

        return $this;
    }
    
    /* ------------------------------------ Setters & Getters END -------------------------------------- */
    
}
