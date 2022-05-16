<?php namespace Nimbeo;

use Nimbeo\BladeCompiler;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\CompilerEngine;

class NimbeoServiceProvider extends ServiceProvider {

	/**
	 * Provider components
	 * 
	 * @var type 
	 */
	protected $components = array(
		'Blade',
		'Mail',
		'TemplateBuilder',
	);
	
	/**
	 * Register components
	 */
    public function register()
    {
		foreach ($this->components as $component)
        {
            $this->{'register'.$component}();
        }
    }
	
	/**
	 * Overwrite blade engine.
	 * 
	 * @return type
	 */
	protected function registerBlade()
	{
		$app = $this->app;

		$app['view.engine.resolver']->register('blade', function() use ($app)
		{
			$cache = $app['path.storage'].'/views';

			// The Compiler engine requires an instance of the CompilerInterface, which in
			// this case will be the Blade compiler, so we'll first create the compiler
			// instance to pass into the engine so it can compile the views properly.
			$compiler = new BladeCompiler($app['files'], $cache);

			return new CompilerEngine($compiler, $app['files']);
		});
	}
	
	/**
	 * Register TemplateBuilder
	 * 
	 * @return type
	 */
	protected function registerTemplateBuilder()
	{
		$this->app['templatebuilder'] = $this->app->share(function($app)
        {
            return new TemplateBuilder;
        });
	}

	/**
	 * Register TemplateBuilder
	 * 
	 * @return type
	 */
	protected function registerMail()
	{
		$this->app['simplemail'] = $this->app->share(function($app)
        {
            return new Mail;
        });
	}
}