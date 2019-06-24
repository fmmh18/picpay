<?php

namespace App\model;


class Usuario extends Conexao
{
    public function __construct()
    {
        parent::__construct();
    }
    public function validarUsarioCPF($cpf)
    {
        $usuario = $this->pdo->query("SELECT * FROM usuario WHERE cpf = '".$cpf."'")->rowCount();
        return $usuario;
    }
    public function validarUsarioEmail($email)
    {
        $usuario = $this->pdo->query("SELECT * FROM usuario WHERE email = '".$email."'")->rowCount();
        return $usuario;
    }
    public function cadastrarUsario($dado)
    {
        $stmt = $this->pdo->prepare("INSERT INTO usuario (nome,cpf,telefone,email,usuario,senha,status) VALUES(:nome,:cpf,:telefone,:email,:usuario,:senha,:status)");
        $stmt->execute(array(
            ":nome"=>$dado['nome'],
            ":cpf"=>$dado['cpf'],
            ":telefone"=>$dado['telefone'],
            ":email"=>$dado['email'],
            ":usuario"=>$dado['usuario'],
            ":senha"=>$dado['senha'],
            ":status"=>1
        ));
        return $stmt->rowCount();
    }

    public function buscarUsuario($cpf)
    {
        $usuario = $this->pdo->query("SELECT * FROM usuario WHERE cpf = '".$cpf."'")->fetchObject(\PDO::FETCH_OBJ);
        return $usuario;
    }
    public function pesquisar($nome)
    {
        $usuario = $this->pdo->query("SELECT a.id as id, a.nome as nome,a.email as email,a.usuario as usuario, b.nomefantasia as nomefantasia, b.razaosocial as razaosocial FROM usuario a 
                                                LEFT JOIN loja b ON b.idusuario = a.id 
                                                WHERE (a.nome LIKE '".$nome."%' OR b.razaosocial LIKE '".$nome."%' 
                                                OR b.nomefantasia LIKE '".$nome."%')")->fetchAll(\PDO::FETCH_OBJ);
        return $usuario;
    }
    public function listarUsuario()
    {
        $usuario = $this->pdo->query("SELECT a.id as id, a.nome as nome,a.email as email,a.usuario as usuario, b.nomefantasia as nomefantasia, b.razaosocial as razaosocial FROM usuario a LEFT JOIN loja b ON b.idusuario = a.id ")->fetchAll(\PDO::FETCH_OBJ);
        return $usuario;
    }
    public function validarUsarioLogin($login)
    {
        $usuario = $this->pdo->query("SELECT * FROM usuario WHERE usuario = '".$login."' AND status = 1")->rowCount();
        return $usuario;
    }
    public function validarUsuarioLoginSenha($login,$senha)
    {
        $usuario = $this->pdo->query("SELECT * FROM usuario WHERE usuario = '".$login."' AND senha = '".$senha."' AND status = 1")->rowCount();
        return $usuario;
    }
    public function dadosusuario($login)
    {
        $usuario = $this->pdo->query("SELECT * FROM usuario WHERE usuario = '".$login."'")->fetchAll(\PDO::FETCH_OBJ);
        return $usuario;
    }

    public function listarUsuarioAtivos()
    {
        $usuario = $this->pdo->query("SELECT * FROM usuario WHERE status = 1")->fetchAll(\PDO::FETCH_OBJ);
        return $usuario;
    }
    public function transferir($transferir)
    {
        $stmt = $this->pdo->prepare("INSERT INTO transacao (mandante,receptor,valor,status) VALUES(:mandante,:receptor,:valor,:status)");
        $stmt->execute(array(
            ":mandante"=>$transferir['mandante'],
            ":receptor"=>$transferir['receptor'],
            ":valor"=>$transferir['valor'],
            ":status"=>1
        ));
        return $stmt->rowCount();
    }
}