@extends('templates.default')

@section('title', Auth::user()->getFullname())

@section('content')

{{-- PUBLIC PROFILE --}}

<!--=== Content Part ===-->
<div class="container">
	
	<div class="page-header">
		<h1>
			{{ $user->getFullname() }}

			<!--<p style="font-size: small">Karma: {{ $user->getReputation() }}</p>-->
			
			@if( $user->getKey() != Auth::user()->getKey() )
				<a class="btn btn-danger action-unfollow" href="javascript:void(0)" style="{{ $user->getUsersFromFollowers()->contains(Auth::user()->getKey()) ? '' : 'display:none' }}"
					data-followable-type="{{ lcfirst(get_class($user)) }}"
					data-followable-id="{{ $user->id }}"
					data-toggle="true"
					data-loading-text="<i class='icon-spinner icon-spin'></i>"
					title="Click to unfollow this">
						<i class="icon-eye-close"></i> Unfollow
				</a>
			@endif

			<span class="pull-right">
				<small><i class="icon-user"></i> {{ trans('sections.profile.show') }}</small>
			</span>
		</h1>
	</div>
	</br>

	<div class="row">
		
		<div class="col-sm-7">
			<dl class="dl-horizontal">
				<dt>Role</dt>
				<dd>{{ $user->role->title }}</dd>
				<dt>Group</dt>
				<dd>{{ $user->group->title }}</dd>
			</dl>
		
		</div>
	
		<div class="col-sm-2">
			<a class="quick-button disabled" href="javascript:void(0)">
				<i class="icon-file-text-alt"></i>
				<p>Reports</p>
				<span class="notification blue">{{ $user->reports->count() }}</span>
			</a>
		</div>
	
		<div class="col-sm-2">
			<a class="quick-button disabled" href="javascript:void(0)">
				<i class="icon-comment-alt"></i>
				<p>Comments</p>
				<span class="notification green">{{ $user->comments->count() }}</span>
			</a>
		</div>		
	</div>
	
	<!--<div class="row">
		<div class="col-sm-4">
			<h2>Impact</h2>
			<div class="col-sm-3">
				<span><b>{{ $user->allFollowers() }}</b></span></br><span>followers</span>
			</div>
			<div class="col-sm-3">
				<span><b><i class="icon-thumbs-up green"></i>{{ $user->voters()[0] }}</span> <span><i class="icon-thumbs-down red"></i>{{ $user->voters()[1] }}</b></span></br><span>votes recibed</span>
			</div>
			<div class="col-sm-3">
				<span><b>{{ $user->allVisits() }}</b></span></br><span>visits recibed</span>
			</div>
		</div>
	</div>-->

	<!--<div class="row margin-top-20">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header level-2">
					<h2><i class="icon-trophy"></i><span class="break"></span>Badges</h2>
					<div class="box-icon">
						<a href="javascript:void(0)" class="btn-minimize"><i class="icon-chevron-up"></i></a>
					</div>
				</div>
				<div class="box-content">
					<div class="row">
						<div class="col-xs-6">
							@foreach ($user->badges as $badge)
								@if ($badge->badge == 1) 
								<img src="{{URL::asset('img/badges/reports20.png'); }}" title="20 reports created!">
								@elseif ($badge->badge == 2)
								<img src="{{URL::asset('img/badges/reports50.png'); }}" title="50 reports created!">
								@elseif ($badge->badge == 3)
								<img src="{{URL::asset('img/badges/reports100.png'); }}" title="100 reports created!">
		   						@elseif ($badge->badge == 4)
								<img src="{{URL::asset('img/badges/comments1.png'); }}" title="New Commentator">
								@elseif ($badge->badge == 5)
								<img src="{{URL::asset('img/badges/comments2.png'); }}" title="Advanced Commentator">
								@elseif ($badge->badge == 6)
								<img src="{{URL::asset('img/badges/comments3.png'); }}" title="Master Commentator">
		   						@elseif ($badge->badge == 7)
								<img src="{{URL::asset('img/badges/followers5.png'); }}" title="5 followers reached!">
								@elseif ($badge->badge == 8)
								<img src="{{URL::asset('img/badges/followers50.png'); }}" title="50 followers reached!">
		   						@elseif ($badge->badge == 9)
								<img src="{{URL::asset('img/badges/followers100.png'); }}" title="100 followers reached!">
		   						@elseif ($badge->badge == 10)
		   						<img src="{{URL::asset('img/badges/years1.png'); }}" title="1 year veteran">
		   						@elseif ($badge->badge == 11)
		   						<img src="{{URL::asset('img/badges/years2.png'); }}" title="2 years veteran">
		   						@elseif ($badge->badge == 12)
		   						<img src="{{URL::asset('img/badges/years5.png'); }}" title="+5 years veteran">
		   						@endif
		   					@endforeach
						</div>
						<div class="col-xs-6" style="border-left: 1px solid #000">
							<h2>Last badges</h2>
							<div class="graph">
								<div class="timeline">
									@foreach ($user->badgesOrdered as $index => $badge)
										@if ($index < 3)
										<div class="timeslot alt">
											<div class="task">
									    		<span>
													<span class="details">
														@if ($badge->badge == 1)
															20 reports created!
														@elseif ($badge->badge == 2)
															50 reports created!
														@elseif ($badge->badge == 3)
															100 reports created!
														@elseif ($badge->badge == 4)
															New Commentator
														@elseif ($badge->badge == 5)
															Advanced Commentator
														@elseif ($badge->badge == 6)
															Master Commentator
														@elseif ($badge->badge == 7)
															5 followers reached!
														@elseif ($badge->badge == 8)
															50 followers reached!
														@elseif ($badge->badge == 9)
															100 followers reached!
														@elseif ($badge->badge == 10)
															1 year veteran
														@elseif ($badge->badge == 11)
															2 year veteran
														@elseif ($badge->badge == 12)
															+5 year veteran
														@endif
													</span>
												</span>
												<div class="arrow"></div>
											</div>
											<div class="icon">
												<i class="icon-calendar"></i>
											</div>
											<div class="time">
												{{ $badge->created_at }}
											</div>	
									    </div>
								    	@endif
								    @endforeach
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>-->

</div>
<!--=== End Content Part ===-->
	
@stop


@section('specific-javascript-plugins')
	{{ basset_javascripts('page-login') }}
@stop


@section('custom-javascript')
@stop