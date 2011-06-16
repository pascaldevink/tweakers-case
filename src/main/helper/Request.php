<?php

class Request {

    private $postParameters = array();
    private $getParameters = array();

    /**
     * Constructor which immediately takes the $_GET and $_POST globals and wraps them. It then unsets them for added
     * security.
     * 
     */
    public function __construct()
    {
        foreach($_GET as $key=>$value) {
            $this->addGetParameter($key, $value);
        }

        foreach($_POST as $key=>$value) {
            $this->addPostParameter($key, $value);
        }

        unset($_GET);
        unset($_POST);
        unset($_REQUEST);
    }

    /**
     * Add a &key => $value pair to the get parameters.
     *
     * @param  $key
     * @param  $value
     * @return void
     */
    public function addGetParameter($key, $value)
    {
        $this->getParameters[$key] = htmlentities($value);
    }

    /**
     * Add a &key => $value pair to the post parameters.
     *
     * @param  $key
     * @param  $value
     * @return void
     */
    public function addPostParameter($key, $value)
    {
        $this->postParameters[$key] = htmlentities($value);
    }

    /**
     * Retrieve a post parameter by $name.
     * 
     * @param  $name
     * @return string|null
     */
    public function getPostParameter($name)
    {
        if (isset($this->postParameters[$name])) {
            return $this->postParameters[$name];
        } else {
            return null;
        }
    }

    /**
     * Retrieve a get parameter by $name.
     *
     * @param  $name
     * @return string|null
     */
    public function getGetParameter($name)
    {
        if (isset($this->getParameters[$name])) {
           return $this->getParameters[$name];
        } else {
            return null;
        }
    }
}
