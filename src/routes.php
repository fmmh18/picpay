<?php

use App\controller\Principal;
use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    $container['Principal'] = function($container) use ($app)
    {
        return new app\controller\Principal($container);
    };

    $app->get('/',Principal::class.":index");
    $app->get('/adicionar',Principal::class.":adicionar");
    $app->post('/adicionar',Principal::class.":cadastrar");
    $app->get('/consultar',Principal::class.":consultar");
    $app->get('/transferir',Principal::class.":transferir");
    $app->get('/admin/login',Principal::class.":login");
    $app->post('/admin/login',Principal::class.":logar");
    $app->get("/admin/principal",Principal::class.":home");
    $app->get("/admin/transferir",Principal::class.":transferir");
    $app->post("/admin/transferir",Principal::class.":enviar");

};
