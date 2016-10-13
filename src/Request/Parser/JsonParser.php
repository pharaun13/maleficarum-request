<?php

namespace Maleficarum\Request\Parser;

class JsonParser extends \Maleficarum\Request\Parser\AbstractParser
{
    /* ------------------------------------ AbstractParser methods START ------------------------------- */
    /**
     * @see \Maleficarum\Request\Parser\AbstractParser::parsePostData()
     */
    public function parsePostData() {
        // fetch request data from phalcon (json is handled in a different way that $_REQUEST)
        $data = (array)$this->getRequest()->getJsonRawBody();
        $data = $this->sanitizeData($data);

        return $data;
    }
    /* ------------------------------------ AbstractParser methods END --------------------------------- */
}
