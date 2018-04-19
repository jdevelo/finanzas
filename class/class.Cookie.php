<?php 

	/**
	* Cookies
	*/
	class Cookie
	{	

		public static function readCookie($cookieName)
		{	
			if(isset($_COOKIE[$cookieName])) {
			    return unserialize($_COOKIE[$cookieName]);
			}else{
				return false;
			}
		}

		static public function find($cookieName,$column=[])
		{
			/*
				$column = [ key => value ];
			*/
			$cookie = self::readCookie($cookieName);
			$key = array_keys($column)[0];
			$value = $column[array_keys($column)[0]];
			$indice = array_search($value, array_column($cookie, $key));
			
			return $cookie[$indice];
		}

		public static function searchInCookie($arrayCookie=[],$dataToCompare,$compare)
		{	
			$cantidadDatosRepetidos = 0;
			foreach ($arrayCookie as $key => $value) {
				if ($value[$dataToCompare] == $compare) {
					$cantidadDatosRepetidos++;
				}
			}
			return $cantidadDatosRepetidos;
		}

		public static function insertInCookie($cookieName,$keyDataArray=[],$seconds = 2592000,$unique = null)
		{		
			/*	
				$keyDataArray = [ ['key','data'] ];
				$seconds = Seconds Cookie will be before destroy ifself
				$unique = Define if there are data that would be unique in DB.
			*/
			$dataCookie = array();
			$dataCookie = self::readCookie($cookieName);

			if ($dataCookie != false) {				
				$uni = 0;
				if ($unique != null) {
					foreach ($keyDataArray as $value) {
						$uni = $uni + self::searchInCookie($dataCookie,$unique,$value[$unique]);
					}			
				}
				if ($uni > 0) {
					return false;
				}	
			}

		    if (count($dataCookie) == 0) {
		    	$dataCookie = [];
		    }
	
			// Anidando a la lista de deseos
			if (count($dataCookie) > 0) {
				$newCookie = array_merge($dataCookie,$keyDataArray);
			}else{
				$newCookie = $keyDataArray;
			}
			
			self::createCookie($cookieName,$newCookie,$seconds);			
			
			return true;
		}

		public static function createCookie($cookieName,$arrayCookie,$seconds = 2592000)
		{
			 $iTemCad = time() + $seconds;
			return setcookie($cookieName, serialize($arrayCookie), $iTemCad,'/');
		}

		public static function updateCookie($cookieName,$keyCompare=[],$valueToInsert=[])
		{
			/*
				$keyCompare = [ 'key' => 'value' ];
				$valueToInsert = [ 
					['key' => 'data' ]
				];
			*/
			$cookie = self::readCookie($cookieName);

			if ($cookie == null) {
				return false;
			}

			$contar = 0;
			foreach ($cookie as $valueC) {
				if ( $valueC[array_keys($keyCompare)[0]] == $keyCompare[array_keys($keyCompare)[0]] ) {
					foreach ( $valueToInsert as $keyInsert => $dataInsert ) {
						$cookie[$contar][$keyInsert] = $dataInsert;
					}
				}
				$contar++;
			}
			
			return self::createCookie($cookieName,$cookie);
		}

		public static function deleteCookie($cookieName)
		{
			$timeDelete = time() - 12;
	 		unset($_COOKIE[$cookieName]);
			setcookie($cookieName, null, -1, '/');
			return "Cookie Eliminada";
		}


		static public function delValue($cookieName,$column,$value)
		{
			$cookie = self::readCookie($cookieName);
			$indice = array_search($value, array_column($cookie, $column));

			if ($indice !== false) {
				unset($cookie[$indice]);
				$newCookie = array_values($cookie);
				return self::createCookie($cookieName,$newCookie);
			}
			return false;
		}

		public static function agregarEnVistos($productoID)
		{
            $data = [
            		[
            		'id_producto' => $productoID,
            		'date_ls_vistos' => date("Y-m-d H:i:s")
            		]
            	];

            return self::insertInCookie('rce_vis',$data,258000,'id_producto');
		}

		static $_productos_vistos = [];
		public static function productosVistos($productoID = 0,$limit=10)
		{	
			if (isset($_COOKIE['rce_vis'])) {
				$aCarrito = unserialize($_COOKIE['rce_vis']);
                shuffle($aCarrito);

               	$contar = 0;
                foreach ($aCarrito as $key => $value) {
                	if ( $value['id_producto'] != $productoID AND $contar < $limit ) {
                        $where = 'productos.id_producto = ? AND productos_publicados.estado_publicado = ?';
                        $params = array("is",$value['id_producto'],"SI");
                        $join = [
                        	['INNER','productos_cantidad','productos_cantidad.id_producto = productos.id_producto'],
                        	['INNER','productos_imagenes_principales','productos_imagenes_principales.id_producto = productos.id_producto'],
			            	['INNER','categorias','categorias.id_categoria = productos.id_categoria'],
			            	['INNER','categorias_sub','categorias_sub.id_sub_categoria = productos.id_sub_categoria'],
                        	['INNER','productos_publicados','productos_publicados.serie = productos.serie']
                        ];
                        $productos_vistos = CRUD::find('productos','*',$where,$params,$join);

					    while ($pms = $productos_vistos[1]->fetch_assoc()) {

				          	$datoDescuento = self::buscarDescuento($pms['id_producto'],$pms['precio']);

				          	self::$_productos_vistos[$contar] = array(
				                'id_producto' =>  $pms['id_producto'],
				                'serie' =>  $pms['serie'],
				                'nombre_producto' =>  $pms['nombre_producto'],
				                'categoria' => $pms['categoria'],
				                'sub-categoria' => $pms['nombre_sub_categoria'],
				                'vendidos' => $pms['cantidad_salida'],
				                'ruta_img_frontal' =>  $pms['ruta_img_frontal'],
				                'precioAntesDescuento' => $pms['precio'],
				                'porcentajeDescuento' => $datoDescuento['porcentaje'],
				                'descuentoPorProducto' => $datoDescuento['valorDescuento'],
				                'precio' => $datoDescuento['precio_final'] 
				          	);
				          	$contar++;
				    	}    
                	}
                }/*foreach*/
                return self::$_productos_vistos;
			}                    
		}

	}



 ?>