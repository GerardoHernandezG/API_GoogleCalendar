<?php

namespace Apicontrolexpedientes\Home\Controllers;

use Phalcon\Mvc\View\Simple;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use App\Libraries\RespuestaJson;
//////////////////////////////////////////////MODELOSS
use Apicontrolexpedientes\Home\Models\Usuarios;
use Apicontrolexpedientes\Home\Models\App;
use Apicontrolexpedientes\Home\Models\Token;
////////////////////////////////////////////
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Assets\Manager;
use Phalcon\Mvc\Url;
use Phalcon\Tag;
use OAuth2;
use Phalcon\Loader;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
//use Phalcon\Mvc\Model\Query\Builder

use App\Libraries\CalendarioGoogle;


class IndexController extends \Phalcon\Mvc\Controller
{
    public function indexAction()
    {
       $this->assets->addCss("css/bootstrap.css");
       $this->assets->addCss("css/style.css");
       $this->assets->addJs("js/jquery-1.11.2.min.js");
       $this->assets->addJs("js/bootstrap.js");
    }

    public function loginAction()
    {

      $this->view->disable();
      require_once '../apps/oauthcargador/server.php';
      $response = new Response();
      $response->setContentType('text/plain', 'UTF-8');
      $datos = $this->request;
      $response = RespuestaJson::revisa_tokenheader($datos);
      if($response['estado'] == true){
          ////verifico si el token aun es valido 
           $tokenapp = $response['data'];  
           $username = $this->request->getPost('username');
           $secret = $this->request->getPost('client_secret');

             if($username == ''){
              $response = RespuestaJson::error($this->request->getMethod(),404,$this->request->getUri(),500,'Username es requerido');
                  return $response;
             }

             if($secret == ''){
              $response = RespuestaJson::error($this->request->getMethod(),404,$this->request->getUri(),500,'client_secret es requerido');
                  return $response;
             }

             $datosdelusuario = Usuarios::findFirst(['username = "'.$username.'"','client_secret = "'.$secret.'"']);

            if($datosdelusuario === false){
              $response = RespuestaJson::error($this->request->getMethod(),404,$this->request->getUri(),500,'Credenciales no validas.');
                return $response;
            }else{

                $datosdeltoken = Token::find('client_id = "'.$datosdelusuario->client_id.'"')->getFirst();
                /////////////////////////////////// Pregunto si ese usuario Tiene algun token
                if($datosdeltoken === false){
                /////////////////////////////////// Si no tiene token le creo un token automaticamente 
                  $_POST['client_id'] = $datosdelusuario->client_id;
                  $_POST['token_app'] = $tokenapp;
                  $server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
                }else{
                 ////////////////////////////////// si tiene un token verifico si ya expiro o no
                  $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                  if($fecha_actual > strtotime($datosdeltoken->expires)){
                    //// si la fecha actual es mayor le genero un token.
                    $expires = time() + 3600;
                    $tokenuevo = $storage->setAccessToken($datosdeltoken->access_token,$datosdelusuario->client_id,'',$expires,'');
                    print_r($tokenuevo);
                  }else{
                    //// le dejo el token existente
                    $token = array('access_token' => $datosdeltoken->access_token,
                                   'expires_in' => $datosdeltoken->expires,
                                   'client_id' => $datosdeltoken->client_id);
                    print_r($token);
                  }
                }
            }
        }else{
          return $response['data'];
        }
    }

    public function logoutAction(){
        require_once '../apps/oauthcargador/server.php';
        $response = new Response();
        $response->setContentType('text/plain', 'UTF-8');
        $this->view->disable();
        $datos = $this->request;
        $response = RespuestaJson::revisa_tokenheader($datos);
        if($response['estado'] == true){
          $respuesta = RespuestaJson::revisa_tokensesion($datos);
          if($respuesta['estado'] == true){
              $logout= $storage->unsetAccessToken($respuesta['data']);
              if($logout){
                  $data = 'Sesion Finalizada';
                  $response = RespuestaJson::bien($this->request->getMethod(),200,$this->request->getUri(),$data);
                  return $response;
              }else{
                $response = RespuestaJson::error($this->request->getMethod(),404,$this->request->getUri(),500,'Ocurrio un problema intentelo más tarde.');
                return $response;
              }
          }else{
            return $respuesta['data'];
          }
        }else{
          return $response['data'];
        }
    }

