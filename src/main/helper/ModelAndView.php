<?php

class ModelAndView
{

    private $view;
    private $models;

    public function __construct($view = null, $models = null)
    {
        if ($view != null) {
            $this->view = $view;
        }

        if ($models != null) {
            $this->models = $models;
        } else {
            $this->models = array();
        }
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->models)) {

            return $this->models[$name];
        }

        var_dump('Not found');
        return null;
    }

    public function addModel($name, $model)
    {
        $this->models[$name] = $model;
    }

    public function getModels()
    {
        return $this->models;
    }

    public function setModels($models)
    {
        $this->models = $models;
    }

    public function getView()
    {
        return $this->view;
    }

    public function setView($view)
    {
        $this->view = $view;
    }
}
