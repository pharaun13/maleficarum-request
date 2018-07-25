<?php
declare (strict_types=1);

namespace Maleficarum\Request\Files;

class Files {

    /**
     * @var array
     */
    private $files = [];

    /**
     * Files constructor.
     */
    public function __construct() {
        isset($_FILES) and $this->files = $_FILES;
    }

    /**
     * @return array
     */
    public function getFiles() {
        return $this->files;
    }
}
