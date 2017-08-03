<?php
/**
 * This trait provides functionality common to all classes dependant on the \Maleficarum\Request class
 */
declare (strict_types=1);

namespace Maleficarum\Request;

trait Dependant {
    /* ------------------------------------ Class Property START --------------------------------------- */

    /**
     * Internal storage for the request provider object.
     *
     * @var \Maleficarum\Request\Request|null
     */
    protected $request = null;

    /* ------------------------------------ Class Property END ----------------------------------------- */

    /* ------------------------------------ Class Methods START ---------------------------------------- */

    /**
     * Inject a new request provider object into this collection.
     *
     * @param \Maleficarum\Request\Request $request
     *
     * @return \Maleficarum\Request\Request
     */
    public function setRequest(\Maleficarum\Request\Request $request) {
        $this->request = $request;

        return $this;
    }

    /**
     * Fetch the currently assigned request provider object.
     *
     * @return \Maleficarum\Request\Request
     */
    public function getRequest(): ?\Maleficarum\Request\Request {
        return $this->request;
    }

    /**
     * Detach the currently assigned request provider object.
     *
     * @return \Maleficarum\Request\Request
     */
    public function detachRequest() {
        $this->request = null;

        return $this;
    }

    /* ------------------------------------ Class Methods END ------------------------------------------ */
}
