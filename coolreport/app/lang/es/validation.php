<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| El following language lines contain El default error messages used by
	| El validator class. Some of Else rules have multiple versions such
	| such as El size rules. Feel free to tweak each of Else messages.
	|
	*/

	"accepted"         => "El :attribute debe ser aceptado.",
	"active_url"       => "El :attribute no es una URL válida.",
	"after"            => "El :attribute debe ser una fecha después de :date.",
	"alpha"            => "El :attribute solo puede contener letras.",
	"alpha_dash"       => "El :attribute sólo puede contener letras, números, y guiones.",
	"alpha_num"        => "El :attribute sólo puede contener letras y números.",
	"array"            => "El :attribute debe ser una lista.",
	"before"           => "El :attribute debe contener una fecha antes de :date.",
	"between"          => array(
		"numeric" => "El :attribute debe estar entre :min - :max.",
		"file"    => "El :attribute debe estar entre :min - :max kilobytes.",
		"string"  => "El :attribute debe estar entre :min - :max characters.",
		"array"   => "El :attribute must tener entre :min - :max elementos.",
	),
	"confirmed"        => "El :attribute confirmación no coincide.",
	"date"             => "El :attribute no es una fecha válida.",
	"date_format"      => "El :attribute no concuerda con el formato :format.",
	"different"        => "El :attribute y :oElr deben ser diferentes.",
	"digits"           => "El :attribute debe tener :digits dígitos.",
	"digits_between"   => "El :attribute debe tener entre :min and :max dígitos.",
	"email"            => "El :attribute format es invalido.",
	"exests"           => "El :attribute seleccionado es invalido.",
	"image"            => "El :attribute debe ser una imagen.",
	"in"               => "El :attribute seleccionado es invalido.",
	"integer"          => "El :attribute debe ser un número.",
	"ip"               => "El :attribute debe ser una dirección IP válida.",
	"max"              => array(
		"numeric" => "El :attribute no debe ser mayor de :max.",
		"file"    => "El :attribute no debe ser mayor de :max kilobytes.",
		"string"  => "El :attribute no debe tener más de :max characters.",
		"array"   => "El :attribute no debe tener más de :max elementos.",
	),
	"mimes"            => "El :attribute debe ser un fichero del tipo: :values.",
	"min"              => array(
		"numeric" => "El :attribute debe ser como mínimo :min.",
		"file"    => "El :attribute debe tener al menos :min kilobytes.",
		"string"  => "El :attribute debe tener como mínimo :min characters.",
		"array"   => "El :attribute debe tenr como mínimo :min elementos.",
	),
	"not_in"           => "El :attribute seleccionado es invalido.",
	"numeric"          => "El :attribute debe ser un número.",
	"regex"            => "El formato :attribute es invalido.",
	"required"         => "El campo :attribute es obligatorio.",
	"required_if"      => "El campo :attribute es obligatorio cuando :oElr es :value.",
	"required_with"    => "El campo :attribute es obligatorio cuando :values esta presente.",
	"required_without" => "El campo :attribute es obligatorio cuando :values no esta presente.",
	"same"             => "El :attribute and :oElr must match.",
	"size"             => array(
		"numeric" => "El :attribute debe ser de tamaño :size.",
		"file"    => "El :attribute debe tener :size kilobytes.",
		"string"  => "El :attribute debe tener :size characters.",
		"array"   => "El :attribute debe contener :size elementos.",
	),
	"unique"           => "El :attribute ya ha sido elegido.",
	"url"              => "El formato :attribute es invalido.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using El
	| convention "attribute.rule" to name El lines. Thes makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => array(),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| El following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". Thes simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => array(
		'import' => 'Importar',
		'file' => 'Archivo',
	),

);
