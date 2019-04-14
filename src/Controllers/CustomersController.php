<?php

namespace App\Controllers;

use App\Forms\CustomersForm;
use App\Lib\Auth;
use App\Lib\Controller;

class CustomersController extends Controller
{
    public static function rules()
    {
        return [
            'index' => [Auth::requireLoggedIn()],
        ];
    }

    public function index()
    {
        $context = $this->app->DB()->getContext();

        $this->title = "Zákazníci";
        $customers = $context->table('zakaznik');
        return $this->success($this->render('index', [
            'model' => $customers,
        ]));
    }

    public function register()
    {
        $context = $this->app->DB()->getContext();
        $form = new CustomersForm(function (CustomersForm $form) use ($context) {
            $customer = $context->table('zakaznik')->where('email', $form->data['email'])->fetch();
            if ($customer) {
                $form->addError('email', 'Tento email sa už používa');
                return false;
            }
            var_dump($form->data['password']);
            $context->table('zakaznik')->insert([
                'meno' => $form->data['name'],
                'adresa1' => $form->data['adresa1'],
                'adresa2' => $form->data['adresa2'],
                'adresa3' => $form->data['adresa3'],
                'email' => $form->data['email'],
                'password' => password_hash($form->data['password'], PASSWORD_BCRYPT),
            ]);
            return true;
        });
        $this->title = "Registrácia";

        return $this->success($this->render('register', [
            'form' => $form,
        ]));
    }
}
