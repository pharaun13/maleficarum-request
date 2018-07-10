<?php
declare (strict_types=1);

namespace Maleficarum\Request\File;

class File {

    /**
     * @var array
     */
    private $files = [];

    public function __construct() {
        isset($_FILES) and $this->files = $_FILES;
    }

    /**
     * @param string $key
     *
     * @return null
     */
    public function getFile(string $key) {
        return $this->files[$key] ?? null;
    }
}
