<?php 



/**
* 
*/
class Images
{
	
	// Images
	private $_files_images;
	private $_name;
	public 	$count_imgs;
	private $_contar_uploads = 1;
	public $name_large;
	public $name_small;
	public $name_thumbnail;

	// Extension
	private $_allowedExts = ["jpg", "jpeg", "gif", "png", "JPG", "GIF", "PNG"];

	// Prepare
	private $_resize = [];
	private $_crop;

	// Handdle Errors
	public $error;

	function __construct($name, $extention = [])
	{	
		$this->_name = $name;
		$this->_files_images = $_FILES[$this->_name];
		$this->count_imgs = count($this->_files_images['tmp_name']);

		if ($this->count_imgs == 0) {
			$this->error = 'No se han agregado Imagenes.';
			return false;
		}

		/*Extensiones Validas (Cambias las por defecto)*/
		if (count($extention) > 0) {
			$this->_allowedExts = $extention;
		}
		if ($this->validate()) {
			return true;		
		}		
		return false;
	}

	private function validate()
	{
		for ($i=0; $i < $this->count_imgs; $i++) { 
			$extension_1 = explode(".", $this->_files_images["name"][$i]);
        	$extension = end($extension_1);

			$finfo = new finfo(FILEINFO_MIME_TYPE);
			$fileContents = file_get_contents($_FILES[$this->_name]["tmp_name"][$i]);
			$mimetype = $finfo->buffer($fileContents);

			if ((
				$mimetype != 'image/jpeg' &&
				$mimetype != 'image/png' &&
				$mimetype != 'image/gif' && 
				$mimetype != 'image/gif' ) ||
				(!in_array($extension, $this->_allowedExts))
			   ) {
			   	$this->error = 'La extensión '.$extension.' no corresponde a las permitidas';
				return false;
			}
		}
		return true;
	}


	public function prepareImages($resize = [], $crop = true)
	{
    	/*
			$resize = [
				'file_name', 'alto', 'ancho'
			];
    	*/
		$this->_resize = $resize;
		$this->_crop = $crop;
	}

	public function adjustCounter($counter = 1)
	{
		$this->_contar_uploads = $counter;
	}

	public function addSerial($serial)
	{
		$this->_serial = $serial;
	}

	public function uploadImages( $filename, $dir = false)
	{	
		for ($i=0; $i < $this->count_imgs; $i++) { 
			$extension_1 = explode(".", $this->_files_images["name"][$i]);
        	$extension = end($extension_1);
        	$imagen = $filename.$this->_contar_uploads.'.'.$extension;
        	if (!$dir) {
        		$directorio = dirname(__FILE__); // directorio de tu elección
        	}else{
        		$directorio = $dir;
        	}

            // almacenar imagen en el servidor
            move_uploaded_file($_FILES[$this->_name]['tmp_name'][$i], $directorio.'/'.$imagen);

            if ($this->_crop) {
            	$this->cropImagen( $directorio, $imagen, $extension );
            }

            if ( count($this->_resize) > 0 ) {

				foreach ($this->_resize as $rz) {
					$nombreN = $rz[0].$this->_contar_uploads.'.'.$extension;
					$this->resizeImagen( $directorio, $imagen, $rz[1], $rz[2], $nombreN, $extension );
					$names[] = $nombreN;


					if (count($names) === 3) {
						$set = [
							'serie' => $this->_serial,
							'ruta_img_lg' => $names[0],
							'ruta_img_sm' => $names[1],
							'ruta_img_tn' => $names[2],
						];

						CRUD::insert('productos_imagenes',$set);
						if ( $this->_contar_uploads === 1) {
					 		$this->name_large = $names[0];
					 		$this->name_small = $names[1];
					 		$this->name_thumbnail = $names[2];
				        }
						$names = [];
					}
					
				}		

            }
            $this->_contar_uploads++;
		}
	}

	public function cropImagen($ruta, $nombre, $extension)
	{   
	    $rutaImagenOriginal = $ruta.$nombre;
	    $img_original = $this->createImage( $rutaImagenOriginal, $extension );

	    $size = min(imagesx($img_original), imagesy($img_original));
	    $x = $y = 0;

	    if (imagesx($img_original) == imagesy($img_original)) {
	        imagejpeg($img_original, $ruta.$nombre);  
	        return true;    
	    }elseif ( imagesx($img_original) > imagesy($img_original) ) {
	        $diferencia = imagesx($img_original) - imagesy($img_original);
	        $x = $diferencia/2;
	    }else{
	        $diferencia = imagesy($img_original) - imagesx($img_original);
	        $y = $diferencia/2;
	    }

	    $im2 = imagecrop($img_original, ['x' => $x, 'y' => $y, 'width' => $size, 'height' => $size]);
	    if ($im2 !== FALSE) {
	        imagejpeg($im2, $ruta.$nombre);
	        return true;
	    }
	    return false;
	}

	function resizeImagen($ruta, $nombre, $alto, $ancho,$nombreN,$extension){
	    $rutaImagenOriginal = $ruta.$nombre;
	    
	    $img_original = $this->createImage( $rutaImagenOriginal, $extension );

	    $max_ancho = $ancho;
	    $max_alto = $alto;
	    list($ancho,$alto)=getimagesize($rutaImagenOriginal);    

	    $x_ratio = $max_ancho / $ancho;
	    $y_ratio = $max_alto / $alto;
	    if( ($ancho <= $max_ancho) && ($alto <= $max_alto) ){//Si ancho 
	        $ancho_final = $ancho;
			$alto_final = $alto;
		} elseif (($x_ratio * $alto) < $max_alto){
			$alto_final = ceil($x_ratio * $alto);
			$ancho_final = $max_ancho;
		} else{
			$ancho_final = ceil($y_ratio * $ancho);
			$alto_final = $max_alto;
		}
	    $tmp=imagecreatetruecolor($ancho_final,$alto_final);
	    imagecopyresampled($tmp,$img_original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);
	    imagedestroy($img_original);
	    $calidad=70;
	    imagejpeg($tmp,$ruta.$nombreN,$calidad);
	    
	}


	private function createImage($ruta,$extension)
	{
		if($extension == 'GIF' || $extension == 'gif'){
	    	return imagecreatefromgif($ruta);
	    }
	    if($extension == 'jpg' || $extension == 'JPG'){
	    	return imagecreatefromjpeg($ruta);
	    }
	    if($extension == 'png' || $extension == 'PNG'){
	    	return imagecreatefrompng($ruta);
	    }
	}

	// Creador de folders
	public function folders($array=[])
	{	
		if (count($array) > 0) {
			foreach ($array as $folder) {
				$this->files($folder);
			}
		}
	}

	// Crear directorios
	private function files($folder)
	{	
		$new_folder = DIRECTORIO_ROOT.$folder;
		if (!file_exists($new_folder)) {
		    mkdir($new_folder, 0777, true);
		}
	}


}