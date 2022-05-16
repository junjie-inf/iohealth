@extends('emails.default')

@section('content')

<div style="padding:15px;max-width:600px;margin:0 auto;display:block;">
	<table style="width:100%">
		<tr>
			<td>
				<p style="font-size:17px;margin-bottom:10px;font-weight:normal;font-size:14px;line-height:1.6;">
					El usuario <a href="{{ URL::route('user.show', $model->user->id) }}">{{ $model->user->getFullname() }}</a> ha eliminado un informe:
				</p>
				<p style="font-size:14px;margin-bottom:10px;font-weight:normal;line-height:1.6;">
					<strong>{{ $model->title }}</strong>
				</p>
			</td>
		</tr>
	</table>
</div>

@stop