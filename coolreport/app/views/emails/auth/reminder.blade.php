<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>{{trans('emails.reminder.passwordreset')}}</h2>

		<div>
			{{trans('emails.reminder.completeform')}} {{ URL::to('password/reset', array($token)) }}.
		</div>
	</body>
</html>