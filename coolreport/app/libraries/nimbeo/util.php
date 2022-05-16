<?php namespace Nimbeo;

class Util {

	/*
	 * cleanForUrl(): prepara un string para ser parte de una URL.
	 *					Ejemplo: 'Antonio J. García' => 'antonio-j-garcia'
	 * 
	 * @param string $string	String a preparar
	 * @return string			String preparado
	 */
	public static function cleanForUrl( $string )
	{
		$string = trim($string); // Quito espacios al principio y al final

		// Copiado de normalize():
		$table = array(
			'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
			'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
			'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
			'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
			'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
			'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
			'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
			'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',
		);
		$string = strtr($string, $table); // Sustituyo con la tabla de caracteres extraños
		// -----------------------

		$string = strtolower($string); // Convierto todo a minúsculas
		$string = preg_replace("/[^a-z0-9_-]/", '-', $string); // Sustituyo por '-' todo lo que no sean caracteres a-z, 0-9, _ o -
		$string = str_replace('---', '-', $string); // Sustituyo los posibles '---' por un solo '-';
		$string = str_replace('--', '-', $string); // Sustituyo los posibles '--' por un solo '-';

		return $string;
	}

	/**
	 * cleanForSearch(): prepara un string (nombre de asignatura, por ejemplo) para buscarlo en la BD.
	 * Fuente: http://foros.ovh.es/showthread.php?t=6076
	 * 
	 * @param type $cadena
	 * @return type
	 */
	public static function cleanForSearch( $sef )
	{
		$sef = trim($sef);
		$sef = mb_strtolower($sef, 'UTF-8'); // Convierto todo a minúsculas

		/*
		 * Quitamos las tildes y le cambia la "ñ" por la "n" y la "ç" por la "c"
		 */
		//$sef = strtr($sef, "áéíóúÁÉÍÓÚñÑç", "aeiouAEIOUnNc");

			#debug(__FUNCTION__.' name antes: ['.$sef.']');
		/*
		 * Cambiamos tildes, "ñ" y "ç" por % (comodín MySQL, para que busque con o sin estos caracteres)
		 */ //$sef = strtr($sef, "áéíóúÁÉÍÓÚñÑç", "%%%%%%%%%%%%%");
		$sef = preg_replace("/á|é|í|ó|ú|ñ|ç|\?/", '%', $sef);

			#debug(__FUNCTION__.' name después: ['.$sef.']');
		/*
		 * Quitamos los símbolos "¡¿?!^':@#$%&"~+* /|\[](){}  >>> rectificado para no eliminar '%'
		 */
		//$sef = preg_replace("/¡|¿|\?|!|\^|'|:|@|#|\$|%|&|\"|~|\+|\*|\/|\||\\|\[|\]|\(|\)|\{|\}/", "", $sef);
		$sef = preg_replace("/¡|¿|\?|!|\^|'|:|@|#|\$|&|\"|~|\+|\*|\/|\||\\|\[|\]|\(|\)|\{|\}/", "", $sef);

		/*
		 * Cambiamos por un único guión, el siguiente conjunto:
		 *		espacio en blanco guión espacio en blanco
		 *		guión espacio en blanco
		 *		espacio en blanco guion
		 *		espacio en blanco
		 */
		$sef = preg_replace("/( - |- | -| )/", "-", $sef);

		/*
		 * Cambiamos cada vez que encuentra 2 o más guiones juntos, por un solo guion
		 */
		$sef = preg_replace("/-{2,}/", "-", $sef);

		/*
		 * Quitamos los guiones del principio y final de la cadena
		 */
		$sef = preg_replace("/^-|-+$/", "", $sef);

		/*
		 * Cambiamos los guiones por espacios
		 */
		$sef = str_replace("-", " ", $sef);

		return $sef;
	}


	/*
	 * checkEmail(): verifica y valida una direccion de email
	 * @param mixed $email
	 * 
	 */
	public static function checkEmail( $email )
	{
		// Comprobamos si se verifica el formato
		if( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) 
		{
			// Separamos la direccion
			list( $user, $domaine ) = explode( "@", $email, 2 );

			// Verificamos dns
			if( checkdnsrr( $domaine, "MX" ) == false && checkdnsrr( $domaine, "A" ) == false )
			{
				return false; // echo 'Mail OK but invalid domain';
			}
			else 
			{
				return true; // echo'Mail ok';
			}
		}
		else 
		{
			return false; // echo 'Invalid Mail';
		}
	}

	
	/**
	 * Escribe en una imagen un texto dividido en varias líneas
	 * 
	 * @param resource $image
	 * @param int $size
	 * @param int $angle
	 * @param int $x
	 * @param int $y
	 * @param int $color
	 * @param string $fontfile
	 * @param string $text
	 * @param int $im_x
	 * @param boolean $center
	 * @param int $spacing
	 */
	public static function imagettfmultilinetext( $image, $size, $angle, $x, $y, $color, $fontfile, $text, $im_x, $center = true, $spacing = 1 )
	{
		$lines = explode("\n", $text);

		for( $i = 0; $i < count($lines); $i++ ){
			// Calcula la caja circundante en píxeles del texto
			$tb = imagettfbbox( $size, 0, $fontfile, $text );

			// Calculo coordenada X del texto para que quede centrado en la caja
			if( $center ) $x = ceil(($im_x - $tb[2]) / 2); // lower left X coordinate for text

			// Calculo coordenada Y del texto
			$newY = $y + ($i * $size * $spacing);

			// Pinto la linea de texto
			imagettftext($image, $size, $angle, $x, $newY, $color, $fontfile, $lines[$i]);
		}
	}
	


