<?php

namespace App\controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Principal
{
    public function index(Request $request, Response $response, array $args)
    {

        return $this->view->render($response,'index.twig',['title'=>"Bem Vindo"]);
    }
}