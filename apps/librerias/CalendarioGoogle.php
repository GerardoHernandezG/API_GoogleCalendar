<?php 

namespace App\Libraries;

require_once('../vendor/autoload.php');

use Phalcon\Db\Adapter\Pdo\Mysql;
use App\Libraries\Config_Database;


class CalendarioGoogle {

	public static function get_Cliente($id_usuario){

	  $conexion = new ConfigDatabase();
	
	  $query = $conexion->db()->query("select * FROM auth_calendario where id_usuario = ".$id_usuario)->fetchAll()[0]; 

	  $clientData = json_decode($query['client_secret'], true);

   	  $client = new \Google_Client();
	  $client->setApplicationName('ControlExpedientes');
	  //$client->setAuthConfigFile($resultset); //Si se toma de archivo json
	  $client->setClientId($clientData['web']['client_id']);  //Se toman los datos de la bd
	  $client->setClientSecret($clientData['web']['client_secret']);  //Se toman los datos de la bd
	  $client->setRedirectUri($query['redirect_url']);
	  $client->setScopes(array('https://www.googleapis.com/auth/calendar'));		
      $client->setAccessType('offline');

      //Detecto la recarga de la pagina para recibir el codigo retornado de la funcion createAuthUrl 
   	  if(isset($_GET) && isset($_GET['code'])){	

		$code = $_GET['code'];	

		$accessToken = $client->fetchAccessTokenWithAuthCode($code);
		$client->setAccessToken(json_encode($accessToken)); 

		// Refresh the token if it's expired.
		if ($client->isAccessTokenExpired()) {
		  $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
		  json_encode($client->getAccessToken());
		}

		return $client;			

	  }//Si no hay respuesta del server de la api, entonces se hace el request par solicitar el token
	  else{	

	  	//configurado previamente el objeto cliente de Google, le mandamos el client_id, y el redirect url, necesarios para el response			
		header('Location: '.$client->createAuthUrl());  
	  }
	} 

   public static function Crear_Calendario($id_usuario, $titulo, $descripcion){ 

   		$client = self::get_Cliente($id_usuario);   	   		

	    $service = new \Google_Service_Calendar($client);

	    if(self::getCalendarios($id_usuario, $titulo, $client, $service) == 1){
	    	//echo 'El calendario ya existe';  
	    	return false;
	    }else{    		    
			
			// Crear un nuevo calendario
			$calendar = new \Google_Service_Calendar_Calendar();
			$calendar->setSummary($titulo);
			$calendar->setDescription($descripcion);
			$calendar->setTimeZone('America/Mexico_City');	

			$createdCalendar = $service->calendars->insert($calendar);

			if($createdCalendar){
				$calendarId = $createdCalendar->getId();  

				$conexion = new ConfigDatabase();    		

		   		$res = $conexion->db()->execute("update auth_calendario SET id_calendario = ? WHERE id_usuario = ?",array($calendarId,$id_usuario));
		   		if (!$res) {
		            //echo 'Error al crear el calendario';
		            return false;
		        } else {
		            //echo 'Calendario creado, el ID es: '.$calendarId;
		            return true;
		        }	  
			}else{
				//echo 'Error al crear el calendario';
				return false;
			}
	    } 	  	   

   }

