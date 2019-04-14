<?php

namespace App\Controllers;

use App\Forms\LoginForm;
use App\Lib\Auth;
use App\Lib\Controller;

class AuthController extends Controller
{
    public static function rules()
    {
        return [
            //'index' => [Auth::requireLoggedIn(true)],
        ];
    }
    public function login()
    {
        $form = new LoginForm(function (LoginForm $form) {
            if($this->app->auth()->login($form->data['email'], $form->data['password'])) {
                return true;
            } else {
                $form->addError('password', 'Nesprávne prihlasovacie údaje');
            }
        });
        $this->title = "Prihlásenie";
        if ($this->app->auth()->isLoggedIn()) return $this->redirect('/');
        return $this->success($this->render('login', [
            'form' => $form,
        ]));
    }

    public function logout()
    {
        $this->title = "Ohlásenie";
        if ($this->app->auth()->isLoggedIn()) {
            $this->app->auth()->logout();
        }
        return $this->redirect('/');
    }
}
