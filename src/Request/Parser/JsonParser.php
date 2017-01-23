<?php
/**
 * This class is a specific parser implementation for JSON data parsing
 *
 * @extends \Maleficarum\Request\Parser\AbstractParser
 */

namespace Maleficarum\Request\Parser;

class JsonParser extends \Maleficarum\Request\Parser\AbstractParser
{
    /* ------------------------------------ AbstractParser methods START ------------------------------- */
    /**
     * Parse POST data
     * 
     * @see \Maleficarum\Request\Parser\AbstractParser::parsePostData()
     * 
     * @return array
     */
    public function parsePostData() : array {
        // fetch request data from phalcon (json is handled in a different way that $_REQUEST)
        $data = (array)$this->getRequest()->getJsonRawBody(true);
        $data = $this->sanitizeData($data);

        return $data;
    }
    /* ------------------------------------ AbstractParser methods END --------------------------------- */
}
