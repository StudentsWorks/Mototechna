<?php

namespace App\Controllers;


use App\Lib\Controller;

class ErrorsController extends Controller
{
    public function forbidden()
    {
        \http_response_code(403);
        $this->title = "Chyba: Prístup zamietnutý";
        return $this->error($this->render('forbidden'));
    }

    public function notFound()
    {
        \http_response_code(404);
        $this->title = "Chyba: Nenájdené";
        return $this->error($this->render('notFound'));
    }
    public function internal(\Exception $exception)
    {

        \http_response_code(500);
        $this->title = "Chyba: Niečo sa nepodarilo";
        return $this->error($this->render('internal', ['error' => $exception]));
    }
}