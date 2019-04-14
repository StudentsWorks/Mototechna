<?php

namespace App\Forms;


use App\App;
use App\Lib\Form;
use App\Lib\FormConnector;

class OrdersForm extends Form
{
    use FormConnector;

    public function rules():array
    {
        return array_merge([
            [
                'empty',
                [
                    ['preprava']
                ]
            ],
        ], parent::rules());
    }


    public function labels(): array
    {
        return [
            'preprava' => 'Spôsob prepravy',
        ];
    }
}