   public static function Crear_Cita($id_usuario, $nombreEvento, $fecha_inicio, $fecha_fin, $correo_paciente){

   		$client = self::get_Cliente($id_usuario);   	

	    $service = new \Google_Service_Calendar($client);
	
		//Crear evento del calendario
	    $event = new \Google_Service_Calendar_Event();
		$event->setSummary($nombreEvento);
		$event->setDescription('Test Descripción evento');
		$event->setLocation('Colima');
		$event->setVisibility("default");

		// Fecha de inicio del evento
		$date = new \DateTime($fecha_inicio, new \DateTimeZone("America/Mexico_City"));
		$start = new \Google_Service_Calendar_EventDateTime();
		$start->setDateTime($date->format("Y-m-d")."T08:00:00");  //Checar hora de inicio
		$start->setTimeZone("America/Mexico_City");
		$event->setStart($start);

		//Fecha de fin del evento
		$date = new \DateTime($fecha_fin, new \DateTimeZone("America/Mexico_City"));
		$end = new \Google_Service_Calendar_EventDateTime();
		$end->setDateTime($date->format("Y-m-d")."T18:00:00");   //checar hora fin
		$end->setTimeZone("America/Mexico_City");
		$event->setEnd($end);

		//Establecer recordatorios
		$Arrayrecordatorios = array();
		$recordatorio = new \Google_Service_Calendar_EventReminder();
		$recordatorio->setMethod("email");
		$recordatorio->setMinutes(25);
		$Arrayrecordatorios[] = $recordatorio;

		$recordatorio = new \Google_Service_Calendar_EventReminder();
		$recordatorio->setMethod("popup");
		$recordatorio->setMinutes(15);
		$Arrayrecordatorios[] = $recordatorio;

		$recordatorio = new \Google_Service_Calendar_EventReminders();
		$recordatorio->setUseDefault(false);
		$recordatorio->setOverrides($Arrayrecordatorios);
		$event->setReminders($recordatorio);

   		//Para agregar participantes al evento
		$participantesArray = array();
		$attendee = new \Google_Service_Calendar_EventAttendee();	

	    $attendee->setEmail($correo_paciente);
	    //$attendee->setEmail($correo2);
		
		$participantesArray[] = $attendee;
		$event->attendees = $participantesArray;

		$conexion = new ConfigDatabase();

		$calendarId = $conexion->db()->query("select id_calendario FROM auth_calendario where id_usuario = ".$id_usuario)->fetch()['id_calendario'];

		// Guardar el evento en el calendario seleccionado
		$createdEvent = $service->events->insert($calendarId, $event, array("sendNotifications" => true));		

		if($createdEvent){
						
			$res = $conexion->db()->execute("insert into cat_eventos_calendario(id_auth_calendario, id_evento) values((select id from auth_calendario where id_usuario = ?), ?)",array($id_usuario, $createdEvent->getId()));
	   		if (!$res) {
	            //echo 'Error al crear la cita';
	            return false;
	        } else {
	            //echo "Cita creada con ID: ".$createdEvent->getId();
	            return true;
	        }
		}else{
			//echo 'Error al crear la cita';
			return false;
		}		

   }

   public static function Actualizar_Cita($id_usuario, $id_evento, $titulo, $descripcion, $fecha_inicio, $fecha_fin){

   		$cliente = self::get_Cliente($id_usuario);

	    $servicio = new \Google_Service_Calendar($cliente);	 

	    $event = $servicio->events->get('primary', $id_evento);

		$event->setSummary($titulo);
		$event->setDescription($descripcion);
		$event->setLocation('Colima');

		//Cambiar fecha de inicio
		$date = new \DateTime($fecha_inicio, new \DateTimeZone("America/Mexico_City"));
		$start = new \Google_Service_Calendar_EventDateTime();
		$start->setDateTime($date->format("Y-m-d")."T08:00:00");
		$start->setTimeZone("America/Mexico_City");
		$event->setStart($start);

		//Cambiar fecha fin
		$date = new \DateTime($fecha_fin, new \DateTimeZone("America/Mexico_City"));
		$end = new \Google_Service_Calendar_EventDateTime();
		$end->setDateTime($date->format("Y-m-d")."T18:00:00");
		$end->setTimeZone("America/Mexico_City");
		$event->setEnd($end);

		$updatedEvent = $servicio->events->update('primary', $event->getId(), $event);		

		if($updatedEvent){
			return true;  //'Cita Actualizada'.$updatedEvent->getUpdated();
		}else{
			return false;
		}
   }

    public static function Eliminar_Cita($id_usuario, $id_evento){   	   

   		$client = self::get_Cliente($id_usuario);   	

	    $service = new \Google_Service_Calendar($client);		

	  	if($service->events->delete('primary', $id_evento)){	  		
   			return true;  //'Cita Eliminada';
	  	}else{
	  		return false;
	  	} 
   		
    }

   public static function Eliminar_Calendario($id_usuario){   	   

   		$client = self::get_Cliente($id_usuario);   	

	    $service = new \Google_Service_Calendar($client);

	    $conexion = new ConfigDatabase();

	  	$calendarId = $conexion->db()->query("select id_calendario FROM auth_calendario where id_usuario = ".$id_usuario)->fetch()['id_calendario'];

   		if($service->calendars->delete($calendarId)){
   			return true; //'Calendario Eliminado';
	  	}else{
	  		return false;
	  	}
   		
   }

   public static function getCalendarios($id_usuario, $nombre_calendario, $client, $service){   

   		$calendarList = $service->calendarList->listCalendarList();

		$calendarios = array();

		while(true) {
		  foreach ($calendarList->getItems() as $calendarListEntry) {
		    $calendarios[] = $calendarListEntry['summary'];	
		  }

		  $pageToken = $calendarList->getNextPageToken();
		  if ($pageToken) {
		    $optParams = array('pageToken' => $pageToken);
		    $calendarList = $service->calendarList->listCalendarList($optParams);
		  } else {
		    break;
		  }
		}
		
		if(in_array($nombre_calendario, $calendarios)){
		   //echo 'El calendario ya existe';
			return true;
		}
		else{
		    //echo 'No existe';
		    return false;
	    }

   }

}