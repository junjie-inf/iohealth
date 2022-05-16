<?php namespace Nimbeo;

use Illuminate\View\Compilers\BladeCompiler as ExtensibleBlade;

class BladeCompiler extends ExtensibleBlade {

	/**
	 * Moar compiler functions.
	 * 
	 * @var type 
	 */
	protected $spreads = array(
		'Assert',
		'Component',
		'InlineCode',
		'Isset',
		'EndIsset',
		'RawCode',		
	);

	/**
	 * Syntax: 
	 *	{% php code %}
	 * 
	 * @param type $value
	 * @return type
	 */
	protected function spreadRawCode($value)
	{
		$pattern = '/{\%((.|\s)*?)\%}/';

		return preg_replace($pattern, '<?php $1 ?>', $value);
	}
	
	/**
	 * Syntax: 
	 *	@php(phpcode)
	 * 
	 * @param type $value
	 * @return type
	 */
	protected function spreadInlineCode($value)
	{
		$pattern = $this->createBracketMatcher('php');

		return preg_replace($pattern, '$1<?php $2; ?>', $value);
	}
	
	/**
	 * Syntax: 
	 *	@assert('var')
	 * 
	 * @param type $value
	 * @return type
	 */
	protected function spreadAssert($value)
	{
		$pattern = $this->createBracketMatcher('assert');

		return preg_replace($pattern, '$1<?php if( ! isset(\${$2}) ) throw new Exception("Undefined variable [\${$2}]."); ?>', $value);
	}
	
	/**
	 * Syntax: 
	 *	@isset('var')
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected function spreadIsset($value)
	{
		$pattern = $this->createBracketMatcher('isset');

		return preg_replace($pattern, '$1<?php if ( isset(\${$2}) ): ?>', $value);
	}
	
	/**
	 * Syntax: 
	 *	@endisset
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected function spreadEndIsset($value)
	{
		$pattern = $this->createPlainMatcher('endisset');

		return preg_replace($pattern, '$1<?php endif; ?>$2', $value);
	}
	
	/**
	 * Syntax: 
	 *	@component('view', array $data)
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected function spreadComponent($value)
	{
		$pattern = $this->createMatcher('component');

		return preg_replace($pattern, '$1<?php echo $__env->make$2->render(); ?>', $value);
	}
	
	/**
	 * Compile the given Blade template contents.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function compileString($value)
	{
		foreach ($this->spreads as $spread)
		{
			$value = $this->{"spread{$spread}"}($value);
		}

		return parent::compileString($value);
	}
	
	/**
	 * Get the regular expression for a generic Blade function.
	 *
	 * @param  string  $function
	 * @return string
	 */
	public function createBracketMatcher($function)
	{
		return '/(?<!\w)(\s*)@'.$function.'\s*\((.+)\)/';
	}

}