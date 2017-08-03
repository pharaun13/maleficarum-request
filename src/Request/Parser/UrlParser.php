<?php
/**
 * This class is a specific parser implementation for urlencoded data parsing
 */
declare (strict_types=1);

namespace Maleficarum\Request\Parser;

class UrlParser extends \Maleficarum\Request\Parser\AbstractParser {
    /* ------------------------------------ Class Methods START ---------------------------------------- */

    /**
     * @see \Maleficarum\Request\Parser\AbstractParser::parsePostData()
     */
    public function parsePostData(): array {
        // fetch request data from $_POST superglobal
        $data = (array)$this->getRequest()->getPost();
        $data = $this->sanitizeData($data);

        return $data;
    }

    /* ------------------------------------ Class Methods END ------------------------------------------ */
}
