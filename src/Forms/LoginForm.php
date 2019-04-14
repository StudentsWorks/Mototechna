<?php

namespace App\Forms;


use App\Lib\Form;
use App\Lib\FormConnector;

class LoginForm extends Form
{
    use FormConnector;

    public function rules():array
    {
        return array_merge([
            [
                'empty',
                [
                    ['email', 'password']
                ]
            ],
            ['mail', ['email']],
        ], parent::rules());
    }

    public function labels(): array
    {
        return [
            'email' => 'Mail',
            'password' => 'Heslo',
        ];
    }
}