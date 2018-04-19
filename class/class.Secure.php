<?php 

/**
* Secure methods
*/
class Secure 
{	
	static public function maintenance()
	{	
		$user_ip = self::getUserIP();
	    $mant = CRUD::all('mantenimiento','*','fecha_mantenimiento IS NOT NULL',[]);
	    if (count($mant) > 0) {
	    	if ($mant[0]['fecha_mantenimiento'] >= date('Y-m-d H:m:s')) {
		        $dirIP = CRUD::all('mantenimiento_ip','direccion_ip');
		        $idioma = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2);
		        if (array_search($user_ip, array_column($dirIP, 'direccion_ip')) === false) {
		        	?>
		        	<script type="text/javascript">
		        		window.location.assign('<?php echo URL_BASE.$idioma.'/comming-soon.php' ?>');
		        	</script>
		        	<?php
		        }
		    }
	    }
	}


	static public function errorRequest()
	{	
		$msn = "Ha ocurrido un error, Intentalo nuevamente";
		header('Location: '.ADMIN.'?bd=error&msn='.msn);
	}

	static public function retornoError($error,$url_origin="")
	{	
		exit();
		if ($url_origin == '') {
			$url = URL_PAGE.'page/usuarios/iniciar-sesion/';
		}else{
			$url = $url_origin;
		}
		$url_error = $url.'error/'.$error;

		?>	

		<script type="text/javascript">
        window.location.assign( '<?php echo $url_error; ?>' );
        </script>

		<?php
	    exit();
	}	


	public static function peticionRequest($method = 'POST')
	{	
		$data = self::recibirRequest($method);
		if ($data !== false) {
			if (self::camposVacios()) {
				if (isset($data['empt_val'])) {
					if (self::verEmptyVal($data['empt_val'])) {
						if (self::camposVacios()) {
							unset($data['empt_val']);
							return $data;
						}
					}
				}
			}
		}
		return false;
	}

	public static function recibirRequest($method = 'POST')
	{	

		if ($method == 'GET') {
			$checkMethod = self::solicitud_es_get();
		}elseif ($method == 'POST') {
			$checkMethod = self::solicitud_es_post();
		}

		if ($checkMethod == TRUE) {
			$dataInsert = [];
			if ($method === 'GET') {
				foreach ($_GET as $key => $value) {
					$dataInsert[$key] = Sqlconsult::escape($value);
				}				
			}
			if ($method === 'POST') {
				foreach ($_POST as $key => $value) {
					$dataInsert[$key] = Sqlconsult::escape($value);
				}				
			}
			return $dataInsert;
		}		
		return false;
	}

	static public function recibirAngularObject($object)
	{
		foreach ($object as $key => $value) {
			$data[$key] = Sqlconsult::escape($value);
		}
		return $data;
	}	

	static public function checkTokenRestorePsw($mail,$token)
	{
		$process_restore_psw = false;
	    if (Secure::validar_mail($mail)) {
            $where = 'correo = ? AND token = ?';
            $params = ['ss',$mail,$token];
            $buscar = CRUD::all('usuarios_restaurar_psw','*',$where,$params);
            if (count($buscar) === 1) {
                $hoy = time();
                $tm_solicitud = strtotime($buscar[0]['tm_solicitud']);
                if (($hoy - $tm_solicitud) < 86400) {
                    $process_restore_psw = true;
                }
            }
	    }
	    return $process_restore_psw;
	}

	// Validación para robots
	public static function verEmptyVal($campo)
	{
		return $campo === "";
	}

	static public function camposVacios()
	{
		foreach ($_REQUEST as $nombre_campo => $valor) {
			if ($nombre_campo != 'empt_val') {			
				if (!self::tiene_valor($valor)) {
					return false; 
				}
			}
		}
		return true;
	}

	/*Mantenimiento*/
	static public function getUserIP()
    {
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        else
        {
            $ip = $remote;
        }

        return $ip;
    }
    
    static public function parametros_permitidos($params=[],$data=[])
    {	
    	$valores_permitidos = [];
    	foreach ($data as $key => $valor) {
    		if (array_search($key, $params) !== false) {
    			$valores_permitidos[$key] = $valor;
    		}else{
    			unset($data[$key]);
    		}
    	}

    	foreach ($params as $key => $value) {
    		if (array_key_exists($value, $data) === false) {
    			return false;
    		}
    	}

    	return $valores_permitidos;
    }

	static public function parametros_get_permitidos($parametros_permitidos=[]){

		$valores_permitidos = [];

		foreach ($parametros_permitidos as $valor) {
			if(isset($_GET[$valor])){
				$valores_permitidos[$valor] = $_GET[$valor];
			}
			else{
				$valores_permitidos[$valor] = NULL;
			}
		}
			return $valores_permitidos;
	}

	// $parametros_get = parametros_get_permitidos(['username', 'password']);

	// * Validar si un valor esta vacío
	// Use trim() para quitar espacios en blanco y no validar valores con espacios
	// Use === para evitar falsos positivos
	// empty() deberia considerar "0" como valor vacío

	static public function tiene_valor($valor) {
		$valor_ajustado = Sqlconsult::escape($valor);
	  return isset($valor_ajustado) && $valor_ajustado !== "";
	}

	// * Validar si un valor tiene longitud
	// Espacios iniciales y finales cuentan
	// Opciones: exacto, maximo, minimo
	// tiene_longitud($primer_nombre, ['exacto' => 20])
	// tiene_longitud($primer_nombre, ['minimo' => 5, 'maximo' => 100])
	static public function tiene_longitud($valor, $opciones=[]) {
		if(isset($opciones['maximo']) && (strlen($valor) > (int)$opciones['maximo'])) {
			return false;
		}
		if(isset($opciones['minimo']) && (strlen($valor) < (int)$opciones['minimo'])) {
			return false;
		}
		if(isset($opciones['exacto']) && (strlen($valor) != (int)$opciones['exacto'])) {
			return false;
		}
		return true;
	}

	// Validar si un campo tiene formato que cumple con una expresion regular

	// Asegurate en la expresion regular de incluir el inicio y final del string
	// (Use \A y \Z, no ^ y $.) 
	// 
	// Ejemplo:
	// cumple_formato('1234', '/\d{4}/') true
	// cumple_formato('12345', '/\d{4}/') tambien es true
	// cumple_formato('12345', '/\A\d{4}\Z/') false
	static public function cumple_formato($valor, $expresion) {
		return preg_match($expresion, $valor);	
	}


	// Validar si el formato ingresado es un correo aceptado
	static public function validar_mail($correo){
		return filter_var($correo, FILTER_VALIDATE_EMAIL);
	}

	// Contraseña Aleatoria
	 static public function generarCodigo($longitud = 15) {
	     $key = '';
	     $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ*_';
	     $max = strlen($pattern)-1;
	     for($i=0;$i < $longitud;$i++) $key .= $pattern{mt_rand(0,$max)};
	     return $key;
	 }

	 static public function numeroAleatoreo($longitud) {
	     $key = '';
	     $pattern = '1234567890';
	     $max = strlen($pattern)-1;
	     for($i=0;$i < $longitud;$i++) $key .= $pattern{mt_rand(0,$max)};
	     return $key;
	 }

	 static public function letrasAleatoreo($longitud) {
	     $key = '';
	     $pattern = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	     $max = strlen($pattern)-1;
	     for($i=0;$i < $longitud;$i++) $key .= $pattern{mt_rand(0,$max)};
	     return $key;
	 }

	// Validar Contraseña
	const SALT = '$%imund9489//8=mo';
	static public function montar_clave_verificacion($clave){
		return md5(self::SALT.$clave);
	}

	// Validar Intentos de Logueo
	static public function verificarIntentos($correo){	
		return CRUD::find('users_logs','intentos','correo_usuario = ?',['s',$correo]);		 
	}

	// Gestion de intentos de logueo
	static public function intentosSesion($correo){
		$consulta = self::verificarIntentos($correo);	
		if ($consulta[1]->num_rows == 1) {
			$datos = $consulta[1]->fetch_assoc();
			$datoNuevo = $datos['intentos'] + 1;
			$set = ['intentos' => $datoNuevo ];
			$params = array("s",$correo);
			$actualizar = CRUD::update('users_logs',$set,'correo_usuario = ?',$params);
		}elseif ($consulta[1]->num_rows == 0) {
			$data = [
				'correo_usuario' => $correo,
				'intentos' => 1
			];
			$intentos_logueo = CRUD::insert('users_logs',$data);
		}
	}


	// Reseteo intentos de logueo
	static public function resetIntentos($correo){
		$consult = self::verificarIntentos($correo);	
		if ($consult[1]->num_rows == 1) {
			$params = array("s",$correo);
			$eliminar = CRUD::delete('users_logs','correo_usuario = ?',$params);
		}
	}


	// * Validar si un valor es numero
	// Valores en solicitudes son string, use is_numeric en vez de is_int
	// opciones: maximo, minimo
	// es_numero($valor, ['minimo' => 1, 'maximo' => 5])
	static public function es_numero($valor, $opciones=[]) {
		if(!is_numeric($valor)) {
			return false;
		}
		if(isset($opciones['maximo']) && ($valor > (int)$opciones['maximo'])) {
			return false;
		}
		if(isset($opciones['minimo']) && ($valor < (int)$opciones['minimo'])) {
			return false;
		}
		return true;
	}

	// Caracterizar datos de las variables si son string, Int o NULL
	// Ejemplo typeChart(12) return 'i'
	// Ejemplo typeChart('Hola Mundo') return 's'
	public static function typeChart($valor)
	{			
		if ($valor === NULL) {
			return '';
		}
		if (is_numeric($valor)) {
			return 'i';
		}
		if (is_string($valor)) {
			return 's';
		}
		return false;
	}

	// * Validar si esta incluido en un conjunto
	static public function esta_incluido($valor, $conjunto=[]) {
	  return in_array($valor, $conjunto);
	}

	// * Validar si esta excluido de un conjunto
	static public function esta_excluido($valor, $conjunto=[]) {
	  return !in_array($valor, $conjunto);
	}

	// * Validar singularidad
	// Una validacion comun pero dificil de escribir de forma generica
	// la implementacion depende de tu base de datos
	// Require ir a la base de datos y consultar los valores presentes
	// Aqui esta presente un modelo del metodo de validacion de singularidad
	// Además considera los espacios en blanco y hacer tu validacion sql sensible a los posibles valores
	//
	// static public function es_unico($valor, $tabla, $columna) {
	//   $valor_ajustado = mysql_validado($valor);
	//   $sql = "SELECT COUNT(*) as contador FROM {$tabla} WHERE {$columna} = '{$valor_ajustado}';"
	//   if contador > 0 entonces el valor esta presente y no es único
	// }

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
/*
			Validaciones a las solicitudes
*/
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

	// Solicitudes GET no deberian hacer cambios
	// Sólo las solicitudes POST deben hacer cambios

	static public function solicitud_es_get() {
		return $_SERVER['REQUEST_METHOD'] === 'GET';
	}

	static public function solicitud_es_post() {
		return $_SERVER['REQUEST_METHOD'] === 'POST';
	}


