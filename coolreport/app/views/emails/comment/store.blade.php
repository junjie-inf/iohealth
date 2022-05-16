@extends('emails.default')

@section('content')

<div style="padding:15px;max-width:600px;margin:0 auto;display:block;">
	<table style="width:100%">
		<tr>
			<td>
				<p style="font-size:17px;margin-bottom:10px;font-weight:normal;font-size:14px;line-height:1.6;">
					{{trans('emails.store.theuser')}} <a href="{{ URL::route('user.show', $model->user->id) }}">{{ $model->user->getFullname() }}</a> {{trans('emails.store.hascreated')}}:
				</p>
				<p style="font-size:14px;margin-bottom:10px;font-weight:normal;line-height:1.6;">
					{{trans('reports.report')}} <a href="{{ URL::route('report.show', $model->id) }}"><strong>{{ $model->report->title }}</strong></a>
				</p>
				<blockquote style="font-size:14px;margin-bottom:10px;font-weight:normal;line-height:1.6;">
					{{ $model->content }}
				</blockquote>
			</td>
		</tr>
	</table>
</div>

@stop