    public function TestCalendarioAction(){
        CalendarioGoogle::Crear_Calendario('2', 'Calendario Test', 'Calendario Prueba');
    }

    /*public function autorizarAction()
    {
        $this->view->disable();
        require_once '../apps/oauthcargador/server.php'; 

        $request = OAuth2\Request::createFromGlobals(); 
        $response = new OAuth2\Response();      
        
        // valido que la petición sea valida
        if (!$server->validateAuthorizeRequest($request, $response)) {  
            $response->send();
            die;
        }

        $is_authorized = true;

        $valor = $server->handleAuthorizeRequest($request, $response, $is_authorized);
        if ($is_authorized) {
           $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);
        } 

        $codigo = array('codigo'=>$code);
        $datos = json_encode(array('data' => $codigo));
        return $datos;
    }*/



    /*public function registroAction()
    {  

      //////////////////////////////////////////////////////////// Validación de los campos
      $validation = new Validation();
      $validation->add("correo",
          new PresenceOf([
                    "message" => "El campo email es requerido",
          ])
      );
      $validation->add('correo',
        new Email([
                    "message" => "Tiene que ser un correo valido",
                    "allowEmpty" => true,
          ])
      );
      $validation->add('password',
        new PresenceOf([
                    "message" => "El campo password es requerido",
          ])
      );
      $validation->setFilters("correo", "trim");
      $validation->setFilters("password","trim");

      $this->assets->addCss("css/bootstrap.css");
      $this->assets->addCss("css/style.css");
      $this->assets->addJs("js/jquery-1.11.2.min.js");
      $this->assets->addJs("js/bootstrap.js");

      //////////////////////////////////////////////////////// Inicializo los datos 
       $client_id = '';
       $oculto = 'hidden';
       $token = '';

      //////////////////////////////////////////////////// Pregunto si envio algo por POST
       if($this->request->getPost()){
          $messages = $validation->validate($_POST);
          if(count($messages) > 0){
              foreach ($messages as $message) {
                  echo $message, "<br>";
              }
          }else{
             $client_id = md5(uniqid(rand(), true));
             
             $client = new Usuario();
             $guardar = array('client_id' => $client_id,
                              'client_secret' => $this->request->getPost('password'),
                              'redirect_uri' => 'er');
             $success = $client->save(
                  $guardar
             );

             $curl = new \curl();
             $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
             $curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
             $curl->setHeader('Content-Type', 'application/json');
             $curl->setHeader('Cache-Control','no-cache');
             $curl->get('http://localhost/apicontrolexpedientes/home/autorizar?response_type=code&client_id='.$client_id.'&state=xyz');
             $datos = obtgrespuesta($curl);
             if(count($datos['error'])>0){
                print_r($datos['error']);
                exit();
             }else{
                $token = $datos['data']->codigo;
             }
          }
       }
      //////////////////////////////////////////////////// Campos del Formulario
       $correo = Tag::emailField(array("correo",'id'=>'email','placeholder' => 'correo','class' => 'form-control','value' => $this->request->getPost('correo')));
       $password = Tag::passwordField(array("password",'id'=>'password','placeholder' => 'contraseña','class' => 'form-control','value' => $this->request->getPost('password')));
       $nombre = Tag::textField(array("nombre",'id'=>'nombre','placeholder' => 'nombre','class' => 'form-control','value' => $this->request->getPost('nombre')));
       $apellido = Tag::textField(array("apellido",'id'=>'apellido','placeholder' => 'apellido','class' => 'form-control','value' => $this->request->getPost('apellido')));
       $empresa = Tag::textField(array("empresa",'id'=>'empresa','placeholder' => 'empresa','class' => 'form-control','value' => $this->request->getPost('empresa')));
       $token = Tag::textArea(array("token",'id'=>'token','placeholder' => 'token','class' => 'form-control','value' => $token,"rows" => 8));
       $clientid = Tag::textField(array("client_id",'id'=>'client_id','placeholder' => 'client_id','class' => 'form-control','value' => $client_id));
       ////////////////////////////////////////////////// Declaro los parametros que le enviare a la vista
       $this->view->setVars(
          [
            'correo' => $correo,
            'password' => $password,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'empresa' => $empresa,
            'token' => $token,
            'client_id' => $client_id,
          ]
      );
    }*/
}

