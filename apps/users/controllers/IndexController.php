<?php

namespace Apicontrolexpedientes\Usuario\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use App\Libraries\RespuestaJson;
use Apicontrolexpedientes\Cita\Models\Usuario;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\Resultset\Simple;
use OAuth2;


class IndexController extends \Phalcon\Mvc\Controller
{
    public function indexAction()
    {
       
    }

    public function usuario_getAction()
    {
        require_once '../apps/oauthcargador/server.php';
        //$token = $server->accessToken();
        //echo $token;
        exit();
        // Con este creo un token para el usuario y la contraseña que envie 
        
        $server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
        exit();
    }
}