	/**
	 *				-----------------------------
	 *				 Written by Unnu.es (C) 2012
	 *				-----------------------------
	 * getYoutubeThumb(): devuelve la URL de una thumbnail de un video alojado en YouTube.
	 * 
	 *		YouTube ofrece 4 thumbnails de cada video, seleccionables mediante el parametro $num.
	 *		Los tamaños de las thumbnails son:
	 *			0: 480x360 px
	 *			1-3: 120x90 px
	 * 
	 * @param string $url_video	URL del video (de la forma "http://www.youtube.com/watch?v=Z6wpku7uciI&feature=...")
	 * @param int $num			Numero de thumbnail deseada
	 * @return string			URL de la thumbnail
	 */
	public static function getYoutubeThumb( $tag, $num = 0 )
	{
		/*parse_str( parse_url( $url_video, PHP_URL_QUERY ) );
		$id = $v;*/
		return 'http://img.youtube.com/vi/' .$tag. '/' .$num. '.jpg';
	}


	/**
	 *				-----------------------------
	 *				 Written by Unnu.es (C) 2012
	 *				-----------------------------
	 * getYoutubeURL(): devuelve la URL de un video alojado en YouTube.
	 * 
	 * @param string $tag		Codigo del video
	 * @return string			URL del video
	 */
	public static function getYoutubeURL( $tag, $autoplay = 0 )
	{
		return 'http://www.youtube.com/embed/' . $tag . '?autoplay=' . $autoplay . '&modestbranding=1&rel=0&showinfo=0&autohide=1&enablejsapi=1&theme=light';
	}
	
	

	/**
	 * Convierte una fecha recibida a formato "Hace X minutos"
	 * 
	 * @param string $date La fecha
	 * @param boolean $ucfirst Si es true, el string devuelto comienza con mayúscula
	 * @param string $lang Idioma elegido (disponibles 'es' y 'en')
	 * @return string
	 */
	public static function prettyDate( $date, $ucfirst = true, $lang = 'es' )
	{		
		$words = array(
			'en' => array(
				's' => 'second',	'ss' => 'seconds',
				'i' => 'minute',	'is' => 'minutes',
				'h' => 'hour',		'hs' => 'hours',
				'd' => 'day',		'ds' => 'days',
				'm' => 'month',		'ms' => 'months',
				'y' => 'year',		'ys' => 'years',
				'format' => '%s agor'
			),
			'es' => array(
				's' => 'segundo',	'ss' => 'segundos',
				'i' => 'minuto',	'is' => 'minutos',
				'h' => 'hora',		'hs' => 'horas',
				'd' => 'día',		'ds' => 'días',
				'm' => 'mes',		'ms' => 'meses',
				'y' => 'año',		'ys' => 'años',
				'format' => 'hace %s'
			),
		);
		
		$time = strtotime($date);
		$now = time();
		$ago = $now - $time;
		if ($ago < 60) {
			$when = round($ago);
			$what = $words[$lang][ ($when == 1) ? "s" : "ss" ];
		} elseif ($ago < 3600) {
			$when = round($ago / 60);
			$what = $words[$lang][($when == 1) ? "i" : "is" ];
		} elseif ($ago >= 3600 && $ago < 86400) {
			$when = round($ago / 60 / 60);
			$what = $words[$lang][($when == 1) ? "h" : "hs" ];
		} elseif ($ago >= 86400 && $ago < 2629743.83) {
			$when = round($ago / 60 / 60 / 24);
			$what = $words[$lang][($when == 1) ? "d" : "ds" ];
		} elseif ($ago >= 2629743.83 && $ago < 31556926) {
			$when = round($ago / 60 / 60 / 24 / 30.4375);
			$what = $words[$lang][($when == 1) ? "m" : "ms" ];
		} else {
			$when = round($ago / 60 / 60 / 24 / 365);
			$what = $words[$lang][($when == 1) ? "y" : "ys" ];
		}
		
		$final = sprintf($words[$lang]['format'], "$when $what" );

		return ( $ucfirst ? ucfirst($final) : $final );
	}
	
	
	/**
	 * Convert a number in bytes, kilobytes...
	 *
	 * @param int $bytes Number to convert
	 * @return string Number converted and formatted (with 2 decimals if >= 1MB)
	 */
	public static function bytes_format( $bytes )
	{
		$unim = array('bytes', 'KB', 'MB', 'GB', 'TB', 'PB');
		$c = 0;
		while ($bytes >= 1024) {
			$c++;
			$bytes /= 1024;
		}
		return number_format($bytes, ($c ? 2 : 0), ',', '.') . ' ' . $unim[$c];
	}
	
