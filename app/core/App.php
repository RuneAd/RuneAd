<?php

use Router\Router;

class App {

    /** @var Controller controller */
    private $controller;
    private $router;

    public function __construct() {
        $this->router = PageRouter::getInstance();
        $this->initializeRouter();

        $this->loadController();

        if ($this->initRoute()) {
            $this->controller->show();
        }
    }

    /**
     * Initializes the router and handles routing.
     */
    private function initializeRouter() {
        $this->router->initRoutes();

        try {
            $this->router->route();
        } catch (\Router\RouteNotFoundException $e) {
            $this->router->setRoute("errors", "show404");
        }
    }

    /**
     * Loads the appropriate controller based on the current route.
     */
    private function loadController() {
        $controllerClass = $this->router->getController(true);

        if (!class_exists($controllerClass)) {
            $this->redirectTo404();
            return;
        }

        $this->controller = new $controllerClass;

        if (!method_exists($this->controller, $this->router->getMethod())) {
            $this->redirectTo404();
        }

        $this->controller->setView($this->router->getViewPath());
        $this->controller->setActionName($this->router->getMethod());
        $this->controller->setRouter($this->router);
    }

    /**
     * Redirects to the 404 error page.
     */
    private function redirectTo404() {
        $this->router->setRoute("errors", "show404");

        $controllerClass = $this->router->getController(true);
        $this->controller = new $controllerClass;
    }

    /**
     * Executes the controller action and handles output.
     *
     * @return bool
     */
    public function initRoute() {
        if (method_exists($this->controller, "beforeExecute")) {
            $before = call_user_func([$this->controller, "beforeExecute"]);
            if (!$before) {
                return false;
            }
        }

        $method = $this->router->getMethod();
        $params = $this->router->getParams();

        ob_start();
        $output = call_user_func_array([$this->controller, $method], $params);
        $content = ob_get_clean();

        if ($this->controller->isJson()) {
            header('Content-Type: application/json');
            echo json_encode($output);
            return false;
        }

        $this->controller->set("content", $content);
        return true;
    }
}
?>
