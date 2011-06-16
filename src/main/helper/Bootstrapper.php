<?php

require_once realpath(dirname(__FILE__)) . '/Request.php';

class Bootstrapper
{
    const CONTROLLER_DIR = '/../controller/';

    /**
     * Initialize the application:
     * - Check and create the controller
     * - Check the method
     * - Create a request object
     * - Call the method
     * 
     * @throws Exception
     * @param  $controller
     * @param  $method
     * @return void
     */
    public function init($controller, $method)
    {
        // Check the controller
        if (!$this->controllerExists($controller)) {
            throw new Exception('Controller does not exist');
        }

        // Instantiate the controller
        require_once realpath(dirname(__FILE__)) . '/../controller/' . $controller.'.php';
        $instance = new $controller();

        // Check the method
        $method = 'execute'.$method;
        if (!$this->methodExists($instance, $method)) {
            throw new Exception('Method does not exist');
        }

        // Make a request object
        $request = new Request();

        // Execute the requested method
        $modelAndView = $instance->$method($request);

        if ($modelAndView == null || !($modelAndView instanceof ModelAndView)) {
            throw new Exception('No ModelAndView given');
        }

        $this->renderView($modelAndView);
    }

    protected function renderView(ModelAndView $modelAndView)
    {
        include realpath(dirname(__FILE__)) . '/../view/'.$modelAndView->getView().'.php';
    }

    /**
     * Checks whether the $controller file exists.
     *
     * @param  $controller
     * @return bool
     */
    protected function controllerExists($controller)
    {
        return file_exists(realpath(dirname(__FILE__)) . self::CONTROLLER_DIR . $controller.'.php');
    }

    /**
     * Checks whether the $method in $controller exists.
     * 
     * @param  $controller
     * @param  $method
     * @return bool
     */
    protected function methodExists($controller, $method)
    {
        return method_exists($controller, $method);
    }
}