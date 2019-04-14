<?php
/**
 * Created by PhpStorm.
 * User: pato1
 * Date: 13/01/2019
 * Time: 17:57
 */

namespace App\Lib;

use App\App;

class View {
    protected $filename;
    protected $data;
    public $app;

    function __construct($filename, $data = []) {
        $this->filename = $filename;
        $this->data = $data;
        $this->app = App::app();
    }

    function escape($str) {
        return htmlspecialchars( $str );
    }

    function __get($name) {
        if (isset( $this->data[$name])) {
            return $this->data[$name];
        }
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return false;
    }

    function __set( $name, $value ) {
        $this->data[$name] = $value;
    }

    function render($print = true) {
        ob_start();
        include($this->filename);
        $rendered = ob_get_clean();
        if($print) {
            echo $rendered;
            return null;
        }
        return $rendered;
    }

    function __toString() {
        return $this->render(false);
    }

    function renderPartial($partial, $data, $print = true) {
        $filename = dirname($this->filename) . '_' . $partial . '.php';
        (new self($filename, $data))->render($print);
    }
}
