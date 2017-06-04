<?php

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\View\Simple as SimpleView;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Mvc\Url as UrlProvider;

$di = new FactoryDefault();

// Specify routes for modules
// More information how to set the router up https://docs.phalconphp.com/en/latest/reference/routing.html

 $di->set(
        "url",
        function () {
            $url = new UrlProvider();
            $url->setBaseUri("/apicontrolexpedientes/");
            return $url;
        }
 );

$di->set(
    "router",
    function () {
        
        $router = new Router(false);

        /////////////////////////////////////////////// OBTENGO EL CATALOGO DE MOTIVOS
        $router->addGet("/motivo/motivos_get", [
                'module'     => 'motivo',
                'controller' => 'motivo',
                'action'     => 'motivos_get',
        ]);

        ////////////////////////////////////////////// OBTENGO EL CATALOGO DE CITAS
        $router->addGet("/estatus_cita/estatuscita_get",[
              'module'     => 'estatus_cita',
              'controller' => 'index',
              'action'     => 'estatuscita_get',
        ]);

        ////////////////////////////////////////////// OBTENGO TODAS LAS CITAS 
        $router->addGet("/citas/citas_get",[
              'module'     => 'cita',
              'controller' => 'index',
              'action'     => 'citas_get',
        ]);

        ////////////////////////////////////////////// OBTENGO USUARIOS
        $router->addPost("/usuario/usuario_get",[
              'module'     => 'users',
              'controller' => 'index',
              'action'     => 'usuario_get',
        ]);

        ///////////////////////////////////////////// OBTENGO UNA CITA POR SU ID
        $router->addGet("/citas/cita_get/{type:[0-9]+}",[
              'module'     => 'cita',
              'controller' => 'index',
              'action'     => 'cita_get',
        ]);

        /////////////////////////////////////////////  URL DEL BUSCADOR DE LA CITA ENVIAR 0 COMO PARAMETRO SI NO SE ENVIA NADA EN ESE VALOR
        $router->addGet("/citas/buscador_get/{type:[0-9]+}/{type:[0-9]+}/{type:[0-9]+}/:params",[
              'module'     => 'cita',
              'controller' => 'index',
              'action'     => 'buscadorcitas_get',
              'id_motivo'  => 1,
              'id_paciente'=> 2,
              'id_estatuscita' => 3,  
        ]); 

        ///////////////////////////////////////////// OBTENGO TODOS LOS PACIENTES
        $router->addGet("/paciente/pacientes_get",[
              'module'     => 'paciente',
              'controller' => 'index',
              'action'     => 'pacientes_get',
        ]);

        /////////////////////////////////////////////  OBTENGO EL PACIENTE POR ID 
        $router->addGet("/paciente/paciente_get/{type:[0-9]+}",[
              'module'     => 'paciente',
              'controller' => 'index',
              'action'     => 'paciente_get',
        ]);

        //////////////////////////////////////////// Ruta Para Iniciar Sesion
        $router->addPost("/home/login",[
            'module'     => 'home',
            'controller' => 'index',
            'action'     => 'login',
        ]);

        //////////////////////////////////////////// RUTA PARA CERRAR SESION
        $router->addPost("/home/logout",[
            'module'     => 'home',
            'controller' => 'index',
            'action'     => 'logout',
        ]);

        //////////////////////////////////////////// RUTA FORMULARIO DE REGISTRO
        $router->add("/home/registro",[
              'module'     => 'home',
              'controller' => 'index',
              'action'     => 'registro',
        ]);

        //////////////////////////////////////////// OBTENER CODIGO DE AUTORIZACION
        $router->addGet("/home/autorizar",[
              'module'     => 'home',
              'controller' => 'index',
              'action'     => 'autorizar',
        ]);

        /////////////////////////////////////////// OBTENER TOKEN
        $router->addPost("/home/obtoken",[
              'module'     => 'home',
              'controller' => 'index',
              'action'     => 'token',
        ]);

        //////////////////////////////////////////// GUARDAR USUARIO DE LA APP
        $router->addPost("/home/guarda_datos",[
              'module'     => 'home',
              'controller' => 'index',
              'action'     => 'guardapp',
        ]);

        //////////////////////////////////////////// peticion para consumir api y crear calendario
        $router->add("/home/TestCalendario",[
              'module'     => 'home',
              'controller' => 'index',
              'action'     => 'TestCalendario',
        ]);

        ////////////////////////////////////////////  PARA CUANDO NO COINCIDE NINGUNA URL
        $router->notFound(
            array('module'     => 'motivo',
                  'controller' => 'index', 
                  'action'     => 'index'));

       return $router;
    }
);

/////////////////////////////////////CONEXION A BASE DE DATOS 
$di->set(
        "db",
        function () {
            return new PdoMysql(
                [
                    "host"     => "localhost",
                    "username" => "root",
                    "password" => "",
                    "dbname"   => "control_expedientes",
                ]
            );
        }
    );
/////////////////////////////////////////////////////////////
try{
    // CREO LOS MODULOS
    $application = new Application($di);
    // AQUI PONGO TODOS LOS MODULOS QUE HE HECHO
    $application->registerModules(
        [
            "motivo"=>[
                "className" => "Apicontrolexpedientes\Motivo\Module",
                "path"      => "../apps/motivo/Module.php",
            ],
            "estatus_cita"=>[
                "className" => "Apicontrolexpedientes\Estatus_Cita\Module",
                "path"      => "../apps/estatus_cita/Module.php",
            ],
            "cita"=>[
                'className' => "Apicontrolexpedientes\Cita\Module",
                'path'      => "../apps/cita/Module.php",
            ],
            "paciente"=>[
                'className' => "Apicontrolexpedientes\Paciente\Module",
                'path'      => "../apps/paciente/Module.php",
            ],
            "users"=>[
                'className' => "Apicontrolexpedientes\Usuario\Module",
                'path'      => "../apps/users/Module.php",
            ],
            "home"=>[
                'className' => "Apicontrolexpedientes\Home\Module",
                'path'      => "../apps/home/Module.php",
            ]
        ]
    );
    // Handle the request
    $response = $application->handle();
    $response->send();
}catch(\Exception $e) {
    echo $e->getMessage();
}