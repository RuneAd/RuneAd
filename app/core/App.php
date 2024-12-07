<?php

use Router\Router;

class App {

    /** @var Controller controller */
    private $controller;
    private $router;

    public function __construct() {
        $this->router = PageRouter::getInstance();
        $this->router->initRoutes();

        try {
            $this->router->route();
        } catch (\Router\RouteNotFoundException $e) {
            $this->router->setRoute("/");
        }

        $controller  = $this->router->getController(true);

        /** @var Controller controller */
        $this->controller = new $controller;

        /** Redirects to 404 is method doesn't exist. */
        if (!method_exists($this->controller, $this->router->getMethod())) {
            $this->router->setRoute("/");

            $controller  = $this->router->getController(true);
            $this->controller = new $controller;
        }

        $this->controller->setView($this->router->getViewPath());
        $this->controller->setActionName($this->router->getMethod());
        $this->controller->setRouter($this->router);

        if ($this->initRoute()) {
            $this->controller->show();
        }
    }

    /**
     * Calls the action within a controller
     * TODO: revisit later
     */
    public function initRoute() {
        if (method_exists($this->controller, "beforeExecute")) {
            $before = call_user_func_array([$this->controller, "beforeExecute"], []);

              if (!$before) {
                return true;
            }
        }

        $controller = $this->controller;
         $method     = $this->router->getMethod();
         $params     = $this->router->getParams();

         $output = call_user_func_array([$controller, $method], $params);

        if ($this->controller->isJson()) {
            header('Content-Type: application/json');
            echo json_encode($output);
            return;
        }

        $content = ob_get_contents();
        ob_end_clean();
        $this->controller->set("content", $content);
        return true;
    }


}
?>
