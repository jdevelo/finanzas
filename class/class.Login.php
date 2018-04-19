<?php 
/**
* 
*/
class Login extends Secure
{	

	public $url_user;
	public $url_admin;
	public $url_origin;
	static public $lang;
	private $_data = [];
	
	function __construct()
	{
		// URL DE ORIGEN PARA RELIZAR RETORNO
	    $this->url_user = urldecode(URL_BASE.'page/users/informacion-personal/');
	    $this->url_admin = urldecode(ADMIN);
		if (isset($_REQUEST['url'])) {
			$this->url_origin = urldecode(Sqlconsult::escape($_REQUEST['url']));
			unset($_REQUEST['url']);
		}

		if (isset($_REQUEST['payment_continue'])) {
			$this->url_origin = URL_PAGE.'/'.self::$lang.'/checkout/basket';
			unset($_REQUEST['payment_continue']);
		}

	    if ($this->validaciones()) {
	    	$loguear = $this->logIn();

	    	if ($loguear == true) {
	    		$this->ajusteBolsa();
	    	}
	    }else{	    	
	    	parent::retornoError('ERROR_VAL_LOG_MAIL_PSW',$this->url_origin);
	    }
	}

	private function validaciones()
	{
		$this->_data = parent::peticionRequest();

		if ($this->_data === false) {
			return parent::retornoError('ERROR_DATA_REQUEST',$this->url_origin);
		}

		if (!parent::validar_mail($this->_data['mail'])) {
			return parent::retornoError('INVALID_MAIL',$this->url_origin);
		}

	    if (!parent::camposVacios()) {
	    	parent::retornoError('INCONMPLETE_FORM',$this->url_origin);
	    }

	    $buscarUsuario = CRUD::find('users','mail','mail = ?',['s',$this->_data['mail']]);
		if ($buscarUsuario[1]->num_rows < 1) {
			return parent::retornoError('COUNT_LOST',$this->url_origin);
		}

		if(!parent::tiene_longitud($this->_data['password'], ['minimo' => 8, 'maximo' => 25])) {
			return parent::retornoError('LENGHT_PASSWORD',$this->url_origin);
		} 
		return true;
	}


	public static function validarPermisos($rol = 'ADMINISTRADOR')
	{	
		switch ($rol) {
			case 'ADMINISTRADOR':
				$rolID = 1;
				break;
			
			case 'VENDEDOR':
				$rolID = 2;
				break;
			
			case 'USUARIO':
				$rolID = 3;
				break;
			
			default:
				$rolID = 0;
				break;
		}

		if (isset($_SESSION['id_usuario'])) {
			$buscar = CRUD::all('users','id_rol','id_usuario = ?',['i',$_SESSION['id_usuario']]);

			if (array_search($rolID, array_column($buscar, 'id_rol')) === false) {
				header('Location: '.URL_BASE.'?permition=without_credentials');
			}
			return true;
		}
	}

	private function logIn()
	{
		/*Conteo de los intentos de sesion*/
		$intentosLogueo = Secure::verificarIntentos($this->_data['mail']);

		if ($intentosLogueo[1]->num_rows > 0) {
			$logueos = $intentosLogueo[1]->fetch_assoc();
			if ($logueos['intentos'] > 10) {
				parent::retornoError('ERR_LOG_CUENTA_BLOQUEADA',$this->url_origin);
			}
		}
		
		/*Verificar contraseña en la BD*/
		$clave2 = parent::montar_clave_verificacion($this->_data['password']);
		$params = ["ss",$this->_data['mail'],$clave2];
		$loguear = CRUD::find('users','*','mail = ? AND password = ?',$params);

		/*Validacion retornada por la BD*/
		$fila = $loguear[1]->fetch_assoc();
		

		if ($loguear[1]->num_rows == 1) {
			unset($fila['password']);
			parent::resetIntentos($this->_data['mail']);/*Reset Intentos de LogIn*/
			if ($fila['estado_usuario'] != 1) {
				parent::retornoError('ERROR_VALIDACION_users_P_V',$this->url_origin);
			}
			if($fila['estado_usuario'] == 1){
				$this->_data['recordar'] = "true";
				if (isset($this->_data['recordar']) AND $this->_data['recordar'] == "true") {

					if (isset($_COOKIE['lg_us_rem'])) {
						$verDatos = Cookie::readCookie('lg_us_rem');

						foreach ($verDatos as $dato) {							
							if ($this->_data['mail'] == $dato['user']) {
								if ($this->_data['password'] != $dato['clv']) {
									$key = [
					        			'user' => $this->_data['mail']
					        		];
					        		$data = [
					        			'clv' => $this->_data['password'],
					        		];
					        		$actualizar = Cookie::updateCookie('lg_us_rem',$key,$data);
					        		$data2 = [
					        			'tm_log' => date("Y-m-d h:i:sa")
					        		];
					        		$actualizar = Cookie::updateCookie('lg_us_rem',$key,$data2);
								}
							}else{
								$data = [ 
									[
									'user' => $this->_data['mail'],
									'clv' => $this->_data['password'],
									'close' => false,
					        		'tm_log' => date("Y-m-d h:i:sa")
									]
								];
								$insertar = Cookie::insertInCookie('lg_us_rem',$data,258000,'user');
							}
						}
					}else{
						$aLogin[0]['user'] = $this->_data['mail'];
					    $aLogin[0]['clv'] = $this->_data['password'];
					    $aLogin[0]['close'] = false;
					    $aLogin[0]['tm_log'] = date("Y-m-d h:i:sa");
						Cookie::createCookie('lg_us_rem',$aLogin);
					}					
				}
				// if (!isset($this->_data['recordar']) AND isset($_COOKIE['lg_us_rem'])) {
				// 	Cookie::deleteCookie('lg_us_rem');
				// }
				// session_start();				
				$_SESSION['user'] = Sqlconsult::escape($fila['nombre']);
				$_SESSION['id_usuario'] = $fila['id_usuario'];
			  	$_SESSION['csrf_token'] = parent::crear_csrf_token();
			 	$_SESSION['csrf_token_time'] = time();
				$id_usuario = Sqlconsult::escape($_SESSION['id_usuario']);

				// Coupon
				if (isset($_COOKIE['coupon'])) {
					$cupon = Cookie::readCookie('coupon');
					$agregarCupon = new Coupon;
					$agregarCupon->newUserCoupon($cupon['clave_cupon']);
				}

				// Address
				if (isset($_COOKIE['addr_us_shp'])) {
					CRUD::falseDelete('users_direcciones','id_usuario = ? AND tm_delete is NULL',['i',$_SESSION['id_usuario']]);	
					$data = Cookie::readCookie('addr_us_shp');
					$data['id_usuario'] = $_SESSION['id_usuario'];
					unset($data['nombre_departamento']);
					unset($data['nombre_ciudad']);
					$unique = [
						'conditional' => 'nombre_direccion = ? AND id_usuario = ? AND mail = ? AND id_departamento = ? AND id_ciudad = ? AND direccion = ? AND telefono = ? AND tm_delete IS NULL',
						'params' => ['sisiisi',$data['nombre_direccion'],$data['id_usuario'],$data['mail'],$data['id_departamento'],$data['id_ciudad'],$data['direccion'],$data['telefono']]
					];
					$nueva = CRUD::insert('users_direcciones',$data,$unique); /*Insertar Direccion*/
					Cookie::deleteCookie('addr_us_shp');
				}
			}	
		}else{
			parent::intentosSesion($this->_data['mail']);
			parent::retornoError('ERROR_VAL_LOG_MAIL_PSW',$this->url_origin);
		}
		return true;
	}

