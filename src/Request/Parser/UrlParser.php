<?php
/**
 * This class is a specific parser implementation for urlencoded data parsing
 *
 * @extends \Maleficarum\Request\Parser\AbstractParser
 */

namespace Maleficarum\Request\Parser;

class UrlParser extends \Maleficarum\Request\Parser\AbstractParser
{
    /* ------------------------------------ AbstractParser methods START ------------------------------- */
    /**
     * Parse POST data
     * 
     * @see \Maleficarum\Request\Parser\AbstractParser::parsePostData()
     * @return array
     */
    public function parsePostData() : array {
        // fetch request data from $_POST superglobal
        $data = (array)$this->getRequest()->getPost();
        $data = $this->sanitizeData($data);

        return $data;
    }
    /* ------------------------------------ AbstractParser methods END --------------------------------- */
}
