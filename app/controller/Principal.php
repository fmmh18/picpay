<?php

namespace App\controller;
use App\model\Usuario;
use App\model\Loja;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


class Principal extends Controller
{
    public function index(Request $request, Response $response, array $args)
    {
        return $this->view->render($response,'index.twig',['title'=>"Página Inicial"]);
    }
    public function adicionar(Request $request, Response $response, array $args)
    {
        return $this->view->render($response,'cadastro.twig',['title'=>"Cadastrar Usuário"]);
    }
    public function consultar(Request $request, Response $response, array $args)
    {
        $usuariomodel = new Usuario();

        $get = $request->getQueryParams();
        $nome = $get['q'];
        if(empty($nome)){
            $dados = $usuariomodel->listarUsuario();

        }else{
            $dados = $usuariomodel->pesquisar($nome);
        }
        return $this->view->render($response,'consulta.twig',['title'=>"Consultar Usuário",'dados'=>$dados]);
    }

    public function cadastrar(Request $request, Response $response, array $args)
    {
        $usuariomodel = new Usuario();
        $lojamodel = new Loja();

        $post = $request->getParsedBody();

        $nome       = $post['inputNome'];
        $cpf        = $post['inputCPF'];
        $telefone   = $post['inputTelefone'];
        $email      = $post['inputEmail'];
        $usuario    = $post['inputUsuario'];
        $senha      = md5($post['inputSenha']);

        //lojista

        $lojista        = $post['example1'];
        $cnpj           = $post['inputCNPJ'];
        $razaosocial    = $post['inputRazaoSocial'];
        $nomefantasia   = $post['inputNomeFantasia'];


        $dado = array("nome"=>$nome,"cpf"=>$cpf,"telefone"=>$telefone,"email"=>$email,"usuario"=>$usuario,"senha"=>$senha);

        //validar CPF
        $validarcpf = $usuariomodel->validarUsarioCPF($cpf);
        $validaremail = $usuariomodel->validarUsarioEmail($email);
        if($validarcpf == 1){
            $data = array('msg'=>'CPF já cadastrado.','tipo'=>"error");
        }
        else if($validaremail == 1){
            $data = array('msg'=>'E-mail já cadastrado.','tipo'=>"error");
        }else {
            $inserir = $usuariomodel->cadastrarUsario($post);
            if($inserir == 1){
                if($lojista == 1){
                    $last = $usuariomodel->buscarUsuario($cpf);

                    $loja = array("idusuario"=>$last->id,"razaosocial"=>$razaosocial,"nomefantasia"=>$nomefantasia,"cnpj"=>$cnpj);

                    $inserirloja = $lojamodel->cadastrarLoja($loja);
                    if($inserirloja == 1){
                        $data = array('msg'=>"Usuário Cadastrado com sucesso.",'tipo'=>"success");
                    }
                }
            }

        }

        return $this->view->render($response,'cadastro.twig',['title'=>"Cadastrar Usuário",'data'=>$data]);

    }

    //ADMIN
    public function login(Request $request, Response $response, array $args)
    {
        return $this->view->render($response,'admin/login.twig');
    }
    public function logar(Request $request, Response $response, array $args)
    {
        $usuariomodel = new Usuario();
        $post = $request->getParsedBody();

        $login = $post['inputUsuario'];
        $senha = md5($post['inputSenha']);

        $validarlogin = $usuariomodel->validarUsarioLogin($login);
        if($validarlogin == 1){
            $validar = $usuariomodel->validarUsuarioLoginSenha($login,$senha);
            if($validar == 1){
                session_start();
                $res = new \Slim\Http\Response();
                $_SESSION['usuarioLogin'] = $login;
                $_SESSION['sessionID'] = md5(date('d/m/Y H:i:s'));
                //header("location: /admin/principal");

                return $res->withRedirect('/picpay/admin/principal');
               // exit;
            }else{
                $data = array('msg'=>"Usuário não Cadastrado.",'tipo'=>"error");
                $url = "admin/login.twig";
                return $this->view->render($response,$url,['data'=>$data]);
            }
        }else{
            $data = array('msg'=>"Usuário Cadastrado não encontrado e/ou desativado.",'tipo'=>"error");
            $url = "admin/login.twig";
            return $this->view->render($response,$url,['data'=>$data]);
        }

    }
    public function home(Request $request, Response $response, array $args)
    {
        $usuariomodel = new Usuario();
        $res = new \Slim\Http\Response();
        session_start();
        if(!empty($_SESSION['sessionID'])){
            $login =  $_SESSION['usuarioLogin'];
            $info = $usuariomodel->dadosusuario($login);
            return $this->view->render($response,'admin/principal.twig',['header'=>'Dashboard','usuario'=>$info]);
        }else{
            return $res->withRedirect('/picpay/admin/login');
        }

    }

    public function transferir(Request $request, Response $response, array $args)
    {
        $usuariomodel = new Usuario();
        $lojamodel = new Loja();
        $res = new \Slim\Http\Response();
        session_start();
        if(!empty($_SESSION['sessionID'])){
            $login =  $_SESSION['usuarioLogin'];
            $info = $usuariomodel->dadosusuario($login);
            $usuarios = $usuariomodel->listarUsuarioAtivos();
            $lojas = $lojamodel->listarLojaAtivos();
            return $this->view->render($response,'admin/transferir.twig',['header'=>'Transferir','usuario'=>$info,'usuarios'=>$usuarios,'lojas'=>$lojas]);
        }else{
            return $res->withRedirect('/picpay/admin/login');
        }
    }
    public function enviar(Request $request, Response $response, array $args)
    {
        $usuariomodel = new Usuario();
        $lojamodel = new Loja();
        $res = new \Slim\Http\Response();
        $post = $request->getParsedBody();


        $usuariomandate  = $post['usuariomandate'];
        $usuarioreceptor = $post['usuarioreceptor'];
        $lojareceptor    = $post['lojareceptor'];
        $valor           = number_format($post['inputValor'],2,'.','.');

        session_start();
        if(!empty($_SESSION['sessionID'])){
            $login =  $_SESSION['usuarioLogin'];
            $info = $usuariomodel->dadosusuario($login);
            $usuarios = $usuariomodel->listarUsuarioAtivos();
            $lojas = $lojamodel->listarLojaAtivos();

            if($valor >='100.00'){
                $data = array('msg'=>"Valor acima do permitido para trasferência.",'tipo'=>"error");
            }else{
                if($usuariomandate == $usuarioreceptor){
                    $data = array('msg'=>"Você não pode enviar valores para o mesmo.",'tipo'=>"error");
                }else{
                    $transferir = array('mandante'=>$usuariomandate,'receptor'=>$usuarioreceptor,'valor'=>$valor);
                    $inserir = $usuariomodel->transferir($transferir);
                    if($inserir == 1){
                        $data = array('msg'=>"Transação efetuada com sucesso.",'tipo'=>"success");
                    }else{
                        $data = array('msg'=>"Transação não pode ser efetuada.",'tipo'=>"error");
                    }
                }
            }

            return $this->view->render($response,'admin/transferir.twig',['header'=>'Transferir','usuario'=>$info,'usuarios'=>$usuarios,'lojas'=>$lojas,'data'=>$data]);
        }else{
            return $res->withRedirect('/picpay/admin/login');
        }
    }
}