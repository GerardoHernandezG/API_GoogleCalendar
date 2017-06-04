<?php

namespace Apicontrolexpedientes\Usuario\Models;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Resultset\Simple;

class Usuario extends Model
{
	///DIGO QUE TABLA VA A UTILIZAR EL MODELO EN ESTE CASO ESTOY UTILIZANDO UNA VISTA
	///PARA NO HACER JOINS
	public function initialize()
    {
        $this->setSource("users");
    }
}