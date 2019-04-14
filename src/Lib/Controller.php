<?php

namespace App\Lib;


use App\App;
use Exception;
use Jasny\HttpMessage\Response;
use Jasny\HttpMessage\ServerRequest;

class Controller
{
    public $success = false;
    public $title = null;
    public $request;
    public $response;
    public $layout = 'layout';
    public $app;

    public function __construct(ServerRequest $request, Response $response)
    {
        $this->app = App::app();
        $this->request = $request;
        $this->response = $response;
    }

    public static function rules() {
        return [];
    }

    public static function exec(ServerRequest &$request, Response &$response, $handler, $params = []): bool
    {
        // TODO: Add support for other handler styles
        $controller = new $handler[0]($request, $response);
        if (!is_callable([$controller, $handler[1]])) {
            die('Undefined action!');
        }

        $output = null;
        App::app()->DB()->begin();
        try {
            $output = call_user_func_array([$controller, $handler[1]], $params);
            App::app()->DB()->commit();
        } catch (Exception $exception) {
            App::app()->DB()->rollback();
            $handler = ['\App\Controllers\ErrorsController', 'internal'];
            $controller = new $handler[0]($request, $response);
            $output = call_user_func_array([$controller, $handler[1]], [$exception]);
        }

       /* if ($controller->success) {
            App::app()->DB()->commit();
        } else {
            App::app()->DB()->rollback();
        }*/

        $response->getBody()->write((string) $output);
        return $controller->success;
    }

    public static function authorize($handler, $params = [])
    {
        $rules = $handler[0]::rules();
        if (isset($rules['*'])) {
            foreach ($rules['*'] as $rule) {
                if (!$rule(App::app()->auth(), $params)) return false;
            }
        }
        if (!isset($rules[$handler[1]])) return true;
        foreach ($rules[$handler[1]] as $rule) {
            if (!$rule(App::app()->auth(), $params)) return false;
        }
        return true;
    }

    protected function error($res) {
        $this->success = false;
        return $res;
    }

    protected function success($res) {
        $this->success = true;
        return $res;
    }

    protected function render($view, $data = []) {
        $paths = [
            '../Views/' . $this->getControllerName() . '/' . $view . '.php',
            '/../Views/' . $this->getControllerName() . '/' . $view,
            '/../Views/' . $view . '.php',
            '/../Views/' . $view . '',
        ];
        $filename = null;
        foreach ($paths as $path) {
            if (realpath(__DIR__ . '/' . $path)) {
                $filename = realpath(__DIR__ . '/' . $path);
                break;
            }
        }
        if (!$filename) {
            throw new \Error('View not found');
        }
        $view = new View($filename, $data);
        $layoutPath = realpath(__DIR__ . '/../Views/' . $this->layout . '.php');
        $layout = new View($layoutPath, ['content'=> $view]);
        $layout->title = $this->title ? $this->title . ' - ' . App::app()->appName : App::app()->appName;
        return $layout;
    }

    protected function redirect($uri, $redirectStatus = 302) {
        return $this->app->redirect($uri, $redirectStatus);
    }

    private function getControllerName()
    {
        $matches = [];
        preg_match('/^(.*)\\\\(\w+)Controller$/', get_called_class(), $matches);
        return $matches[2];
    }
}