//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
/*
			TOKEN
*/
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::


	// Debes llamar al método session_start() antes de usar estos metodos

	// Generar un token para proteger del ataque CSRF
	// No almacena el token.
	private static function csrf_token() {
		return md5(uniqid(mt_rand(), true));
	}
	// Generar y almacenar token en sesion de usuario
	// Requiere que la sesión ya se haya iniciado.
	static public function crear_csrf_token() {
		$token = self::csrf_token();
		return $token;
	}

	// Destruir todos los token.
	static public function destruir_csrf_token() {
	  	$_SESSION['csrf_token'] = null;
	 	$_SESSION['csrf_token_time'] = null;
		return true;
	}

	// Retornar un HTML donde se incluya el token
	// para usar en un formulario.
	// Uso: echo csrf_token_tag();
	static public function csrf_token_tag() {
		$token = crear_csrf_token();
	  	$_SESSION['csrf_token'] = $token;
		return "<input type=\"hidden\" name=\"csrf_token\" value=\"".$token."\">";
	}

	// Retornar true si el token de la solicitud POST del formulario es
	// identico al generado anteriormente
	// Retornar falso en caso contrario.
	static public function csrf_token_valido() {
		if(isset($_REQUEST['csrf_token'])) {
			$usuario_token = $_REQUEST['csrf_token'];
			$guardado_token = $_SESSION['csrf_token'];
			return $usuario_token === $guardado_token;
		} else {
			return false;
		}
	}

	// Puedes simplificar el método para validar el token y detener el proceso si falla 
	static public function csrf_detener_falla_token() {
		if(!csrf_token_valido()) {
			die("Token CSRF es inválido.");
		}
	}

	// Opcional para verificar si el token es reciente
	static public function csrf_token_es_reciente() {
		$lapso_maximo = 60 * 60 * 24; // 1 día
		if(isset($_SESSION['csrf_token_time'])) {
			$tiempo_almacenado = $_SESSION['csrf_token_time'];
			return ($tiempo_almacenado + $lapso_maximo) >= time();
		} else {
			// Remover expiracion del token
			destruir_csrf_token();
			return false;
		}
	}

	static public function decodeArray($array=[])
	{
		$data = json_encode($array);  
		return (object)json_decode($data);
	}
}