	/**
	 * String to int.
	 * 
	 * @param type $string
	 * @return type
	 */
	public static function stringToInteger($string) 
	{
		$output = 0;
		
		for ($i = 0; $i < strlen($string); $i++) 
		{
			$output += ord($string[$i]);
		}
		
		return $output;
	}
	
	/**
	 * Get random color.
	 * 
	 * @param type $id
	 * @return string
	 */
	public static function getColor($id)
	{
		$colors = array(
			'#ff7f0e',
			'#000000',
			'#030303'
		);
		
		return $colors[$id % count($colors)];
	}
	
	/**
	 * Return disk usage.
	 * 
	 * @return type
	 */
	public static function getDiskUsage()
	{
		$total = disk_total_space( storage_path() );
		$free = disk_free_space( storage_path() );
		return ($total - $free) / $total * 100;
	}
	
	/**
	 * Return percent of templates used.
	 * 
	 * @return type
	 */
	public static function getTemplatesUsedPercent()
	{
		$total = \Template::all()->count();
		
		$used = \Report::all()->filter(function($report){
			if( ! is_null($report->templates) &&  $report->templates->count() == 0 )
				return $report;
		});

		$perc = ($total - $used->count()) / $total * 100;

		return number_format( $perc, 2, ',', '.' );
	}

	/**
	* Return dashboard
	*
	*
	*/
	public static function dashboard()
	{
		$value = '';
		$reports = \Report::all();
		$templates = array();
		$putabusqueda = '';

		foreach ( $reports as $report ){
			if( empty($templates) )
			{
				$templates[$report->template->id] = array('title' => $report->template->title, 'informes' => 1, 'elementos' => array());
				
				foreach ( $report->datum as $field )
				{	
					$templates[$report->template->id]['elementos'][] = $field->value;
				}
			}
			else
			{
				if ( empty($templates[$report->template->id]) )
				{
					$templates[$report->template->id] = array('title' => $report->template->title, 'informes' => 1, 'elementos' => array());
				
					foreach ( $report->datum as $field )
					{	
						$templates[$report->template->id]['elementos'][] = $field->value;
					}
				}
				else
				{
					$templates[$report->template->id]['informes']++; 
					foreach ( $report->datum as $field )
					{	
						$templates[$report->template->id]['elementos'][] = $field->value;
					}
				}
			}
		}

		foreach ( $templates as $template)
		{
				$titulo = $template['title'];

				$value .= '<div class="box-content">';
				$value .= '<div class="box-header">';
				$value .= '<h2><i class="icon-dashboard"></i><span class="break"></span>'. $titulo .'</h2>';
				$value .= '<div class="box-icon">';
				$value .= '</div>';
				$value .= '</div>';
				$value .= '<table class="table table-striped table-bordered table-condensed bootstrap-datatable vertical-middle">';
				$value .= '<thead>';
				$value .= '<tr>';
				$value .= '<th>Informes completados</th>';
				$value .= '<th>Media</th>';
				$value .= '<th>Desv. Típica</th>';
				$value .= '</tr>';
				$value .= '<td width="40%">' .  $template['informes'] . '</td>';
				$value .= '<td width="30%">' . round(Util::media($template['elementos']),2) . '</td>';
				$value .= '<td width="30%">' . round(Util::desviacionEstandar($template['elementos']),2) . '</td>';
				$value .= '</thead><tbody></tbody></table></div>';	
		}

		return $value;
	}

	public static function media($array)
	{
		$suma = 0;
		$valores = count($array);

		foreach ($array as $elemento)
		{
			$suma += $elemento;
		}

		return ($suma/$valores);
	}

	public static function desviacionEstandar($array)
	{
		$suma = 0;
		$valores = count($array);

		foreach ($array as $elemento)
		{
			$suma += pow($elemento - Util::media($array),2);
		}

	    return sqrt($suma/$valores);
	}
}