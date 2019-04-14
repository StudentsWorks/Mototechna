<?php
namespace App\Lib;


use App\App;

trait FormConnector
{
    protected $__process;

    public function getData(): ?array
    {
        return App::app()->request->getParsedBody();
    }

    public function setCSRF(string $token)
    {
        App::app()->auth()->session('csrf', $token);
    }

    public function getCSRF()
    {
        App::app()->auth()->session('csrf');
    }

    public function __construct(callable $process)
    {
        $this->__process = $process;
        parent::__construct();
    }

    public function process()
    {
        $handler = $this->__process;
        if ($handler) {
            $this->successful = $handler($this);
        }
    }
}