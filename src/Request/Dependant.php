<?php
/**
 * This trait provides functionality common to all classes dependant on the \Maleficarum\Request\Request class
 */

namespace Maleficarum\Request;

trait Dependant
{
    /**
     * Internal storage for the request provider object.
     *
     * @var \Maleficarum\Request\Request|null
     */
    protected $request = null;

    /* ------------------------------------ Dependant methods START ------------------------------------ */
    /**
     * Inject a new request provider object into this collection.
     *
     * @param \Maleficarum\Request\Request $request
     *
     * @return $this
     */
    public function setRequest(\Maleficarum\Request\Request $request) {
        $this->request = $request;

        return $this;
    }

    /**
     * Fetch the currently assigned request provider object.
     *
     * @return \Maleficarum\Request\Request|null
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * Detach the currently assigned request provider object.
     *
     * @return $this
     */
    public function detachRequest() {
        $this->request = null;

        return $this;
    }
    /* ------------------------------------ Dependant methods END -------------------------------------- */
}