	private function ajusteBolsa()
	{
		$aCarrito = Cookie::readCookie('bolsa');

		if ($aCarrito == false) {
			$aCarrito = [];
		}
		foreach ($aCarrito as $producto) {
			$data = [
				'id_usuario' => $_SESSION['id_usuario'],
				'id_producto' => $producto['id_producto'],
				'cantidad_bolsa' => $producto['cantidad_bolsa']
			];
			$unique = [
				'conditional' => 'id_producto = ? AND id_usuario= ? AND tm_delete IS NULL',
				'params' => ['ii',$producto['id_producto'],$_SESSION['id_usuario']]
			];
			CRUD::insert('bolsa_compras',$data,$unique);

			$where = 'id_usuario = ? AND id_producto = ? AND tm_delete IS NULL';
			$params = ['ii',$_SESSION['id_usuario'],$producto['id_producto']];
			$buscarBolsaID = CRUD::all('bolsa_compras','id_bolsa_compras',$where,$params);
			$bolsaID = $buscarBolsaID[0]['id_bolsa_compras'];


			/*find personal msn*/
			// $where = 'id_bolsa_compras = ?';
			// $params = ['i',$bolsaID];
			// $findPersonalMsn = CRUD::numRows('venta_personalizar','*',$where,$params);
			/*#find personal msn*/

			// 	$set = [
			// 		'id_bolsa_compras' => $bolsaID,
			// 		'id_producto' => $producto['id_producto'],
			// 		'id_usuario' => $_SESSION['id_usuario'],
			// 		'destinatario' => $producto['destinatario'],
			// 		'motivo' => $producto['motivo'],
			// 		'frase_personalizada' => $producto['frase_personalizada'],
			// 		'mensaje_tarjeta' => $producto['mensaje_tarjeta'],
			// 	];
			// 	$unique = [
			// 		'conditional' => $where,
			// 		'params' => $params
			// 	];
			// 	CRUD::insert('venta_personalizar',$set,$unique);
			// }elseif($findPersonalMsn === 1){
			// 	$update = [
			// 		'destinatario' => $producto['destinatario'],
			// 		'motivo' => $producto['motivo'],
			// 		'frase_personalizada' => $producto['frase_personalizada'],
			// 		'mensaje_tarjeta' => $producto['mensaje_tarjeta'],
			// 	];
			// 	CRUD::update('venta_personalizar',$update,$where,$params);
			// }if ($findPersonalMsn === 0) {
			
		}
		/*Copiar en cookies la bolsa*/
			$carrito = new Checkout;
			$productos_bolsa = $carrito->productosBolsa;
			$contar = 0;
			$bolsa_c = [];
			foreach ($productos_bolsa as $p) {
				$bolsa_c[$contar]['id_producto'] = (int) $p['id_producto'];
				$bolsa_c[$contar]['cantidad_bolsa'] = (int) $p['cantidad_bolsa'];
				$contar++;
			}
			Cookie::createCookie('bolsa',$bolsa_c);
		/*#Copiar en cookies la bolsa*/
}

	private function cargarWishlist()
	{
		$wishlist = Cookie::readCookie('wishlist');

		if ($wishlist == false) {
			$wishlist = [];
		}

		foreach ($wishlist as $producto) {
			User::addWishlist($producto['id_producto']);
		}

		$consulta = CRUD::all('bolsa_deseos','id_producto','id_usuario = ? AND tm_delete = ?',['is',$_SESSION['id_usuario'],NULL]);

		foreach ($consulta as $producto) {
			$data = [
				'id_producto' => $producto['id_producto']
			];
			Cookie::insertInCookie('wishlist',$data,258000,$data);
		}
	}

}