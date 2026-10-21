<?php
class App{
    protected $controller="Home";
    protected $action= "Index";
    protected $param=[];

    function __construct(){
        $arr = $this->processURL();

        if (!empty($arr[0])) {
            if (!$this->isValidRoutePart($arr[0])) {
                $this->notFound();
                return;
            }

            $controllerFile = './MVC/Controllers/' . $arr[0] . '.php';
            if (!file_exists($controllerFile)) {
                $this->notFound();
                return;
            }
            $this->controller = $arr[0];
            unset($arr[0]);
        }

        include_once './MVC/Controllers/' . $this->controller . '.php';
        $controllerClass = $this->controller;
        if (!class_exists($controllerClass) || !is_subclass_of($controllerClass, Controller::class)) {
            $this->notFound();
            return;
        }
        $this->controller = new $controllerClass;

        $requestedAction = $arr[1] ?? $this->action;
        if (!$this->isValidRoutePart($requestedAction) || !$this->isPublicControllerAction($requestedAction, $controllerClass)) {
            $this->notFound();
            return;
        }
        $this->action = $requestedAction;
        unset($arr[1]);

        $this->param = $arr ? array_map('rawurldecode', array_values($arr)) : [];
        call_user_func_array([$this->controller, $this->action], $this->param);
    }

    function processURL(){
        if(isset($_GET['url'])){
            return explode('/', trim((string) $_GET['url'], '/'));
        }
        return [];
    }

    private function isValidRoutePart($value){
        return is_string($value) && preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $value) === 1;
    }

    private function isPublicControllerAction($action, $controllerClass){
        if (!method_exists($this->controller, $action)) return false;

        $method = new ReflectionMethod($this->controller, $action);
        return $method->isPublic() && $method->getDeclaringClass()->getName() === $controllerClass;
    }

    private function notFound(){
        http_response_code(404);
        echo '404 - Không tìm thấy trang';
    }
}
?>
