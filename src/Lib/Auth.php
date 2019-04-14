<?php


namespace App\Lib;


use App\App;

class Auth
{

    public $user;

    public function __construct()
    {
        session_start();
    }

    public function authenticate()
    {
        if (isset($_SESSION['userId'])) {
            $context = App::app()->DB()->getContext();
            $customer = $context->table('zakaznik')->get($_SESSION['userId']);
            if ($customer) $this->user = $customer;
        }
    }

    public function login($email, $password)
    {
        $context = \App\App::app()->DB()->getContext();
        $customer = $context
            ->table('zakaznik')
            ->where('email', $email)
            ->fetch();
        if (!$customer) return false;

        var_dump([$password, $customer->password]);
        if (!password_verify($password, $customer->password)) {
            return false;
        }

        $_SESSION['userId'] = $customer->id;
        $this->authenticate();
        return true;
    }

    public function logout()
    {
        $this->user = null;
        unset($_SESSION['userId']);
        session_destroy();
    }

    public function isLoggedIn() {
        return isset($this->user) && $this->user !== null;
    }

    public function session($key, $value = null)
    {
        if (isset($value)) {
            $_SESSION[$key] = $value;
        }
        return $_SESSION[$key] ?? null;
    }


    public static function requireLoggedIn($redirect = false)
    {
        return function (Auth $auth) use ($redirect) {
            if ($redirect && !$auth->isLoggedIn()) {
                die(App::app()->redirect(is_string($redirect) ? $redirect : '/login'));
            }
            return $auth->isLoggedIn();
        };
    }
}