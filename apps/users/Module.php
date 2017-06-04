<?php 

    namespace Apicontrolexpedientes\Usuario;

    use Phalcon\Loader;
    use Phalcon\Mvc\View;
    use Phalcon\Mvc\Dispatcher;
    use Phalcon\DiInterface;
    use Phalcon\Mvc\ModuleDefinitionInterface;
    use Phalcon\Db\Adapter\Pdo\Mysql as Database;

class Module implements ModuleDefinitionInterface{
    ///INICIALIZO LOS CONTROLADORES Y MODELOS DEL MODULO
    public function registerAutoloaders(DiInterface $di = null)
    {
        ///ESPECIFICO LA RUTA DE LOS CONTROLADORES Y MODELOS
        $loader = new Loader();
        $loader->registerNamespaces(
            [
                "Apicontrolexpedientes\Usuario\Controllers" => "../apps/users/controllers/",
                "Apicontrolexpedientes\Usuario\Models"      => "../apps/users/models/",
                'App\Libraries' => '../apps/librerias/',
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
                $dispatcher->setDefaultNamespace("Apicontrolexpedientes\Usuario\Controllers");
                return $dispatcher;
            }
        );
        //ESPECIFICO DONDE SE ENCUENTRAN LAS VISTAS DEL MODULO
        $di->set(
            "view",
            function () {
                $view = new View();
                $view->setViewsDir("../apps/users/views/");
                return $view;
            }
        );
    }
}