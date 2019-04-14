<?php
namespace App;

use App\Lib\Auth;
use App\Lib\DB;
use App\Lib\Controller;
use FastRoute;
use Jasny\HttpMessage\ServerRequest;
use Jasny\HttpMessage\Response;



class App
{
    public $routeInfo;
    public $appName;
    private $__db;
    private $__auth;
    public $request;
    public $response;

    private function __construct($config = [])
    {
        ob_start();
        $this->routeInfo = $this->dispatch(FastRoute\simpleDispatcher(require_once 'routes.php'));
        $this->appName = $config['appName'] ?? 'Sample Application';
        // TODO: Move tempdir to env
        $this->__db = $config['db']
            ? new DB($config['db']['dsn'], $config['db']['username'], $config['db']['password'])
            : null;
        $this->__auth = new Auth();
    }

    private function dispatch($dispatcher)
    {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
        return $routeInfo;
    }

    protected static $instance;

    /**
     * Returns singleton instance
     *
     * @param array $config Application configuration
     * @return App
     */
    public static function app($config = [])
    {
        if (null === static::$instance) {
            static::$instance = new static($config);
        }

        return static::$instance;

    }

    public function DB(): DB
    {
        return $this->__db;
    }

    /**
     * @return Auth
     */
    public function auth(): Auth
    {
        return $this->__auth;
    }

    public function redirect($uri, $redirectStatus = 302) {
        return $this->response
            ->withStatus($redirectStatus)
            ->withHeader('Location', (string) $uri);
    }

    public function execute() {
        $this->auth()->authenticate();
        $handler = null;
        $params = [];
        switch ($this->routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                $handler = ['\App\Controllers\ErrorsController', 'notFound'];
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $this->routeInfo[1];
                echo 'Allow: ' . implode(', ', $allowedMethods);
                header('Allow: ' . implode(', ', $allowedMethods), true, 405);
                die();
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $this->routeInfo[1];
                $params = $this->routeInfo[2];
                break;
        }

        $this->request = (new ServerRequest())->withGlobalEnvironment();
        $this->response = (new Response())->withGlobalEnvironment(true);
        if (!Controller::authorize($handler, $params)) {
            $handler = ['\App\Controllers\ErrorsController', 'forbidden'];
        }


        Controller::exec($this->request, $this->response, $handler, $params);
        //$this->response->emit();
    }
}