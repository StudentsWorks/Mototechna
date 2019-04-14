<?php

namespace App\Forms;


use App\App;
use App\Lib\Form;
use App\Lib\FormConnector;

class CustomersForm extends Form
{
    use FormConnector;

    public function rules():array
    {
        return array_merge([
            [
                'empty',
                [
                    ['name', 'email', 'password']
                ]
            ],
            ['mail', ['email']],
        ], parent::rules());
    }


    public function labels(): array
    {
        return [
            'name' => 'Meno',
            'adresa1' => 'Adresa 1',
            'adresa2' => 'Adresa 2',
            'adresa3' => 'Adresa 3',
            'email' => 'Mail',
            'password' => 'Heslo',
        ];
    }
}