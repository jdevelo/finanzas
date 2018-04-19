<?php 

	/**
	* Consultas a la Base de Datos
	* Limpiar datos para solicitudes a la Base de Datos
	*/
	class Sqlconsult extends Database{	

		public static function consultaBd($sql_query,$params = []){
			
			$bd = parent::getInstancia();
			$mysqli = $bd->getConnection();

			$consulta = $mysqli -> prepare($sql_query);
			
			if (count($params) > 0) {				
				$tmp = array();
				foreach ($params as $key => $value) { 
					$tmp[$key] = &$params[$key]; 
				}
				call_user_func_array(array($consulta, 'bind_param'), $tmp);
			}

			try {
				if ($consulta->execute() === false) {
					throw new Exception("<b style='color:red'>".$consulta->error.'</b>');
					
				}
			} catch (Exception $e) {
				echo 'Message: '.$e->getMessage();
			}

			$respond = $consulta->get_result();

			return array($consulta,$respond);
			
			$consulta->close();
			$mysqli->close();
		}	

		public static function escape($string){
			
			$bd = parent::getInstancia();
			$mysqli = $bd->getConnection();

			$string = $mysqli->real_escape_string(strip_tags(trim($string)));
			return $string;

			mysqli_close($mysqli);
		}

	}

 ?>