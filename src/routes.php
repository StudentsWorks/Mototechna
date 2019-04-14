<?php
return function(FastRoute\RouteCollector $r) {
    $r->addRoute(['GET', 'POST'], '/', ['\App\Controllers\StoreController', 'index']);
    $r->addRoute(['GET', 'POST'], '/kosik', ['\App\Controllers\StoreController', 'cart']);
    $r->addRoute(['GET'], '/kontakt', ['\App\Controllers\StoreController', 'contact']);
    $r->addRoute(['GET', 'POST'], '/zakaznici', ['\App\Controllers\CustomersController', 'index']);
    $r->addRoute(['GET', 'POST'], '/registracia', ['\App\Controllers\CustomersController', 'register']);
    $r->addRoute(['GET', 'POST'], '/login', ['\App\Controllers\AuthController', 'login']);
    $r->addRoute(['POST'], '/logout', ['\App\Controllers\AuthController', 'logout']);
};