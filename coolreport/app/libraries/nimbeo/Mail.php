<?php namespace Nimbeo;

use Lang;

class Mail {

	/**
	 * App mailer.
	 * 
	 * @var Illuminate\Mail\Mailer
	 */
	protected $mailer;

	/**
	 * Constructor.
	 */
	function __construct() 
	{
		$this->mailer = app('mailer');
	}

	/**
	 * Bluk mail.
	 * 
	 * @param  string $blueprint
	 * @param  Model $model
	 * @param  Transversable  $users
	 * @return int
	 */
	public function bulk($blueprint, $model, $users)
	{
		$count = 0;

		foreach( $users as $user )
		{
			$count += $this->send($blueprint, $model, $user);
		}

		return $count;
	}

	/**
	 * Send email to user.
	 * 
	 * @param  string $blueprint
	 * @param  Model $model
	 * @param  User $user
	 * @return int
	 */
	public function send($blueprint, $model, $user)
	{
		$data = array(
			'model' => $model,
			'instance' => strtolower(get_class($model))
		);

		return $this->mailer->send('emails.' . $blueprint, $data, function($message) use ($blueprint, $model, $user)
		{
			$message->to($user->email, $user->getFullname())
					->subject(Lang::get('emails.subject.' . $blueprint, array_dot(json_decode($model->toJson(), true))));
		});
	}

	/**
	 * Version 'cutre' para notificar la resolución de incidencias al usuario que la genero. Send email to user.
	 * 
	 * @param  Model $model
	 * @param  User $user
	 * @return int
	 */	
	public function sendUser($model, $user, $subject)
	{
		$data = array(
			'model' => $model,
			'instance' => 'report'
		);

		return $this->mailer->send('emails.report.store', $data, function($message) use ($user, $subject)
		{
			$message->to($user->email, $user->getFullname())
					->subject($subject);
		});
	}

	/**
	 * Version 'cutre' para notificar la accion asociada al analisis semantico. Send email to user.
	 * 
	 * @param  Model $model
	 * @param  User $user
	 * @return int
	 */	
	public function sendAction($model, $mail, $dpto, $subject)
	{
		$data = array(
			'model' => $model,
			'instance' => 'report'
		);

		return $this->mailer->send('emails.report.store', $data, function($message) use ($mail, $dpto, $subject)
		{
			$message->to($mail, $dpto)
					->subject($subject);
		});
	}

}
