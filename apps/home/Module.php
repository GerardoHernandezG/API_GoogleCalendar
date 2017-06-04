<?php 

    namespace Apicontrolexpedientes\Home;

    use Phalcon\Loader;
    use Phalcon\Mvc\View;
    use Phalcon\Mvc\Dispatcher;
    use Phalcon\DiInterface;
    use Phalcon\Mvc\ModuleDefinitionInterface;
    use Phalcon\Db\Adapter\Pdo\Mysql as Database;
    use Phalcon\Assets\Manager;

class Module implements ModuleDefinitionInterface{
    ///INICIALIZO LOS CONTROLADORES Y MODELOS DEL MODULO
    public function registerAutoloaders(DiInterface $di = null)
    {
        ///ESPECIFICO LA RUTA DE LOS CONTROLADORES Y MODELOS
        $loader = new Loader();
        $loader->registerNamespaces(
            [
                "Apicontrolexpedientes\Home\Controllers" => "../apps/home/controllers/",
                "Apicontrolexpedientes\Home\Models"      => "../apps/home/models/",
                'App\Libraries' => '../apps/librerias/',
                //'OAuth2\Storage' => '../apps/OAuth2',
            ]
        );

        $loader->registerClasses(
            [
                "curl"  => "../apps/librerias/Curl.php",
                'CaseInsensitiveArray' => "../apps/librerias/CaseInsensitiveArray.php",
                //'Storage' => "../apps/OAuth2/Storage/Pdo.php",
            ]
        );

        $loader->registerFiles(
            [
                "../apps/librerias/respuestasapi_helper.php",
                //"../apps/oauthcargador/server.php",
            ]
        );

        $loader->register();
    }

    /*** Register specific services for the module*/
    public function registerServices(DiInterface $di)
    {
        // Registering a dispatcher
        $di->set(
            "dispatcher",
            function () {
                $dispatcher = new Dispatcher();
                $dispatcher->setDefaultNamespace("Apicontrolexpedientes\Home\Controllers");
                return $dispatcher;
            }
        );
        //ESPECIFICO DONDE SE ENCUENTRAN LAS VISTAS DEL MODULO
        $di->set(
            "view",
            function () {
                $view = new View();
                $view->setViewsDir("../apps/home/views/");
                $view->setLayoutsDir('../../layouts/layoutshome/');
                $view->setTemplateBefore('template');
                $view->registerEngines(array(
                   ".phtml" => 'Phalcon\Mvc\View\Engine\Volt'
                ));
                return $view;
            }
        );
    }
}