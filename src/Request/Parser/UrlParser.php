<?php

namespace Maleficarum\Request\Parser;

class UrlParser extends \Maleficarum\Request\Parser\AbstractParser
{
    /* ------------------------------------ AbstractParser methods START ------------------------------- */
    /**
     * @see \Maleficarum\Request\Parser\AbstractParser::parsePostData()
     */
    public function parsePostData() {
        // fetch request data from $_POST superglobal
        $data = (array)$this->getRequest()->getPost();
        $data = $this->sanitizeData($data);

        return $data;
    }
    /* ------------------------------------ AbstractParser methods END --------------------------------- */
}
