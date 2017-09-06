<?php
/**
 * This class provides functionality common to all parsers classes.
 */
declare (strict_types=1);

namespace Maleficarum\Request\Parser;

abstract class AbstractParser {
    /* ------------------------------------ Class Property START --------------------------------------- */

    /**
     * Internal storage for request
     *
     * @var \Phalcon\Http\Request
     */
    protected $request;

    /* ------------------------------------ Class Property END ----------------------------------------- */

    /* ------------------------------------ Magic methods START ---------------------------------------- */

    /**
     * AbstractParser constructor.
     *
     * @param \Phalcon\Http\Request $request
     */
    public function __construct(\Phalcon\Http\Request $request) {
        $this->request = $request;
    }

    /* ------------------------------------ Magic methods END ------------------------------------------ */

    /* ------------------------------------ Abstract methods START ------------------------------------- */

    /**
     * Parse POST data
     *
     * @return array
     */
    abstract public function parsePostData() : array;
	
	/**
	 * Fetch POST data without any sanitization.
	 * 
	 * @return array
	 */
    abstract public function getRawPostPayload() : array;

    /* ------------------------------------ Abstract methods END --------------------------------------- */

    /* ------------------------------------ Class methods START ---------------------------------------- */

    /**
     * Parse GET data
     *
     * @return array
     */
    public function parseGetData(): array {
        $data = (array)$this->getRequest()->getQuery();
        $data = $this->sanitizeData($data);

        return $data;
    }

    /**
     * Sanitize data
     *
     * @param array $data
     *
     * @return array
     */
    protected function sanitizeData(array $data): array {
        array_walk_recursive($data, function (&$item) {
            is_string($item) and $item = trim(filter_var($item, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
        });

        return $data;
    }

    /* ------------------------------------ Class methods END ------------------------------------------ */

    /* ------------------------------------ Setters & Getters START ------------------------------------ */

    /**
     * Get request
     *
     * @return \Phalcon\Http\Request
     */
    protected function getRequest() {
        return $this->request;
    }

    /**
     * Set request
     *
     * @param \Phalcon\Http\Request $request
     *
     * @return \Maleficarum\Request\Parser\AbstractParser
     */
    protected function setRequest(\Phalcon\Http\Request $request): \Maleficarum\Request\Parser\AbstractParser {
        $this->request = $request;

        return $this;
    }

    /* ------------------------------------ Setters & Getters END -------------------------------------- */
}
