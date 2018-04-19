<?php 
	/**
	* Configuracion inicial del proyecto
	*/
	class Config 
	{	
		private $_tables = [];
		private $_bd = 'ennavidad_bd'; /* DATABASE NAME */

		function __construct()
		{	
			/*Crear BD*/
			// $bd = 'CREATE DATABASE '.$this->_bd;
			// $enlace = new mysqli("localhost","root","");

			// if ($enlace->query($bd)) {
			// 	echo "Base de datos ".$this->_bd;
			// }else{
			// 	echo "Ya existe la base de datos ".$this->_bd;
			// }

			$tablesToDelete = [
				// 'cedulas',
				// 'testimonials_img',
				// 'testimonials_msn',
				// 'usuarios',
				// 'usuarios_direcciones',
				// 'usuarios_intentos',
				// 'usuarios_restaurar_psw',
				// 'usuario_cupon',
				// 'categorias',
				// 'categorias_sub',
				// 'ciudades_tipos_envios',
				// 'mantenimiento',
				// 'mantenimiento_ip',
				// 'bolsa_compras',
				// 'productos',
				// 'productos_imagenes',
				// 'productos_imagenes_principales',
				// 'productos_cantidad',
				// 'productos_items',
				// 'productos_item',
				// 'productos_items_tipos',
				// 'productos_descuento',
				// 'productos_con_items',
				// 'estados',
				// 'ventas',
				// 'venta_detalle',
				// 'productos_publicados',
				// 'venta_token',
				// 'newsletter',
				// 'contactenos',
				// 'venta_personalizar',
				// 'tipos_cupones',
				// 'productos_cupones',
				// 'ingreso_productos_gral',
				// 'estados_pedido',
				// 'ciudades',
				// 'departamento',
			];
			
			if ($this->deleteTable($tablesToDelete) === true) {
				echo "Tablas restauradas correctamente: ";
				echo "<br>";
				foreach ($tablesToDelete as $table) {
					echo strtoupper($table)." // ";
				}
				echo "<br>";
				echo "<br>";
			}

			$this->runTables();
			
			/*Definiendo administradores*/
			$usuarios = [
				['JUAN DAVID','LEON PONCE','jlp25@hotmail.com','M',1,'ctlb31207',1]
			];

			foreach ($usuarios as $usuario) {
				$set = [
					'nombre' => $usuario[0],
					'apellido_usuario' => $usuario[1],
					'correo' => $usuario[2],
					'sexo' => $usuario[3],
					'id_rol' => $usuario[4],
					'clave' => Secure::montar_clave_verificacion($usuario[5]),
					'estado_usuario' => $usuario[6]
				];

				$unique = [
					'conditional' => 'correo = ?',
					'params' => ['s',$usuario[2]]
				];
				CRUD::insert('usuarios',$set,$unique);	
			}		

			/*#MANTENIMIENTO*/
			$mantenimiento = ['activo'];
			foreach ($mantenimiento as $mnt) {
				$set = ['estado' => $mnt, 'fecha_mantenimiento' => '2017-06-13 17:00'];
				$unique = [
					'conditional' => 'id_mantenimiento = ?',
					'params' => ['i',1]
				];
				CRUD::insert('mantenimiento',$set,$unique);
			}

			/*#MANTENIMIENTO IP's*/
			$mantenimiento_ip = ['186.83.17.60', '181.54.6.126', '::1'];
			foreach ($mantenimiento_ip as $mnt) {
				$set = ['id_mantenimiento' => 1, 'direccion_ip' => $mnt];
				$unique = [
					'conditional' => 'direccion_ip = ?',
					'params' => ['s',$mnt]
				];
				CRUD::insert('mantenimiento_ip',$set,$unique);
			}

			/*#ROLES*/
			$roles = ['ADMINISTRADOR','USUARIO'];
			foreach ($roles as $rol) {
				$set = ['rol' => $rol];
				$unique = [
					'conditional' => 'rol = ?',
					'params' => ['s',$rol]
				];
				CRUD::insert('roles',$set,$unique);
			}

			/* #CATEGORIAS */
			$categorias = [
				['Colección 2017','Decora tus espacios o da un toque especial navideño a tus utencilios','NW'],
				['Decora tu Sala','Decora tus espacios o da un toque especial navideño a tus utencilios','SL'],
				['Adornos de Cocina','Decora tus espacios o da un toque especial navideño a tus utencilios','CO'],
				['Decora puertas y paredes','TDecora tus espacios o da un toque especial navideño a tus utencilios','PP']
			];
			
			foreach ($categorias as $categoria) {
				$set = [ 'categoria' => $categoria[0],'descripcion' => $categoria[1] ,'identificador' => $categoria[2] ];
				$unique = [
					'conditional' => 'categoria = ? OR identificador = ?',
					'params' => ['ss',$categoria[0],$categoria[2]]
				];
				CRUD::insert('categorias',$set,$unique);
			}

			/* #CATEGORIAS */
			$categorias_sub = [

			];
			
			foreach ($categorias_sub as $subCategoria) {
				$set = [ 'id_categoria' => $subCategoria[0], 'nombre_sub_categoria' => $subCategoria[1] ];
				$unique = [
					'conditional' => 'nombre_sub_categoria = ?',
					'params' => ['ss',$subCategoria[1]]
				];
				CRUD::insert('categorias_sub',$set,$unique);
			}

			/* #OFERTAS */
			$ofertas = ['DESCUENTO','CUPONES','OFERTA DEL DIA'];

			foreach ($ofertas as $oferta) {
				$set = [ 'tipo_oferta' => $oferta ];
				$unique = [
					'conditional' => 'tipo_oferta = ?',
					'params' => ['s',$oferta]
				];
				CRUD::insert('ofertas',$set,$unique);
			}

			$ofertas_descuentos = [
				['PRODUCTO','Agrega un descuento para un producto o un conjunto de productos específicos.'],
				['COMPRAS MAYOR A','Agrega un descuento que aplique a compras con un valor mayor a un coste determinado.'],
				['ENVIO GRATIS','Establece un monto mínimo de compra para proporcionar el envio gratis de una compra.'],
			];

			foreach ($ofertas_descuentos as $oferta) {
				$set = [ 'tipo_descuento' => $oferta[0], 'descripcion_descuento' => $oferta[1] ];
				$unique = [
					'conditional' => 'tipo_descuento = ?',
					'params' => ['s',$oferta[0]]
				];
				CRUD::insert('ofertas_descuentos',$set,$unique);
			}


			/* #ESTADOS */
			$estados = [
				['Pendiente de pago'],
				['Verificación pago'],
				['Alistamiento'],
				['Enviado'],
				['Completado'],
				['Cancelado'],
				['Devolución'],
				['Transacción rechazada'],
				['Esperando respuesta del pago'],
				['Declinada']
			];

			foreach ($estados as $estado) {
				$set = [	
					'estado_pedido' => $estado[0],	
				];
				$unique = [
					'conditional' => 'estado_pedido = ?',
					'params' => ['s',$estado[0]]
				];				
				CRUD::insert('estados_pedido',$set,$unique);
			}

			/*CIUDADES TIPOS ENVIOS*/
			$ciudades_tipos_envios = [
				'BOGOTA', 'CIUDADES PRINCIPALES','MENOS PRINCIPALES','OTROS'
			];

			foreach ($ciudades_tipos_envios as $ctp) {
				$set = ['tipo_ciudad' => $ctp];
				$unique = [
					'conditional' => 'tipo_ciudad = ?',
					'params' => ['s',$ctp]
				];
				CRUD::insert('ciudades_tipos_envios',$set,$unique);
			}

			/* #CUPON */
			$cupones = [
				['BASICO','Aplica porcentaje de descuento para cualquier tipo de compra sin importar producto o valor de la compra.'],
				['COMPRA MINIMA','Aplica cupón solo si el valor de la compra supera un monto mínimo.'],
				['POR PRODUCTO','Este aplicará un cupón a un produto en especifico unicamente']
			];

			foreach ($cupones as $cupon) {
				$set = [ 'tipo_cupon' => $cupon[0], 'descripcion_cupon' => $cupon[1]];
				$unique = [
					'conditional' => 'tipo_cupon = ?',
					'params' => ['s',$cupon[0]]
				];
				CRUD::insert('tipos_cupones',$set,$unique);
			}		

			$this->addColumn('productos_items','id_tipo_item','SMALLINT(6)','item_en');
			$this->addColumn('contactenos','error_email','VARCHAR(5) ','mensaje_contacto');
			$this->addColumn('productos_descuento','tm_delete','DATETIME ','fecha_limite');
			/* #Ciudades y Departamentos */
			$this->ubicaciones();
		}

		private function cedulas()
		{
			$tiempoInicial = microtime(true);
			for ($i=0; $i <= 700000; $i++) { 
				$set = [
					'cedula' => Secure::numeroAleatoreo(9),
					'nombre' => Secure::letrasAleatoreo(9)
				];
				$insertar = CRUD::insert('cedulas',$set);
			}
			// $ar=fopen("datos.php","a") or
			//     die("Problemas en la creacion");
			// fputs($ar,'<?php [ ');
			// fputs($ar,"\n");
			// fputs($ar,'[938174725,"calJCyoHb"],');

			// foreach ($set as $key => $value) {
			// 	fputs($ar,"[".$value['cedula'].", '".$value['nombre']."'], \n");
			// }

			// fputs($ar,"\n");
			// fputs($ar," ]");
			// fputs($ar,"\n");
			// fclose($ar);

			echo (microtime(true) - $tiempoInicial) / 60;
		}

		private function runTables()
		{	
			/*
				$rows = [		
					0 => 'nameRow',
					1 => $type=VARCHAR(30),
					2 => 'unsigned=false',
					3 => 'not null = false',
					4 => 'autoincrement=false',
					5 => 'primary_key=false',
					6 => $default='data'
				]
			*/

			$tables[] = [
				'nombre' => 'cedulas',
				'filas' => [
					['id_cedula','INT(11)',true,true,true,true,''],
					['cedula','BIGINT(15)',true,true,false,false,''],
					['nombre','VARCHAR(100)',false,true,false,false,'']
				]
			];

			$tables[] = [
				'nombre' => 'mantenimiento',
				'filas' => [
					['id_mantenimiento','INT(1)',true,false,true,true,''],
					['estado','VARCHAR(60)',false,true,false,false,''],
					['fecha_mantenimiento','DATETIME',false,false,false,false,'']
				]
			];

			$tables[] = [
				'nombre' => 'mantenimiento_ip',
				'filas' => [
					['id_ip','INT(1)',true,false,true,true,''],
					['id_mantenimiento','VARCHAR(20)',false,false,false,false,''],
					['direccion_ip','VARCHAR(50)',false,true,false,false,'']
				]
			];

			$tables[] = [ 
				'nombre' => 'usuarios',
				'filas' => [
						['id_usuario','INT(6)',true,false,true,true,''],
						['nombre','VARCHAR(60)',false,true,false,false,''],
						['apellido_usuario','VARCHAR(60)',false,false,false,false,''],
						['correo','VARCHAR(50)',false,true,false,false,''],
						['sexo','VARCHAR(2)',false,false,false,false,''],
						['id_rol','TINYINT(1)',true,true,false,false,2],
						['clave','VARCHAR(50)',false,true,false,false,''],
						['estado_usuario','INT(1)',true,true,false,false,9],
						['fecha_registro','DATETIME',false,false,false,false,'CURRENT_TIMESTAMP'],
						['tm_delete','DATETIME',false,false,false,false,'']
					]	
			];

			$tables[] = [
				'nombre' => 'usuarios_direcciones',
				'filas' => [
						['id_direcciones','INT(10)',true,false,true,true,''],
						['id_usuario','INT(11)',true,true,false,false,''],
						['nombre_direccion','VARCHAR(100)',false,true,false,false,''],
						['correo','VARCHAR(120)',false,true,false,false,''],
						['id_departamento','SMALLINT(5)',true,true,false,false,''],
						['id_ciudad','SMALLINT(5)',true,true,false,false,''],
						['direccion','VARCHAR(40)',false,true,false,false,''],
						['telefono','BIGINT(15)',true,true,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,'']
					]
			];

			$tables[] = [
				'nombre' => 'usuarios_intentos',
				'filas' => [
						['id_intentos','INT(10)',true,false,true,true,''],
						['correo_usuario','VARCHAR(40)',false,true,false,false,''],
						['intentos','TINYINT(2)',true,true,false,false,'']
					]
			];

			$tables[] = [
				'nombre' => 'usuarios_restaurar_psw',
				'filas' => [
						['id_restaurar','INT(10)',true,false,true,true,''],
						['correo','VARCHAR(100)',false,true,false,false,''],
						['token','VARCHAR(40)',false,true,false,false,''],
						['tm_solicitud','DATETIME',false,true,false,false,'CURRENT_TIMESTAMP']
					]
			];

			$tables[] = [ 
				'nombre' => 'roles',
				'filas' => [
						['id_rol','TINYINT(2)',true,false,true,true,''],
						['rol','VARCHAR(15)',false,true,false,false,'']
					]	
			];

			$tables[] = [ 
				'nombre' => 'usuario_cupon',
				'filas' => [
						['id_usuario_cupon','INT(10)',true,false,true,true,''],
						['id_producto_cupon','INT(11)',true,true,false,false,''],
						['id_usuario','INT(11)',true,true,false,false,''],
						['tm_create','DATETIME',false,false,false,false,'CURRENT_TIMESTAMP'],
						['tm_used','DATETIME',false,false,false,false,''],
						['tm_expire','DATETIME',false,false,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,''],
					]	
			];

			$tables[] = [ 
				'nombre' => 'usuarios_lang',
				'filas' => [
						['id_usuario_lang','INT(10)',true,false,true,true,''],
						['id_usuario','INT(11)',true,true,false,false,''],
						['lang','CHAR(2)',false,true,false,false,''],
					]	
			];

			$tables[] = [ 
				'nombre' => 'bolsa_compras',
				'filas' => [
						['id_bolsa_compras','INT(11)',true,false,true,true,''],
						['id_usuario','INT(11)',true,true,false,false,''],
						['id_producto','INT(6)',true,true,false,false,''],
						['cantidad_bolsa','TINYINT(3)',true,true,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,'']
					]	
			];

			$tables[] = [ 
				'nombre' => 'categorias',
				'filas' => [
						['id_categoria','TINYINT(3)',true,false,true,true,''],
						['categoria','VARCHAR(35)',false,true,false,false,''],
						['descripcion','VARCHAR(500)',false,true,false,false,''],
						['identificador','VARCHAR(4)',false,true,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,'']
					]	
			];

			$tables[] = [ 
				'nombre' => 'categorias_sub',
				'filas' => [
						['id_sub_categoria','SMALLINT(5)',true,false,true,true,''],
						['id_categoria','TINYINT(3)',true,true,false,false,''],
						['nombre_sub_categoria','VARCHAR(20)',false,true,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,'']
					]	
			];

			$tables[] = [
				'nombre' => 'productos',
				'filas' => [
						['id_producto','SMALLINT(6)',true,false,true,true,''],
						['serie','VARCHAR(10)',false,true,false,false,''],
						['nombre_producto','VARCHAR(60)',false,true,false,false,''],
						['descripcion','VARCHAR(535)',false,true,false,false,''],
						['id_categoria','TINYINT(3)',true,true,false,false,''],
						['id_sub_categoria','TINYINT(3)',true,true,false,false,''],
						['precio','INT(11)',true,true,false,false,''],
						['fecha_entrada','DATETIME',false,false,false,false,'CURRENT_TIMESTAMP'],
						['tm_delete','DATETIME',false,false,false,false,'']
					]
			];

			$tables[] = [
				'nombre' => 'productos_votacion',
				'filas' => [
						['id_producto_votacion','SMALLINT(6)',true,false,true,true,''],
						['id_usuario','INT(11)',true,true,false,false,''],
						['id_producto','INT(11)',true,true,false,false,''],
						['voto','INT(11)',true,true,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,'']
					]
			];

			$tables[] = [
				'nombre' => 'productos_cantidad',
				'filas' => [
						['id_cantidades	','SMALLINT(6)',true,false,true,true,''],
						['id_producto','SMALLINT(6)',true,true,false,false,''],
						['cantidad_entrada','SMALLINT(5)',true,true,false,false,''],
						['cantidad_salida','SMALLINT(5)',true,true,false,false,'0']
					]
			];

			$tables[] = [
				'nombre' => 'productos_etiquetas_tipos',
				'filas' => [
						['id_etiqueta','SMALLINT(6)',true,false,true,true,''],
						['etiqueta','VARCHAR(100)',false,true,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,'']
					]
			];


			$tables[] = [
				'nombre' => 'productos_etiquetas',
				'filas' => [
						['id_producto_etiqueta','SMALLINT(6)',true,false,true,true,''],
						['id_producto','INT(8)',true,true,false,false,''],
						['id_etiqueta','INT(8)',true,true,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,'']
					]
			];

			$tables[] = [
				'nombre' => 'productos_con_items',
				'filas' => [
						['id_prod_item','SMALLINT(6)',true,false,true,true,''],
						['id_producto','INT(10)',true,true,false,false,''],
						['id_item','SMALLINT(10)',false,true,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,'']
					]
			];

			$tables[] = [
				'nombre' => 'productos_descuento',
				'filas' => [
						['id_promo','SMALLINT(6)',true,false,true,true,''],
						['id_producto','SMALLINT(6)',true,true,false,false,''],
						['porcentaje','TINYINT(3)',true,true,false,false,''],
						['valor_descontado','INT(8)',true,true,false,false,''],
						['fecha_inicial','DATETIME',false,true,false,false,'CURRENT_TIMESTAMP'],
						['fecha_limite','DATETIME',false,true,false,false,'']
					]
			];

			$tables[] = [
				'nombre' => 'productos_cupones',
				'filas' => [
						['id_producto_cupon','INT(10)',true,false,true,true,''],
						['clave_cupon','VARCHAR(30)',false,true,false,false,''],
						['id_tipo_cupon','TINYINT(2)',true,true,false,false,''],
						['id_producto','INT(10)',true,false,false,false,''],
						['porcentaje','TINYINT(3)',true,false,false,false,''],
						['valor_descontado','INT(10)',true,false,false,false,''],
						['valor_compra_minima','INT(10)',true,false,false,false,''],
						['fecha_inicial','DATETIME',false,true,false,false,'CURRENT_TIMESTAMP'],
						['fecha_limite','DATETIME',false,false,false,false,''],
						['cupones_disponibles','SMALLINT(6)',true,false,false,false,''],
						['cupones_usados','SMALLINT(6)',true,true,false,false,'0'],
						['maximo_usuario','SMALLINT(6)',true,true,false,false,'1'],
						['tm_delete','DATETIME',false,false,false,false,'']
					]
			];

			$tables[] = [
				'nombre' => 'productos_imagenes',
				'filas' => [
						['id_p_imagenes','SMALLINT(6)',true,false,true,true,''],
						['serie','VARCHAR(20)',false,true,false,false,''],
						['ruta_img_lg','VARCHAR(120)',false,true,false,false,''],
						['ruta_img_sm','VARCHAR(120)',false,true,false,false,''],
						['ruta_img_tn','VARCHAR(120)',false,true,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,'']
					]
			];

			$tables[] = [
				'nombre' => 'productos_imagenes_principales',
				'filas' => [
						['id_pip','SMALLINT(6)',true,false,true,true,''],
						['id_producto','SMALLINT(6)',true,true,false,false,''],
						['ruta_img_lg','VARCHAR(120)',false,true,false,false,''],
						['ruta_img_sm','VARCHAR(120)',false,true,false,false,''],
						['ruta_img_tn','VARCHAR(120)',false,true,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,'']
					]
			];

			$tables[] = [
				'nombre' => 'productos_publicados',
				'filas' => [
						['id_publicacion','INT(10)',true,false,true,true,''],
						['serie','VARCHAR(10)',false,true,false,false,''],
						['estado_publicado','CHAR(2)',false,true,false,false,''],
						['fecha_publicacion','DATETIME',false,true,false,false,'']
					]
			];

			$tables[] = [
				'nombre' => 'productos_agotados',
				'filas' => [
						['id_agotados','INT(10)',true,false,true,true,''],
						['id_producto','INT(10)',false,true,false,false,''],
						['estado_agotado','CHAR(2)',false,true,false,false,''],
						['fecha_agotado','DATETIME',false,true,false,false,'']
					]
			];

			$tables[] = [
				'nombre' => 'ciudades_tipos_envios',
				'filas' => [
						['id_tipo_ciudad','INT(10)',true,false,true,true,''],
						['tipo_ciudad','VARCHAR(25)',false,true,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,'']
					]
			];

			$tables[] = [
				'nombre' => 'ciudades_categoria_envios',
				'filas' => [
						['id_categoria_ciudad','INT(10)',true,false,true,true,''],
						['id_ciudad','INT(10)',true,true,false,false,''],
						['id_tipo_ciudad','INT(10)',true,true,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,'']
					]
			];

			$tables[] = [
				'nombre' => 'productos_costo_envio',
				'filas' => [
						['id_costo_envio','INT(10)',true,false,true,true,''],
						['id_producto','INT(10)',false,true,false,false,''],
						['destino_1','SMALLINT(6)',true,true,false,false,''],
						['destino_2','SMALLINT(6)',true,true,false,false,''],
						['destino_3','SMALLINT(6)',true,true,false,false,''],
						['destino_4','SMALLINT(6)',true,true,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,'']
					]
			];



			$tables[] = [
				'nombre' => 'contactenos',
				'filas' => [
						['id_contactenos','MEDIUMINT(7)',true,false,true,true,''],
						['nombre_contacto','VARCHAR(60)',false,true,false,false,''],
						['telefono_contacto','VARCHAR(60)',false,true,false,false,''],
						['correo_contacto','VARCHAR(50)',false,true,false,false,''],
						['asunto_contacto','VARCHAR(50)',false,true,false,false,''],
						['mensaje_contacto','TEXT(1000)',false,true,false,false,''],
						['fecha_contacto','DATETIME',false,true,false,false,'CURRENT_TIMESTAMP']
					]
			];


			$tables[] = [
				'nombre' => 'newsletter',
				'filas' => [
						['id_news','MEDIUMINT(7)',true,false,true,true,''],
						['correo_news','VARCHAR(60)',false,true,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,'']
					]
			];

			$tables[] = [
				'nombre' => 'ventas',
				'filas' => [
						['id_venta','INT(11)',true,false,true,true,''],
						['serial_venta','VARCHAR(35)',false,true,false,false,''],
						['id_producto','SMALLINT(6)',true,true,false,false,''],
						['cantidad','SMALLINT(4)',true,true,false,false,''],
						['precio_unitario','INT(11)',true,true,false,false,''],
						['descuento','INT(11)',true,true,false,false,''],
						['precio_total_producto','INT(11)',true,true,false,false,'']
					]
			];

			$tables[] = [
				'nombre' => 'venta_detalle',
				'filas' => [
					['id_venta_detalle','INT(11)',true,false,true,true,''],
					['serial_venta','VARCHAR(35)',false,true,false,false,''],
					['id_usuario','SMALLINT(6)',true,true,false,false,''],
					['precio_productos','MEDIUMINT(8)',true,true,false,false,''],
					['precio_envio','MEDIUMINT(8)',true,true,false,false,''],
					['venta_descuento','MEDIUMINT(8)',true,true,false,false,''],
					['id_producto_cupon','SMALLINT(6)',true,false,false,false,''],
					['valor_cupon','MEDIUMINT(8)',true,false,false,false,''],
					['precio_total','MEDIUMINT(8)',true,true,false,false,''],
					['fecha_venta','DATETIME',false,true,false,false,''],
					['id_estado','TINYINT(3)',true,true,false,false,''],
					['id_direccion_envio','MEDIUMINT(6)',true,true,false,false,''],
					['tm_delete','DATETIME',false,false,false,false,'']
				]
			];

			$tables[] = [
				'nombre' => 'venta_personalizar',
				'filas' => [
					['id_frase_personalizada','INT(11)',true,false,true,true,''],
					['serial_venta','VARCHAR(50)',false,false,false,false,''],
					['id_bolsa_compras','INT(10)',true,true,false,false,''],
					['id_producto','INT(10)',true,true,false,false,''],
					['id_usuario','INT(11)',true,true,false,false,''],
					['destinatario','VARCHAR(100)',false,true,false,false,''],
					['motivo','VARCHAR(20)',false,true,false,false,''],
					['frase_personalizada','VARCHAR(35)',false,true,false,false,''],
					['mensaje_tarjeta','VARCHAR(250)',false,true,false,false,''],
					['tm_delete','DATETIME',false,false,false,false,'']
				]
			];

			$tables[] = [
				'nombre' => 'venta_token',
				'filas' => [
					['id_venta_token','INT(11)',true,false,true,true,''],
					['token','VARCHAR(40)',false,true,false,false,''],
					['serial_venta','VARCHAR(40)',false,true,false,false,''],
					['tm_create','DATETIME',false,false,false,false,''],
					['tm_update','DATETIME',false,false,false,false,''],
					['tm_delete','DATETIME',false,false,false,false,'']
				]
			];

			$tables[] = [
				'nombre' => 'ofertas',
				'filas' => [
						['id_oferta','TINYINT(11)',true,false,true,true,''],
						['tipo_oferta','VARCHAR(80)',false,true,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,'']
					]
			];

			$tables[] = [
				'nombre' => 'ofertas_descuentos',
				'filas' => [
						['id_ofertas_descuentos','TINYINT(11)',true,false,true,true,''],
						['tipo_descuento','VARCHAR(30)',false,true,false,false,''],
						['descripcion_descuento','VARCHAR(150)',false,true,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,'']
					]
			];

			$tables[] = [
				'nombre' => 'ingreso_productos_gral',
				'filas' => [
						['id_ingreso_gral','SMALLINT(6)',true,false,true,true,''],
						['serial_compra','VARCHAR(35)',false,true,false,false,''],
						['id_producto','SMALLINT(4)',true,true,false,false,''],
						['cantidad','SMALLINT(4)',true,true,false,false,''],
						['fecha_compra','DATETIME',false,true,false,false,'CURRENT_TIMESTAMP'],
						['id_usuario','SMALLINT(6)',true,true,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,'']
					]
			];

			
			$tables[] = [
				'nombre' => 'departamento',
				'filas' => [
					['id_departamento','SMALLINT(5)',true,true,true,true,''],
					['nombre_departamento','VARCHAR(70)',false,true,false,false,'']
				]
			];	
			
			$tables[] = [
				'nombre' => 'ciudades',
				'filas' => [
					['id_ciudad','SMALLINT(5)',true,true,false,true,''],
					['nombre_ciudad','VARCHAR(70)',false,true,false,false,''],
					['id_departamento','TINYINT(2)',true,true,false,false,'']
				]
			];	


			$tables[] = [
				'nombre' => 'estados_pedido',
				'filas' => [
						['id_estado','SMALLINT(5)',true,false,true,true,''],
						['estado_pedido','VARCHAR(40)',false,true,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,'']
					]
			];	

			$tables[] = [
				'nombre' => 'tipos_cupones',
				'filas' => [
						['id_tipo_cupon','TINYINT(3)',true,false,true,true,''],
						['tipo_cupon','VARCHAR(25)',false,true,false,false,''],
						['descripcion_cupon','VARCHAR(250)',false,true,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,'']
					]
			];	

			$tables[] = [
				'nombre' => 'testimonials_img',
				'filas' => [
						['id_test_img','SMALLINT(5)',true,false,true,true,''],
						['ruta_img_test','VARCHAR(100)',false,true,false,false,''],
						['created_at','DATETIME',false,false,false,false,'CURRENT_TIMESTAMP'],
						['tm_delete','DATETIME',false,false,false,false,'']
					]
			];

			$tables[] = [
				'nombre' => 'testimonials_msn',
				'filas' => [
						['id_test_msn','SMALLINT(5)',true,false,true,true,''],
						['message','TEXT(600)',false,true,false,false,''],
						['author','VARCHAR(100)',false,true,false,false,''],
						['created_at','DATETIME',false,true,false,false,'CURRENT_TIMESTAMP'],
						['tm_delete','DATETIME',false,false,false,false,'']
					]
			];

			/*
				$rows = [		
					0 => 'nameRow',
					1 => $type=VARCHAR(30),
					2 => 'unsigned=false',
					3 => 'not null = false',
					4 => 'autoincrement=false',
					5 => 'primary_key=false',
					6 => $default='data'
				]
			*/

			foreach ($tables as $table) {
				$this->createTables($table['nombre'],$table['filas']);
			}
		}

		public function deleteTable($tables=[])
		{
			$sql = 'DROP TABLE IF EXISTS ';

			foreach ($tables as $table) {
				$sql .= $table.', ';
			}

			$sql = substr($sql, 0, -2);

			$bd = Database::getInstancia();
			$mysqli = $bd->getConnection();

			return $mysqli->query($sql);
		}

		private function createTables($tableName,$rows=[])
		{	
			/*
				$rows = [		
					0 => 'nameRow',
					1 => $type=VARCHAR(30),
					2 => 'unsigned=false',
					3 => 'not null = false',
					4 => 'autoincrement=false',
					5 => 'primary_key=false',
					6 => $default='data'
				]
			*/
			$sql = "CREATE TABLE $tableName ( ";

			foreach ($rows as $row) {
				$sql.= $row[0].' '.$row[1].' ';
				if ($row[2] == true) {
					$sql.= ' UNSIGNED ';
				}
				if ($row[3] == true) {
					$sql.= ' NOT NULL ';
				}
				if ($row[4] == true) {
					$sql.= ' AUTO_INCREMENT ';
				}
				if ($row[5] == true) {
					$sql.= ' PRIMARY KEY ';
				}
				if ($row[6] != '') {
					$sql.= " DEFAULT ".$row[6];
				}
				$sql .= ', ';
			}

			$sql = substr($sql, 0, -2);

			$sql.= ')';

			$bd = Database::getInstancia();
			$mysqli = $bd->getConnection();

			$crearTabla = $mysqli->query($sql);
			
			if ($crearTabla === true) {
				$estado = 'CREADA';
			}else{				
				$estado = 'ERROR AL CREAR LA TABLA';
			} 
			echo "<table style='width:50%; min-width: 200px; margin: 0 auto; border: 1px solid grey;'>
			  <tr>
			    <td>$tableName</td>
			    <td style='float: right;'>$estado</td> 
			  </tr>
			</table>";			
		}

		private function addColumn($tableName,$column,$type=null,$after=null)
		{
			$sql = "ALTER TABLE $tableName ADD $column ";

			if ($type !== null) {
				$sql .= $type;
			}

			if ($after !== null) {
				$sql .= "AFTER $after";
			}

			$bd = Database::getInstancia();
			$mysqli = $bd->getConnection();

			$addColumn = $mysqli->query($sql);
			var_dump($sql);
			var_dump($addColumn);
		}

		private function editColumn($table,$old_name_column,$new_name_column,$type=null)
		{
			$sql = "ALTER TABLE $table CHANGE $old_name_column $new_name_column ";

			if ($type !== null) {
				$sql .= $type;
			}

			$bd = Database::getInstancia();
			$mysqli = $bd->getConnection();

			$editColumn = $mysqli->query($sql);
			var_dump($sql);
			var_dump($editColumn);

		}

		private function ubicaciones()
		{
			$departamentos = ['AMAZONAS', 'ANTIOQUIA', 'ARAUCA', 'ATLÁNTICO', 'BOLÍVAR', 'BOYACÁ', 'CALDAS', 'CAQUETÁ', 'CASANARE', 'CAUCA', 'CESAR', 'CHOCÓ', 'CÓRDOBA', 'CUNDINAMARCA', 'GUAINÍA', 'GUAVIARE', 'HUILA', 'LA GUAJIRA', 'MAGDALENA', 'META', 'NARIÑO', 'NORTE DE SANTANDER', 'PUTUMAYO', 'QUINDÍO', 'RISARALDA', 'SAN ANDRÉS Y ROVIDENCIA', 'SANTANDER', 'SUCRE', 'TOLIMA', 'VALLE DEL CAUCA', 'VAUPÉS', 'VICHADA', 'BOGOTA D.C.'];

			foreach ($departamentos as $nombre_departamento) {
				$set = ['nombre_departamento' => $nombre_departamento];
				$unique = [
					'conditional' => 'nombre_departamento = ?',
					'params' => ['s',$nombre_departamento]
				];
				CRUD::insert('departamento',$set,$unique);
			}

			$ciudades = [
				[1,'EL ENCANTO',1],
				[2,'LA CHORRERA',1],
				[3,'LA PEDRERA',1],
				[4,'LA VICTORIA',1],
				[5,'LETICIA',1],
				[6,'MIRITI',1],
				[7,'PUERTO ALEGRIA',1],
				[8,'PUERTO ARICA',1],
				[9,'PUERTO NARIÑO',1],
				[10,'PUERTO SANTANDER',1],
				[11,'TURAPACA',1],
				[12,'ABEJORRAL',2],
				[13,'ABRIAQUI',2],
				[14,'ALEJANDRIA',2],
				[15,'AMAGA',2],
				[16,'AMALFI',2],
				[17,'ANDES',2],
				[18,'ANGELOPOLIS',2],
				[19,'ANGOSTURA',2],
				[20,'ANORI',2],
				[21,'ANTIOQUIA',2],
				[22,'ANZA',2],
				[23,'APARTADO',2],
				[24,'ARBOLETES',2],
				[25,'ARGELIA',2],
				[26,'ARMENIA',2],
				[27,'BARBOSA',2],
				[28,'BELLO',2],
				[29,'BELMIRA',2],
				[30,'BETANIA',2],
				[31,'BETULIA',2],
				[32,'BOLIVAR',2],
				[33,'BRICEÑO',2],
				[34,'BURITICA',2],
				[35,'CACERES',2],
				[36,'CAICEDO',2],
				[37,'CALDAS',2],
				[38,'CAMPAMENTO',2],
				[39,'CANASGORDAS',2],
				[40,'CARACOLI',2],
				[41,'CARAMANTA',2],
				[42,'CAREPA',2],
				[43,'CARMEN DE VIBORAL',2],
				[44,'CAROLINA DEL PRINCIPE',2],
				[45,'CAUCASIA',2],
				[46,'CHIGORODO',2],
				[47,'CISNEROS',2],
				[48,'COCORNA',2],
				[49,'CONCEPCION',2],
				[50,'CONCORDIA',2],
				[51,'COPACABANA',2],
				[52,'DABEIBA',2],
				[53,'DONMATIAS',2],
				[54,'EBEJICO',2],
				[55,'EL BAGRE',2],
				[56,'EL PENOL',2],
				[57,'EL RETIRO',2],
				[58,'ENTRERRIOS',2],
				[59,'ENVIGADO',2],
				[60,'FREDONIA',2],
				[61,'FRONTINO',2],
				[62,'GIRALDO',2],
				[63,'GIRARDOTA',2],
				[64,'GOMEZ PLATA',2],
				[65,'GRANADA',2],
				[66,'GUADALUPE',2],
				[67,'GUARNE',2],
				[68,'GUATAQUE',2],
				[69,'HELICONIA',2],
				[70,'HISPANIA',2],
				[71,'ITAGUI',2],
				[72,'ITUANGO',2],
				[73,'JARDIN',2],
				[74,'JERICO',2],
				[75,'LA CEJA',2],
				[76,'LA ESTRELLA',2],
				[77,'LA PINTADA',2],
				[78,'LA UNION',2],
				[79,'LIBORINA',2],
				[80,'MACEO',2],
				[81,'MARINILLA',2],
				[82,'MEDELLIN',2],
				[83,'MONTEBELLO',2],
				[84,'MURINDO',2],
				[85,'MUTATA',2],
				[86,'NARINO',2],
				[87,'NECHI',2],
				[88,'NECOCLI',2],
				[89,'OLAYA',2],
				[90,'PEQUE',2],
				[91,'PUEBLORRICO',2],
				[92,'PUERTO BERRIO',2],
				[93,'PUERTO NARE',2],
				[94,'PUERTO TRIUNFO',2],
				[95,'REMEDIOS',2],
				[96,'RIONEGRO',2],
				[97,'SABANALARGA',2],
				[98,'SABANETA',2],
				[99,'SALGAR',2],
				[100,'SAN ANDRES DE CUERQUIA',2],
				[101,'SAN CARLOS',2],
				[102,'SAN FRANCISCO',2],
				[103,'SAN JERONIMO',2],
				[104,'SAN JOSE DE LA MONTAÑA',2],
				[105,'SAN JUAN DE URABA',2],
				[106,'SAN LUIS',2],
				[107,'SAN PEDRO DE LOS MILAGROS',2],
				[108,'SAN PEDRO DE URABA',2],
				[109,'SAN RAFAEL',2],
				[110,'SAN ROQUE',2],
				[111,'SAN VICENTE',2],
				[112,'SANTA BARBARA',2],
				[113,'SANTA ROSA DE OSOS',2],
				[114,'SANTO DOMINGO',2],
				[115,'SANTUARIO',2],
				[116,'SEGOVIA',2],
				[117,'SONSON',2],
				[118,'SOPETRAN',2],
				[119,'TAMESIS',2],
				[120,'TARAZA',2],
				[121,'TARSO',2],
				[122,'TITIRIBI',2],
				[123,'TOLEDO',2],
				[124,'TURBO',2],
				[125,'URAMITA',2],
				[126,'URRAO',2],
				[127,'VALDIVIA',2],
				[128,'VALPARAISO',2],
				[129,'VEGACHI',2],
				[130,'VENECIA',2],
				[131,'VIGIA DEL FUERTE',2],
				[132,'YALI',2],
				[133,'YARUMAL',2],
				[134,'YOLOMBO',2],
				[135,'YONDO',2],
				[136,'ZARAGOZA',2],
				[137,'ARAUCA',3],
				[138,'ARAUQUITA',3],
				[139,'CRAVO NORTE',3],
				[140,'FORTUL',3],
				[141,'PUERTO RONDON',3],
				[142,'SARAVENA',3],
				[143,'TAME',3],
				[144,'BARANOA',4],
				[145,'BARRANQUILLA',4],
				[146,'CAMPO DE LA CRUZ',4],
				[147,'CANDELARIA',4],
				[148,'GALAPA',4],
				[149,'JUAN DE ACOSTA',4],
				[150,'LURUACO',4],
				[151,'MALAMBO',4],
				[152,'MANATI',4],
				[153,'PALMAR DE VARELA',4],
				[154,'PIOJO',4],
				[155,'POLO NUEVO',4],
				[156,'PONEDERA',4],
				[157,'PUERTO COLOMBIA',4],
				[158,'REPELON',4],
				[159,'SABANAGRANDE',4],
				[160,'SABANALARGA',4],
				[161,'SANTA LUCIA',4],
				[162,'SANTO TOMAS',4],
				[163,'SOLEDAD',4],
				[164,'SUAN',4],
				[165,'TUBARA',4],
				[166,'USIACURI',4],
				[167,'ACHI',5],
				[168,'ALTOS DEL ROSARIO',5],
				[169,'ARENAL',5],
				[170,'ARJONA',5],
				[171,'ARROYOHONDO',5],
				[172,'BARRANCO DE LOBA',5],
				[173,'BRAZUELO DE PAPAYAL',5],
				[174,'CALAMAR',5],
				[175,'CANTAGALLO',5],
				[176,'CARTAGENA DE INDIAS',5],
				[177,'CICUCO',5],
				[178,'CLEMENCIA',5],
				[179,'CORDOBA',5],
				[180,'EL CARMEN DE BOLIVAR',5],
				[181,'EL GUAMO',5],
				[182,'EL PENION',5],
				[183,'HATILLO DE LOBA',5],
				[184,'MAGANGUE',5],
				[185,'MAHATES',5],
				[186,'MARGARITA',5],
				[187,'MARIA LA BAJA',5],
				[188,'MONTECRISTO',5],
				[189,'MORALES',5],
				[190,'MORALES',5],
				[191,'NOROSI',5],
				[192,'PINILLOS',5],
				[193,'REGIDOR',5],
				[194,'RIO VIEJO',5],
				[195,'SAN CRISTOBAL',5],
				[196,'SAN ESTANISLAO',5],
				[197,'SAN FERNANDO',5],
				[198,'SAN JACINTO',5],
				[199,'SAN JACINTO DEL CAUCA',5],
				[200,'SAN JUAN DE NEPOMUCENO',5],
				[201,'SAN MARTIN DE LOBA',5],
				[202,'SAN PABLO',5],
				[203,'SAN PABLO NORTE',5],
				[204,'SANTA CATALINA',5],
				[205,'SANTA CRUZ DE MOMPOX',5],
				[206,'SANTA ROSA',5],
				[207,'SANTA ROSA DEL SUR',5],
				[208,'SIMITI',5],
				[209,'SOPLAVIENTO',5],
				[210,'TALAIGUA NUEVO',5],
				[211,'TUQUISIO',5],
				[212,'TURBACO',5],
				[213,'TURBANA',5],
				[214,'VILLANUEVA',5],
				[215,'ZAMBRANO',5],
				[216,'AQUITANIA',6],
				[217,'ARCABUCO',6],
				[218,'BELÉN',6],
				[219,'BERBEO',6],
				[220,'BETÉITIVA',6],
				[221,'BOAVITA',6],
				[222,'BOYACÁ',6],
				[223,'BRICEÑO',6],
				[224,'BUENAVISTA',6],
				[225,'BUSBANZÁ',6],
				[226,'CALDAS',6],
				[227,'CAMPO HERMOSO',6],
				[228,'CERINZA',6],
				[229,'CHINAVITA',6],
				[230,'CHIQUINQUIRÁ',6],
				[231,'CHÍQUIZA',6],
				[232,'CHISCAS',6],
				[233,'CHITA',6],
				[234,'CHITARAQUE',6],
				[235,'CHIVATÁ',6],
				[236,'CIÉNEGA',6],
				[237,'CÓMBITA',6],
				[238,'COPER',6],
				[239,'CORRALES',6],
				[240,'COVARACHÍA',6],
				[241,'CUBARA',6],
				[242,'CUCAITA',6],
				[243,'CUITIVA',6],
				[244,'DUITAMA',6],
				[245,'EL COCUY',6],
				[246,'EL ESPINO',6],
				[247,'FIRAVITOBA',6],
				[248,'FLORESTA',6],
				[249,'GACHANTIVÁ',6],
				[250,'GÁMEZA',6],
				[251,'GARAGOA',6],
				[252,'GUACAMAYAS',6],
				[253,'GÜICÁN',6],
				[254,'IZA',6],
				[255,'JENESANO',6],
				[256,'JERICÓ',6],
				[257,'LA UVITA',6],
				[258,'LA VICTORIA',6],
				[259,'LABRANZA GRANDE',6],
				[260,'MACANAL',6],
				[261,'MARIPÍ',6],
				[262,'MIRAFLORES',6],
				[263,'MONGUA',6],
				[264,'MONGUÍ',6],
				[265,'MONIQUIRÁ',6],
				[266,'MOTAVITA',6],
				[267,'MUZO',6],
				[268,'NOBSA',6],
				[269,'NUEVO COLÓN',6],
				[270,'OICATÁ',6],
				[271,'OTANCHE',6],
				[272,'PACHAVITA',6],
				[273,'PÁEZ',6],
				[274,'PAIPA',6],
				[275,'PAJARITO',6],
				[276,'PANQUEBA',6],
				[277,'PAUNA',6],
				[278,'PAYA',6],
				[279,'PAZ DE RÍO',6],
				[280,'PESCA',6],
				[281,'PISBA',6],
				[282,'PUERTO BOYACA',6],
				[283,'QUÍPAMA',6],
				[284,'RAMIRIQUÍ',6],
				[285,'RÁQUIRA',6],
				[286,'RONDÓN',6],
				[287,'SABOYÁ',6],
				[288,'SÁCHICA',6],
				[289,'SAMACÁ',6],
				[290,'SAN EDUARDO',6],
				[291,'SAN JOSÉ DE PARE',6],
				[292,'SAN LUÍS DE GACENO',6],
				[293,'SAN MATEO',6],
				[294,'SAN MIGUEL DE SEMA',6],
				[295,'SAN PABLO DE BORBUR',6],
				[296,'SANTA MARÍA',6],
				[297,'SANTA ROSA DE VITERBO',6],
				[298,'SANTA SOFÍA',6],
				[299,'SANTANA',6],
				[300,'SATIVANORTE',6],
				[301,'SATIVASUR',6],
				[302,'SIACHOQUE',6],
				[303,'SOATÁ',6],
				[304,'SOCHA',6],
				[305,'SOCOTÁ',6],
				[306,'SOGAMOSO',6],
				[307,'SORA',6],
				[308,'SORACÁ',6],
				[309,'SOTAQUIRÁ',6],
				[310,'SUSACÓN',6],
				[311,'SUTARMACHÁN',6],
				[312,'TASCO',6],
				[313,'TIBANÁ',6],
				[314,'TIBASOSA',6],
				[315,'TINJACÁ',6],
				[316,'TIPACOQUE',6],
				[317,'TOCA',6],
				[318,'TOGÜÍ',6],
				[319,'TÓPAGA',6],
				[320,'TOTA',6],
				[321,'TUNJA',6],
				[322,'TUNUNGUÁ',6],
				[323,'TURMEQUÉ',6],
				[324,'TUTA',6],
				[325,'TUTAZÁ',6],
				[326,'UMBITA',6],
				[327,'VENTA QUEMADA',6],
				[328,'VILLA DE LEYVA',6],
				[329,'VIRACACHÁ',6],
				[330,'ZETAQUIRA',6],
				[331,'AGUADAS',7],
				[332,'ANSERMA',7],
				[333,'ARANZAZU',7],
				[334,'BELALCAZAR',7],
				[335,'CHINCHINÁ',7],
				[336,'FILADELFIA',7],
				[337,'LA DORADA',7],
				[338,'LA MERCED',7],
				[339,'MANIZALES',7],
				[340,'MANZANARES',7],
				[341,'MARMATO',7],
				[342,'MARQUETALIA',7],
				[343,'MARULANDA',7],
				[344,'NEIRA',7],
				[345,'NORCASIA',7],
				[346,'PACORA',7],
				[347,'PALESTINA',7],
				[348,'PENSILVANIA',7],
				[349,'RIOSUCIO',7],
				[350,'RISARALDA',7],
				[351,'SALAMINA',7],
				[352,'SAMANA',7],
				[353,'SAN JOSE',7],
				[354,'SUPÍA',7],
				[355,'VICTORIA',7],
				[356,'VILLAMARÍA',7],
				[357,'VITERBO',7],
				[358,'ALBANIA',8],
				[359,'BELÉN ANDAQUIES',8],
				[360,'CARTAGENA DEL CHAIRA',8],
				[361,'CURILLO',8],
				[362,'EL DONCELLO',8],
				[363,'EL PAUJIL',8],
				[364,'FLORENCIA',8],
				[365,'LA MONTAÑITA',8],
				[366,'MILÁN',8],
				[367,'MORELIA',8],
				[368,'PUERTO RICO',8],
				[369,'SAN VICENTE DEL CAGUAN',8],
				[370,'SAN JOSÉ DE FRAGUA',8],
				[371,'SOLANO',8],
				[372,'SOLITA',8],
				[373,'VALPARAÍSO',8],
				[374,'AGUAZUL',9],
				[375,'CHAMEZA',9],
				[376,'HATO COROZAL',9],
				[377,'LA SALINA',9],
				[378,'MANÍ',9],
				[379,'MONTERREY',9],
				[380,'NUNCHIA',9],
				[381,'OROCUE',9],
				[382,'PAZ DE ARIPORO',9],
				[383,'PORE',9],
				[384,'RECETOR',9],
				[385,'SABANA LARGA',9],
				[386,'SACAMA',9],
				[387,'SAN LUIS DE PALENQUE',9],
				[388,'TAMARA',9],
				[389,'TAURAMENA',9],
				[390,'TRINIDAD',9],
				[391,'VILLANUEVA',9],
				[392,'YOPAL',9],
				[393,'ALMAGUER',10],
				[394,'ARGELIA',10],
				[395,'BALBOA',10],
				[396,'BOLÍVAR',10],
				[397,'BUENOS AIRES',10],
				[398,'CAJIBIO',10],
				[399,'CALDONO',10],
				[400,'CALOTO',10],
				[401,'CORINTO',10],
				[402,'EL TAMBO',10],
				[403,'FLORENCIA',10],
				[404,'GUAPI',10],
				[405,'INZA',10],
				[406,'JAMBALÓ',10],
				[407,'LA SIERRA',10],
				[408,'LA VEGA',10],
				[409,'LÓPEZ',10],
				[410,'MERCADERES',10],
				[411,'MIRANDA',10],
				[412,'MORALES',10],
				[413,'PADILLA',10],
				[414,'PÁEZ',10],
				[415,'PATIA (EL BORDO)',10],
				[416,'PIAMONTE',10],
				[417,'PIENDAMO',10],
				[418,'POPAYÁN',10],
				[419,'PUERTO TEJADA',10],
				[420,'PURACE',10],
				[421,'ROSAS',10],
				[422,'SAN SEBASTIÁN',10],
				[423,'SANTA ROSA',10],
				[424,'SANTANDER DE QUILICHAO',10],
				[425,'SILVIA',10],
				[426,'SOTARA',10],
				[427,'SUÁREZ',10],
				[428,'SUCRE',10],
				[429,'TIMBÍO',10],
				[430,'TIMBIQUÍ',10],
				[431,'TORIBIO',10],
				[432,'TOTORO',10],
				[433,'VILLA RICA',10],
				[434,'AGUACHICA',11],
				[435,'AGUSTÍN CODAZZI',11],
				[436,'ASTREA',11],
				[437,'BECERRIL',11],
				[438,'BOSCONIA',11],
				[439,'CHIMICHAGUA',11],
				[440,'CHIRIGUANÁ',11],
				[441,'CURUMANÍ',11],
				[442,'EL COPEY',11],
				[443,'EL PASO',11],
				[444,'GAMARRA',11],
				[445,'GONZÁLEZ',11],
				[446,'LA GLORIA',11],
				[447,'LA JAGUA IBIRICO',11],
				[448,'MANAURE BALCÓN DEL CESAR',11],
				[449,'PAILITAS',11],
				[450,'PELAYA',11],
				[451,'PUEBLO BELLO',11],
				[452,'RÍO DE ORO',11],
				[453,'ROBLES (LA PAZ)',11],
				[454,'SAN ALBERTO',11],
				[455,'SAN DIEGO',11],
				[456,'SAN MARTÍN',11],
				[457,'TAMALAMEQUE',11],
				[458,'VALLEDUPAR',11],
				[459,'ACANDI',12],
				[460,'ALTO BAUDO (PIE DE PATO)',12],
				[461,'ATRATO',12],
				[462,'BAGADO',12],
				[463,'BAHIA SOLANO (MUTIS)',12],
				[464,'BAJO BAUDO (PIZARRO)',12],
				[465,'BOJAYA (BELLAVISTA)',12],
				[466,'CANTON DE SAN PABLO',12],
				[467,'CARMEN DEL DARIEN',12],
				[468,'CERTEGUI',12],
				[469,'CONDOTO',12],
				[470,'EL CARMEN',12],
				[471,'ISTMINA',12],
				[472,'JURADO',12],
				[473,'LITORAL DEL SAN JUAN',12],
				[474,'LLORO',12],
				[475,'MEDIO ATRATO',12],
				[476,'MEDIO BAUDO (BOCA DE PEPE)',12],
				[477,'MEDIO SAN JUAN',12],
				[478,'NOVITA',12],
				[479,'NUQUI',12],
				[480,'QUIBDO',12],
				[481,'RIO IRO',12],
				[482,'RIO QUITO',12],
				[483,'RIOSUCIO',12],
				[484,'SAN JOSE DEL PALMAR',12],
				[485,'SIPI',12],
				[486,'TADO',12],
				[487,'UNGUIA',12],
				[488,'UNIÓN PANAMERICANA',12],
				[489,'AYAPEL',13],
				[490,'BUENAVISTA',13],
				[491,'CANALETE',13],
				[492,'CERETÉ',13],
				[493,'CHIMA',13],
				[494,'CHINÚ',13],
				[495,'CIENAGA DE ORO',13],
				[496,'COTORRA',13],
				[497,'LA APARTADA',13],
				[498,'LORICA',13],
				[499,'LOS CÓRDOBAS',13],
				[500,'MOMIL',13],
				[501,'MONTELÍBANO',13],
				[502,'MONTERÍA',13],
				[503,'MOÑITOS',13],
				[504,'PLANETA RICA',13],
				[505,'PUEBLO NUEVO',13],
				[506,'PUERTO ESCONDIDO',13],
				[507,'PUERTO LIBERTADOR',13],
				[508,'PURÍSIMA',13],
				[509,'SAHAGÚN',13],
				[510,'SAN ANDRÉS SOTAVENTO',13],
				[511,'SAN ANTERO',13],
				[512,'SAN BERNARDO VIENTO',13],
				[513,'SAN CARLOS',13],
				[514,'SAN PELAYO',13],
				[515,'TIERRALTA',13],
				[516,'VALENCIA',13],
				[517,'AGUA DE DIOS',14],
				[518,'ALBAN',14],
				[519,'ANAPOIMA',14],
				[520,'ANOLAIMA',14],
				[521,'ARBELAEZ',14],
				[522,'BELTRÁN',14],
				[523,'BITUIMA',14],
				[524,'BOGOTÁ DC',33],
				[525,'BOJACÁ',14],
				[526,'CABRERA',14],
				[527,'CACHIPAY',14],
				[528,'CAJICÁ',14],
				[529,'CAPARRAPÍ',14],
				[530,'CAQUEZA',14],
				[531,'CARMEN DE CARUPA',14],
				[532,'CHAGUANÍ',14],
				[533,'CHIA',14],
				[534,'CHIPAQUE',14],
				[535,'CHOACHÍ',14],
				[536,'CHOCONTÁ',14],
				[537,'COGUA',14],
				[538,'COTA',14],
				[539,'CUCUNUBÁ',14],
				[540,'EL COLEGIO',14],
				[541,'EL PEÑÓN',14],
				[542,'EL ROSAL1',14],
				[543,'FACATATIVA',14],
				[544,'FÓMEQUE',14],
				[545,'FOSCA',14],
				[546,'FUNZA',14],
				[547,'FÚQUENE',14],
				[548,'FUSAGASUGA',14],
				[549,'GACHALÁ',14],
				[550,'GACHANCIPÁ',14],
				[551,'GACHETA',14],
				[552,'GAMA',14],
				[553,'GIRARDOT',14],
				[554,'GRANADA2',14],
				[555,'GUACHETÁ',14],
				[556,'GUADUAS',14],
				[557,'GUASCA',14],
				[558,'GUATAQUÍ',14],
				[559,'GUATAVITA',14],
				[560,'GUAYABAL DE SIQUIMA',14],
				[561,'GUAYABETAL',14],
				[562,'GUTIÉRREZ',14],
				[563,'JERUSALÉN',14],
				[564,'JUNÍN',14],
				[565,'LA CALERA',14],
				[566,'LA MESA',14],
				[567,'LA PALMA',14],
				[568,'LA PEÑA',14],
				[569,'LA VEGA',14],
				[570,'LENGUAZAQUE',14],
				[571,'MACHETÁ',14],
				[572,'MADRID',14],
				[573,'MANTA',14],
				[574,'MEDINA',14],
				[575,'MOSQUERA',14],
				[576,'NARIÑO',14],
				[577,'NEMOCÓN',14],
				[578,'NILO',14],
				[579,'NIMAIMA',14],
				[580,'NOCAIMA',14],
				[581,'OSPINA PÉREZ',14],
				[582,'PACHO',14],
				[583,'PAIME',14],
				[584,'PANDI',14],
				[585,'PARATEBUENO',14],
				[586,'PASCA',14],
				[587,'PUERTO SALGAR',14],
				[588,'PULÍ',14],
				[589,'QUEBRADANEGRA',14],
				[590,'QUETAME',14],
				[591,'QUIPILE',14],
				[592,'RAFAEL REYES',14],
				[593,'RICAURTE',14],
				[594,'SAN ANTONIO DEL TEQUENDAMA',14],
				[595,'SAN BERNARDO',14],
				[596,'SAN CAYETANO',14],
				[597,'SAN FRANCISCO',14],
				[598,'SAN JUAN DE RIOSECO',14],
				[599,'SASAIMA',14],
				[600,'SESQUILÉ',14],
				[601,'SIBATÉ',14],
				[602,'SILVANIA',14],
				[603,'SIMIJACA',14],
				[604,'SOACHA',14],
				[605,'SOPO',14],
				[606,'SUBACHOQUE',14],
				[607,'SUESCA',14],
				[608,'SUPATÁ',14],
				[609,'SUSA',14],
				[610,'SUTATAUSA',14],
				[611,'TABIO',14],
				[612,'TAUSA',14],
				[613,'TENA',14],
				[614,'TENJO',14],
				[615,'TIBACUY',14],
				[616,'TIBIRITA',14],
				[617,'TOCAIMA',14],
				[618,'TOCANCIPÁ',14],
				[619,'TOPAIPÍ',14],
				[620,'UBALÁ',14],
				[621,'UBAQUE',14],
				[622,'UBATÉ',14],
				[623,'UNE',14],
				[624,'UTICA',14],
				[625,'VERGARA',14],
				[626,'VIANI',14],
				[627,'VILLA GOMEZ',14],
				[628,'VILLA PINZÓN',14],
				[629,'VILLETA',14],
				[630,'VIOTA',14],
				[631,'YACOPÍ',14],
				[632,'ZIPACÓN',14],
				[633,'ZIPAQUIRÁ',14],
				[634,'BARRANCO MINAS',15],
				[635,'CACAHUAL',15],
				[636,'INÍRIDA',15],
				[637,'LA GUADALUPE',15],
				[638,'MAPIRIPANA',15],
				[639,'MORICHAL',15],
				[640,'PANA PANA',15],
				[641,'PUERTO COLOMBIA',15],
				[642,'SAN FELIPE',15],
				[643,'CALAMAR',16],
				[644,'EL RETORNO',16],
				[645,'MIRAFLOREZ',16],
				[646,'SAN JOSÉ DEL GUAVIARE',16],
				[647,'ACEVEDO',17],
				[648,'AGRADO',17],
				[649,'AIPE',17],
				[650,'ALGECIRAS',17],
				[651,'ALTAMIRA',17],
				[652,'BARAYA',17],
				[653,'CAMPO ALEGRE',17],
				[654,'COLOMBIA',17],
				[655,'ELIAS',17],
				[656,'GARZÓN',17],
				[657,'GIGANTE',17],
				[658,'GUADALUPE',17],
				[659,'HOBO',17],
				[660,'IQUIRA',17],
				[661,'ISNOS',17],
				[662,'LA ARGENTINA',17],
				[663,'LA PLATA',17],
				[664,'NATAGA',17],
				[665,'NEIVA',17],
				[666,'OPORAPA',17],
				[667,'PAICOL',17],
				[668,'PALERMO',17],
				[669,'PALESTINA',17],
				[670,'PITAL',17],
				[671,'PITALITO',17],
				[672,'RIVERA',17],
				[673,'SALADO BLANCO',17],
				[674,'SAN AGUSTÍN',17],
				[675,'SANTA MARIA',17],
				[676,'SUAZA',17],
				[677,'TARQUI',17],
				[678,'TELLO',17],
				[679,'TERUEL',17],
				[680,'TESALIA',17],
				[681,'TIMANA',17],
				[682,'VILLAVIEJA',17],
				[683,'YAGUARA',17],
				[684,'ALBANIA',18],
				[685,'BARRANCAS',18],
				[686,'DIBULLA',18],
				[687,'DISTRACCIÓN',18],
				[688,'EL MOLINO',18],
				[689,'FONSECA',18],
				[690,'HATO NUEVO',18],
				[691,'LA JAGUA DEL PILAR',18],
				[692,'MAICAO',18],
				[693,'MANAURE',18],
				[694,'RIOHACHA',18],
				[695,'SAN JUAN DEL CESAR',18],
				[696,'URIBIA',18],
				[697,'URUMITA',18],
				[698,'VILLANUEVA',18],
				[699,'ALGARROBO',19],
				[700,'ARACATACA',19],
				[701,'ARIGUANI',19],
				[702,'CERRO SAN ANTONIO',19],
				[703,'CHIVOLO',19],
				[704,'CIENAGA',19],
				[705,'CONCORDIA',19],
				[706,'EL BANCO',19],
				[707,'EL PIÑON',19],
				[708,'EL RETEN',19],
				[709,'FUNDACION',19],
				[710,'GUAMAL',19],
				[711,'NUEVA GRANADA',19],
				[712,'PEDRAZA',19],
				[713,'PIJIÑO DEL CARMEN',19],
				[714,'PIVIJAY',19],
				[715,'PLATO',19],
				[716,'PUEBLO VIEJO',19],
				[717,'REMOLINO',19],
				[718,'SABANAS DE SAN ANGEL',19],
				[719,'SALAMINA',19],
				[720,'SAN SEBASTIAN DE BUENAVISTA',19],
				[721,'SAN ZENON',19],
				[722,'SANTA ANA',19],
				[723,'SANTA BARBARA DE PINTO',19],
				[724,'SANTA MARTA',19],
				[725,'SITIONUEVO',19],
				[726,'TENERIFE',19],
				[727,'ZAPAYAN',19],
				[728,'ZONA BANANERA',19],
				[729,'ACACIAS',20],
				[730,'BARRANCA DE UPIA',20],
				[731,'CABUYARO',20],
				[732,'CASTILLA LA NUEVA',20],
				[733,'CUBARRAL',20],
				[734,'CUMARAL',20],
				[735,'EL CALVARIO',20],
				[736,'EL CASTILLO',20],
				[737,'EL DORADO',20],
				[738,'FUENTE DE ORO',20],
				[739,'GRANADA',20],
				[740,'GUAMAL',20],
				[741,'LA MACARENA',20],
				[742,'LA URIBE',20],
				[743,'LEJANÍAS',20],
				[744,'MAPIRIPÁN',20],
				[745,'MESETAS',20],
				[746,'PUERTO CONCORDIA',20],
				[747,'PUERTO GAITÁN',20],
				[748,'PUERTO LLERAS',20],
				[749,'PUERTO LÓPEZ',20],
				[750,'PUERTO RICO',20],
				[751,'RESTREPO',20],
				[752,'SAN JUAN DE ARAMA',20],
				[753,'SAN CARLOS GUAROA',20],
				[754,'SAN JUANITO',20],
				[755,'SAN MARTÍN',20],
				[756,'VILLAVICENCIO',20],
				[757,'VISTA HERMOSA',20],
				[758,'ALBAN',21],
				[759,'ALDAÑA',21],
				[760,'ANCUYA',21],
				[761,'ARBOLEDA',21],
				[762,'BARBACOAS',21],
				[763,'BELEN',21],
				[764,'BUESACO',21],
				[765,'CHACHAGUI',21],
				[766,'COLON (GENOVA)',21],
				[767,'CONSACA',21],
				[768,'CONTADERO',21],
				[769,'CORDOBA',21],
				[770,'CUASPUD',21],
				[771,'CUMBAL',21],
				[772,'CUMBITARA',21],
				[773,'EL CHARCO',21],
				[774,'EL PEÑOL',21],
				[775,'EL ROSARIO',21],
				[776,'EL TABLÓN',21],
				[777,'EL TAMBO',21],
				[778,'FUNES',21],
				[779,'GUACHUCAL',21],
				[780,'GUAITARILLA',21],
				[781,'GUALMATAN',21],
				[782,'ILES',21],
				[783,'IMUES',21],
				[784,'IPIALES',21],
				[785,'LA CRUZ',21],
				[786,'LA FLORIDA',21],
				[787,'LA LLANADA',21],
				[788,'LA TOLA',21],
				[789,'LA UNION',21],
				[790,'LEIVA',21],
				[791,'LINARES',21],
				[792,'LOS ANDES',21],
				[793,'MAGUI',21],
				[794,'MALLAMA',21],
				[795,'MOSQUEZA',21],
				[796,'NARIÑO',21],
				[797,'OLAYA HERRERA',21],
				[798,'OSPINA',21],
				[799,'PASTO',21],
				[800,'PIZARRO',21],
				[801,'POLICARPA',21],
				[802,'POTOSI',21],
				[803,'PROVIDENCIA',21],
				[804,'PUERRES',21],
				[805,'PUPIALES',21],
				[806,'RICAURTE',21],
				[807,'ROBERTO PAYAN',21],
				[808,'SAMANIEGO',21],
				[809,'SAN BERNARDO',21],
				[810,'SAN LORENZO',21],
				[811,'SAN PABLO',21],
				[812,'SAN PEDRO DE CARTAGO',21],
				[813,'SANDONA',21],
				[814,'SANTA BARBARA',21],
				[815,'SANTACRUZ',21],
				[816,'SAPUYES',21],
				[817,'TAMINANGO',21],
				[818,'TANGUA',21],
				[819,'TUMACO',21],
				[820,'TUQUERRES',21],
				[821,'YACUANQUER',21],
				[822,'ABREGO',22],
				[823,'ARBOLEDAS',22],
				[824,'BOCHALEMA',22],
				[825,'BUCARASICA',22],
				[826,'CÁCHIRA',22],
				[827,'CÁCOTA',22],
				[828,'CHINÁCOTA',22],
				[829,'CHITAGÁ',22],
				[830,'CONVENCIÓN',22],
				[831,'CÚCUTA',22],
				[832,'CUCUTILLA',22],
				[833,'DURANIA',22],
				[834,'EL CARMEN',22],
				[835,'EL TARRA',22],
				[836,'EL ZULIA',22],
				[837,'GRAMALOTE',22],
				[838,'HACARI',22],
				[839,'HERRÁN',22],
				[840,'LA ESPERANZA',22],
				[841,'LA PLAYA',22],
				[842,'LABATECA',22],
				[843,'LOS PATIOS',22],
				[844,'LOURDES',22],
				[845,'MUTISCUA',22],
				[846,'OCAÑA',22],
				[847,'PAMPLONA',22],
				[848,'PAMPLONITA',22],
				[849,'PUERTO SANTANDER',22],
				[850,'RAGONVALIA',22],
				[851,'SALAZAR',22],
				[852,'SAN CALIXTO',22],
				[853,'SAN CAYETANO',22],
				[854,'SANTIAGO',22],
				[855,'SARDINATA',22],
				[856,'SILOS',22],
				[857,'TEORAMA',22],
				[858,'TIBÚ',22],
				[859,'TOLEDO',22],
				[860,'VILLA CARO',22],
				[861,'VILLA DEL ROSARIO',22],
				[862,'COLÓN',23],
				[863,'MOCOA',23],
				[864,'ORITO',23],
				[865,'PUERTO ASÍS',23],
				[866,'PUERTO CAYCEDO',23],
				[867,'PUERTO GUZMÁN',23],
				[868,'PUERTO LEGUÍZAMO',23],
				[869,'SAN FRANCISCO',23],
				[870,'SAN MIGUEL',23],
				[871,'SANTIAGO',23],
				[872,'SIBUNDOY',23],
				[873,'VALLE DEL GUAMUEZ',23],
				[874,'VILLAGARZÓN',23],
				[875,'ARMENIA',24],
				[876,'BUENAVISTA',24],
				[877,'CALARCÁ',24],
				[878,'CIRCASIA',24],
				[879,'CÓRDOBA',24],
				[880,'FILANDIA',24],
				[881,'GÉNOVA',24],
				[882,'LA TEBAIDA',24],
				[883,'MONTENEGRO',24],
				[884,'PIJAO',24],
				[885,'QUIMBAYA',24],
				[886,'SALENTO',24],
				[887,'APIA',25],
				[888,'BALBOA',25],
				[889,'BELÉN DE UMBRÍA',25],
				[890,'DOS QUEBRADAS',25],
				[891,'GUATICA',25],
				[892,'LA CELIA',25],
				[893,'LA VIRGINIA',25],
				[894,'MARSELLA',25],
				[895,'MISTRATO',25],
				[896,'PEREIRA',25],
				[897,'PUEBLO RICO',25],
				[898,'QUINCHÍA',25],
				[899,'SANTA ROSA DE CABAL',25],
				[900,'SANTUARIO',25],
				[901,'PROVIDENCIA',26],
				[902,'SAN ANDRES',26],
				[903,'SANTA CATALINA',26],
				[904,'AGUADA',27],
				[905,'ALBANIA',27],
				[906,'ARATOCA',27],
				[907,'BARBOSA',27],
				[908,'BARICHARA',27],
				[909,'BARRANCABERMEJA',27],
				[910,'BETULIA',27],
				[911,'BOLÍVAR',27],
				[912,'BUCARAMANGA',27],
				[913,'CABRERA',27],
				[914,'CALIFORNIA',27],
				[915,'CAPITANEJO',27],
				[916,'CARCASI',27],
				[917,'CEPITA',27],
				[918,'CERRITO',27],
				[919,'CHARALÁ',27],
				[920,'CHARTA',27],
				[921,'CHIMA',27],
				[922,'CHIPATÁ',27],
				[923,'CIMITARRA',27],
				[924,'CONCEPCIÓN',27],
				[925,'CONFINES',27],
				[926,'CONTRATACIÓN',27],
				[927,'COROMORO',27],
				[928,'CURITÍ',27],
				[929,'EL CARMEN',27],
				[930,'EL GUACAMAYO',27],
				[931,'EL PEÑÓN',27],
				[932,'EL PLAYÓN',27],
				[933,'ENCINO',27],
				[934,'ENCISO',27],
				[935,'FLORIÁN',27],
				[936,'FLORIDABLANCA',27],
				[937,'GALÁN',27],
				[938,'GAMBITA',27],
				[939,'GIRÓN',27],
				[940,'GUACA',27],
				[941,'GUADALUPE',27],
				[942,'GUAPOTA',27],
				[943,'GUAVATÁ',27],
				[944,'GUEPSA',27],
				[945,'HATO',27],
				[946,'JESÚS MARIA',27],
				[947,'JORDÁN',27],
				[948,'LA BELLEZA',27],
				[949,'LA PAZ',27],
				[950,'LANDAZURI',27],
				[951,'LEBRIJA',27],
				[952,'LOS SANTOS',27],
				[953,'MACARAVITA',27],
				[954,'MÁLAGA',27],
				[955,'MATANZA',27],
				[956,'MOGOTES',27],
				[957,'MOLAGAVITA',27],
				[958,'OCAMONTE',27],
				[959,'OIBA',27],
				[960,'ONZAGA',27],
				[961,'PALMAR',27],
				[962,'PALMAS DEL SOCORRO',27],
				[963,'PÁRAMO',27],
				[964,'PIEDECUESTA',27],
				[965,'PINCHOTE',27],
				[966,'PUENTE NACIONAL',27],
				[967,'PUERTO PARRA',27],
				[968,'PUERTO WILCHES',27],
				[969,'RIONEGRO',27],
				[970,'SABANA DE TORRES',27],
				[971,'SAN ANDRÉS',27],
				[972,'SAN BENITO',27],
				[973,'SAN GIL',27],
				[974,'SAN JOAQUÍN',27],
				[975,'SAN JOSÉ DE MIRANDA',27],
				[976,'SAN MIGUEL',27],
				[977,'SAN VICENTE DE CHUCURÍ',27],
				[978,'SANTA BÁRBARA',27],
				[979,'SANTA HELENA',27],
				[980,'SIMACOTA',27],
				[981,'SOCORRO',27],
				[982,'SUAITA',27],
				[983,'SUCRE',27],
				[984,'SURATA',27],
				[985,'TONA',27],
				[986,'VALLE SAN JOSÉ',27],
				[987,'VÉLEZ',27],
				[988,'VETAS',27],
				[989,'VILLANUEVA',27],
				[990,'ZAPATOCA',27],
				[991,'BUENAVISTA',28],
				[992,'CAIMITO',28],
				[993,'CHALÁN',28],
				[994,'COLOSO',28],
				[995,'COROZAL',28],
				[996,'EL ROBLE',28],
				[997,'GALERAS',28],
				[998,'GUARANDA',28],
				[999,'LA UNIÓN',28],
				[1000,'LOS PALMITOS',28],
				[1001,'MAJAGUAL',28],
				[1002,'MORROA',28],
				[1003,'OVEJAS',28],
				[1004,'PALMITO',28],
				[1005,'SAMPUES',28],
				[1006,'SAN BENITO ABAD',28],
				[1007,'SAN JUAN DE BETULIA',28],
				[1008,'SAN MARCOS',28],
				[1009,'SAN ONOFRE',28],
				[1010,'SAN PEDRO',28],
				[1011,'SINCÉ',28],
				[1012,'SINCELEJO',28],
				[1013,'SUCRE',28],
				[1014,'TOLÚ',28],
				[1015,'TOLUVIEJO',28],
				[1016,'ALPUJARRA',29],
				[1017,'ALVARADO',29],
				[1018,'AMBALEMA',29],
				[1019,'ANZOATEGUI',29],
				[1020,'ARMERO (GUAYABAL)',29],
				[1021,'ATACO',29],
				[1022,'CAJAMARCA',29],
				[1023,'CARMEN DE APICALÁ',29],
				[1024,'CASABIANCA',29],
				[1025,'CHAPARRAL',29],
				[1026,'COELLO',29],
				[1027,'COYAIMA',29],
				[1028,'CUNDAY',29],
				[1029,'DOLORES',29],
				[1030,'ESPINAL',29],
				[1031,'FALÁN',29],
				[1032,'FLANDES',29],
				[1033,'FRESNO',29],
				[1034,'GUAMO',29],
				[1035,'HERVEO',29],
				[1036,'HONDA',29],
				[1037,'IBAGUÉ',29],
				[1038,'ICONONZO',29],
				[1039,'LÉRIDA',29],
				[1040,'LÍBANO',29],
				[1041,'MARIQUITA',29],
				[1042,'MELGAR',29],
				[1043,'MURILLO',29],
				[1044,'NATAGAIMA',29],
				[1045,'ORTEGA',29],
				[1046,'PALOCABILDO',29],
				[1047,'PIEDRAS PLANADAS',29],
				[1048,'PRADO',29],
				[1049,'PURIFICACIÓN',29],
				[1050,'RIOBLANCO',29],
				[1051,'RONCESVALLES',29],
				[1052,'ROVIRA',29],
				[1053,'SALDAÑA',29],
				[1054,'SAN ANTONIO',29],
				[1055,'SAN LUIS',29],
				[1056,'SANTA ISABEL',29],
				[1057,'SUÁREZ',29],
				[1058,'VALLE DE SAN JUAN',29],
				[1059,'VENADILLO',29],
				[1060,'VILLAHERMOSA',29],
				[1061,'VILLARRICA',29],
				[1062,'ALCALÁ',30],
				[1063,'ANDALUCÍA',30],
				[1064,'ANSERMA NUEVO',30],
				[1065,'ARGELIA',30],
				[1066,'BOLÍVAR',30],
				[1067,'BUENAVENTURA',30],
				[1068,'BUGA',30],
				[1069,'BUGALAGRANDE',30],
				[1070,'CAICEDONIA',30],
				[1071,'CALI',30],
				[1072,'CALIMA (DARIEN)',30],
				[1073,'CANDELARIA',30],
				[1074,'CARTAGO',30],
				[1075,'DAGUA',30],
				[1076,'EL AGUILA',30],
				[1077,'EL CAIRO',30],
				[1078,'EL CERRITO',30],
				[1079,'EL DOVIO',30],
				[1080,'FLORIDA',30],
				[1081,'GINEBRA GUACARI',30],
				[1082,'JAMUNDÍ',30],
				[1083,'LA CUMBRE',30],
				[1084,'LA UNIÓN',30],
				[1085,'LA VICTORIA',30],
				[1086,'OBANDO',30],
				[1087,'PALMIRA',30],
				[1088,'PRADERA',30],
				[1089,'RESTREPO',30],
				[1090,'RIO FRÍO',30],
				[1091,'ROLDANILLO',30],
				[1092,'SAN PEDRO',30],
				[1093,'SEVILLA',30],
				[1094,'TORO',30],
				[1095,'TRUJILLO',30],
				[1096,'TULÚA',30],
				[1097,'ULLOA',30],
				[1098,'VERSALLES',30],
				[1099,'VIJES',30],
				[1100,'YOTOCO',30],
				[1101,'YUMBO',30],
				[1102,'ZARZAL',30],
				[1103,'CARURÚ',31],
				[1104,'MITÚ',31],
				[1105,'PACOA',31],
				[1106,'PAPUNAUA',31],
				[1107,'TARAIRA',31],
				[1108,'YAVARATÉ',31],
				[1109,'CUMARIBO',32],
				[1110,'LA PRIMAVERA',32],
				[1111,'PUERTO CARREÑO',32],
				[1112,'SANTA ROSALIA',32],
			];

			foreach ($ciudades as $ciudad) {
				$set = [
					'id_ciudad' => $ciudad[0],
					'nombre_ciudad' => $ciudad[1], 
					'id_departamento' => $ciudad[2]
				];
				$unique = [
					'conditional' => 'id_ciudad = ?',
					'params' => ['s',$ciudad[0]]
				];
				CRUD::insert('ciudades',$set,$unique);
			}
		}
	}
?>