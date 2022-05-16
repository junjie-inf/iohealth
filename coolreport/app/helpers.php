<?php

if ( ! function_exists('array_group'))
{
	/**
	 * Group two arrays combining keys from one and values from other.
	 *
	 * @param  string  $path
	 * @return string
	 */
	function array_group(array $keys, array $values)
	{
		$combine = array();
		
		foreach( $keys as $key => $null )
		{
			if( isset( $values[ $key ]) )	
			{
				$combine[ $key ] = $values[ $key ];
			}
		}
		
		return $combine;
	}
}

if ( ! function_exists('sanitize'))
{
	/**
	 * Group two arrays combining keys from one and values from other.
	 *
	 * @param  string  $path
	 * @return string
	 */
	function sanitize(array $array)
	{
		foreach( $array as $key => $value )
		{
			if( is_array( $value ) )
			{
				$array[ $key ] = sanitize( $value );
			}
			else
			{
				$array[ $key ] = HTML::entities($value);
			}
		}
		
		return $array;
	}
}

if ( ! function_exists('unsanitize'))
{
	/**
	 * Group two arrays combining keys from one and values from other.
	 *
	 * @param  string  $path
	 * @return string
	 */
	function unsanitize(array $array)
	{
		foreach( $array as $key => $value )
		{
			if( is_array( $value ) )
			{
				$array[ $key ] = unsanitize($value);
			}
			else
			{
				$array[ $key ] = is_string($value) ? html_entity_decode($value, ENT_QUOTES, "UTF-8") : $value;
			}
		}
		
		return $array;
	}
}

if( ! function_exists('extractAttr') )
{
	/**
	 * Extrae un atributo de un objeto y devuelve su valor.
	 * Si el atributo no existe, devuelve $default.
	 * 
	 * @param object $object El objeto
	 * @param string $field_name Nombre del atributo
	 * @param mixed $default Valor por defecto si el atributo no existe
	 * 
	 * @return mixed
	 */
	function extractAttr( $object, $field_name, $default = null )
	{
		if( isset($object->$field_name) )
		{
			return $object->$field_name;
		}
		
		return $default;
	}
}

if( ! function_exists('compactJSON') )
{
	/**
	 * Recibe un json como primer parámetro y un conjunto de variables en el resto.
	 * Parsea el json y crea un objeto únicamente con las variables especificadas.
	 * 
	 * @param string $json El string json.
	 * @param string $vars Variable a extraer.
	 * @param mixed $_ [optional]
	 * 
	 * @return mixed
	 */
	function compactJSON($json, $vars, $_ = null)
	{
		if( ! is_null($_) )
		{
			$vars = func_get_args(); 
			
			$json = array_shift($vars);
		}
		
		$array = json_decode($json, true);

		extract($array);
		
		return (object) compact($vars);
	}
}

if( ! function_exists('compactObject') )
{
	/**
	 * Recibe un json como primer parámetro y un conjunto de variables en el resto.
	 * Parsea el json y crea un objeto únicamente con las variables especificadas.
	 * 
	 * @param StdClass $object Object.
	 * @param string $vars Variable a extraer.
	 * @param mixed $_ [optional]
	 * 
	 * @return mixed
	 */
	function compactObject($object, $vars, $_ = null)
	{
		if( ! is_null($_) )
		{
			$vars = func_get_args(); 
			
			$object = array_shift($vars);
		}
		
		$array = get_object_vars($object);

		extract($array);
		
		return (object) compact($vars);
	}
}

if( ! function_exists('array_remove') )
{
	/**
	 * Elimina valores de un array.
	 * 
	 * @param array $array
	 * @param string|array $value
	 * 
	 * @return array
	 */
	function array_remove(&$array, $values)
	{
		if (is_string($values)) $values = func_get_args();
		
		foreach( $values as $value )
		{
			if( ($key = array_search($value, $array)) !== false )
			{
				unset($array[$key]);
			}
		}
	}
}

if( ! function_exists('class_has_trait') )
{
	/**
	 * Devuelve si una clase tiene un trait.
	 * 
	 * @param array $array
	 * @param string|array $value
	 * 
	 * @return array
	 */
	function class_has_trait($object, $trait)
	{
		if( is_null($object) ) return false;
		
		return in_array($trait, class_uses($object));
	}
}