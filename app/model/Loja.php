<?php

namespace App\model;


class Loja extends Conexao
{
    public function __construct()
    {
        parent::__construct();
    }
    public function listarLojaAtivos()
    {
        $loja = $this->pdo->query("SELECT a.nomefantasia,a.id as id, a.razaosocial,b.usuario FROM loja a INNER JOIN usuario b ON b.id = a.idusuario WHERE a.status = 1")->fetchAll(\PDO::FETCH_OBJ);
        return $loja;
    }

    public function cadastrarLoja($loja)
    {
        $stmt = $this->pdo->prepare("INSERT INTO loja (idusuario,razaosocial,nomefantasia,cnpj,status) VALUES(:idusuario,:razaosocial,:nomefantasia,:cnpj,:status)");
        $stmt->execute(array(
            ":idusuario"=>$loja['idusuario'],
            ":razaosocial"=>$loja['razaosocial'],
            ":nomefantasia"=>$loja['nomefantasia'],
            ":cnpj"=>$loja['cnpj'],
            ":status"=>1
        ));
        return $stmt->rowCount();

    }

}