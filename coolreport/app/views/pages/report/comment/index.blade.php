@extends('templates.default')

@section('title', trans('sections.report.comment.index' ))

@section('content')

<div class="row">		
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-file-text-alt"></i><span class="break"></span><span class="hidden-sm hidden-xs">{{ trans('sections.report.comment.create') }}</span></h2>
			</div>
			
			<div class="box-content">
				<ul class="nav tab-menu nav-tabs">
					@if( $data->editable )
						<li><a href="{{ URL::route('report.edit', $data->id) }}"><i class="icon-pencil"></i> {{ trans('comments.edit') }}</a></li>
					@endif
					<li><a href="{{ URL::to('report') }}"><i class="icon-arrow-left"></i> {{ trans('comments.backtolist') }}</a></li>
					
					<li class="active"><a href="{{ URL::route('report.{report}.comment.index', $data->id) }}">{{ trans('comments.comments') }}</a></li>
					<li><a href="{{ URL::route('report.show', $data->id) }}">{{ trans('comments.info') }}</a></li>
				</ul>

				<div class="row margin-top-20">
					<div class="col-lg-12 discussions">
						<ul>
							<li class="clearfix">
								<form id="" action="" method="POST" class="crs_nomargin" accept-charset="UTF-8" data-report="{{ $data->id }}">
									<textarea class="diss-form input-limiter" name="new_msg_text" data-limit="500" data-clase_alert="text-error" placeholder={{ trans('comments.write') }} required style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 44px;"></textarea>
									<button type="submit" class="btn btn-primary margin-top-10 send-comment" data-loading-text="<i class='icon-spinner icon-spin'></i> {{ trans('comments.sending') }}">{{ trans('comments.send') }}</button>
								</form>
							</li>

							@foreach( $data->comments as $comment )
								<li data-id="{{ $comment->id }}" data-parent-id="{{ $data->id }}" data-type="comment" class="action-data">
									<div class="name"><a href="{{ $comment->user->getUrl() }}" title="View profile">{{ $comment->user->getFullname() }}</a></div>
									<div class="date">{{ $comment->created_at }}</div>
									@if( $comment->removable )
										<div class="delete action-delete"><i class="icon-remove"></i></div>
									@endif
									<span class="pull-right" style="margin-top: 30px">
										<a class="btn btn-success action-vote {{ lcfirst(get_class($comment)) }}-{{ $comment->id }}" href="javascript:void(0)" {{ ($comment->user == Auth::user() || Auth::user()->voted($comment->id, "Comment")) ? 'disabled' : '' }} 
											data-vote-type="{{ lcfirst(get_class($comment)) }}"
											data-vote-id="{{ $comment->id }}"
											data-loading-text="<i class='icon-spinner icon-spin'></i>"
											title="Click para votar positivo">
												<i class="icon-thumbs-up"></i> {{ ($comment->user == Auth::user() || Auth::user()->voted($comment->id, "Comment")) ? $comment->votes()->whereValue(true)->count() : '' }}
										</a>
										<a class="btn btn-danger action-unvote {{ lcfirst(get_class($comment)) }}-{{ $comment->id }}" href="javascript:void(0)" {{ ($comment->user == Auth::user() || Auth::user()->voted($comment->id, "Comment")) ? 'disabled' : '' }}
											data-vote-type="{{ lcfirst(get_class($comment)) }}"
											data-vote-id="{{ $comment->id }}""
											data-loading-text="<i class='icon-spinner icon-spin'></i>"
											title="Click para votar negativo">
												<i class="icon-thumbs-down"></i> {{ ($comment->user == Auth::user() || Auth::user()->voted($comment->id, "Comment")) ? $comment->votes()->whereValue(false)->count() : '' }}
										</a>
									</span>
									<div class="message">{{ nl2br( $comment->content ) }}</div>
								</li>
							@endforeach
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div><!--/col-->

</div><!--/row-->
@stop


@section('specific-javascript-plugins')
@stop


@section('custom-javascript')
	{{-- inline scripts related to this page --}}
@stop