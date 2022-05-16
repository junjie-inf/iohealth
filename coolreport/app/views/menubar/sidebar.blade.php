<div id="sidebar-left" class="col-lg-2 col-sm-1">

	@if( Auth::check() )
	
	<div class="nav-collapse sidebar-nav collapse navbar-collapse bs-navbar-collapse">
		{{-- @TODO: poner permisos --}}
		<ul class="nav nav-tabs nav-stacked main-menu">

			@if( Auth::user()->hasPermission('VIEW_REPORTS') && Auth::user()->getPermissionValue('VIEW_REPORTS') != 'OWN' )
			<!--<li class="<?= strstr(Route::currentRouteName(), 'report.' ) !== false ? 'active' : '' ?>">
				<a href="{{ URL::to('report') }}"><i class="icon-file-text-alt icon-large"></i><span class="hidden-sm"> {{ trans('sections.report.index') }}<span class="label">{{ Report::count() }}</span></span></a>
			</li>-->

			<li>
				<a href="{{ URL::to('report?template=10') }}"><i class="icon-user icon-large"></i><span class="hidden-sm"> {{ trans('sections.report.clients') }}</span></a>
			</li>
			<li><hr /></li>
				
				@if (Auth::user()->admin || Auth::user()->role_id == 4)
				<!--Administrador o médico-->
				<li>
					<a href="{{ URL::to('report?template=2') }}"><i class="icon-screenshot icon-large"></i><span class="hidden-sm"> {{ trans('sections.report.resources') }}</span></a>
				</li>

				<li>
					<a href="{{ URL::to('report?template=6') }}"><i class="icon-warning-sign icon-large"></i><span class="hidden-sm"> {{ trans('sections.report.stability') }}</span></a>
				</li>
				@endif
			@else
			<li>
				<a href="{{ URL::to('report/'. DB::select(DB::raw("SELECT id FROM reports WHERE deleted_at IS NULL AND datum->'5889efb0944a90'->>'value' = '".Auth::user()->id."'"))[0]->id ) }}"><i class="icon-user icon-large"></i><span class="hidden-sm"> {{ trans('sections.report.personal') }}</span></a>
			</li>
			<li><hr /></li>
			@endif

			
			
			
			<li>
				<a href="{{ URL::to('report?template=22') }}"><i class="icon-pencil icon-large"></i><span class="hidden-sm"> {{ trans('sections.report.behaviour') }}</span></a>
			</li>			
			
			@if ( Auth::user()->role_id == '4' )
			<!-- Si es un médico -->
			<li>
				<a href="{{ URL::to('report?template=51') }}"><i class="icon-inbox icon-large"></i><span class="hidden-sm"> {{ trans('sections.report.recodoctor') }}</span></a>
			</li>			
			@endif
			@if (Auth::user()->admin || Auth::user()->role_id == 4)
			<!--Si es administrador o médico-->
			<li>
				<a href="{{ URL::to('report?template=11') }}"><i class="icon-pencil icon-large"></i><span class="hidden-sm"> {{ trans('sections.report.sicktreat') }}</span></a>
			</li>
			@endif

			@foreach (Navigation::getShowMenu() as $templates )
					@foreach ($templates as $t )
						<li>

							@if($t->id == 37)
									<a href="{{ URL::to('report?template='.$t->id) }}"><i class="icon-adjust icon-large"></i><span class="hidden-sm"> {{ucfirst(trim(str_replace("Registro", "", $t->title)))}}</span></a>
							@else
								@if($t->id == 38)
									<a href="{{ URL::to('report?template='.$t->id) }}"><i class="icon-heart icon-large"></i><span class="hidden-sm"> {{ucfirst(trim(str_replace("Registro", "", $t->title)))}}</span></a>
								@else
									@if($t->id == 27)
										<a href="{{ URL::to('report?template='.$t->id) }}"><i class="icon-inbox icon-large"></i><span class="hidden-sm"> {{ucfirst(trim(str_replace("Registro", "", $t->title)))}}</span></a>
									@else
										@if($t->id == 25)
											<a href="{{ URL::to('report?template='.$t->id) }}"><i class="icon-tint icon-large"></i><span class="hidden-sm"> {{ucfirst(trim(str_replace("Registro", "", $t->title)))}}</span></a>
										@else
											@if($t->id == 28)
												<a href="{{ URL::to('report?template='.$t->id) }}"><i class="icon-heart-empty icon-large"></i><span class="hidden-sm"> {{ucfirst(trim(str_replace("Registro", "", $t->title)))}}</span></a>
											@else
												@if($t->id == 49)
													<a href="{{ URL::to('report?template='.$t->id) }}"><i class="icon-sun icon-large"></i><span class="hidden-sm"> {{ucfirst(trim(str_replace("Registro", "", $t->title)))}}</span></a>
												@else
													<a href="{{ URL::to('report?template='.$t->id) }}"><i class="icon-pencil icon-large"></i><span class="hidden-sm"> {{ucfirst(trim(str_replace("Registro", "", $t->title)))}}</span></a>
												@endif
											@endif
										@endif
									@endif
								@endif
							@endif
						</li>
					@endforeach
		    @endforeach
			
			<!--

			<li>
				<a href="{{ URL::to('report?template=37') }}"><i class="icon-adjust icon-large"></i><span class="hidden-sm"> {{ trans('sections.report.spo2') }}</span></a>
			</li>
			<li>
				<a href="{{ URL::to('report?template=28') }}"><i class="icon-heart icon-large"></i><span class="hidden-sm"> {{ trans('sections.report.bp') }}</span></a>
			</li>
			<li>
				<a href="{{ URL::to('report?template=27') }}"><i class="icon-inbox icon-large"></i><span class="hidden-sm"> {{ trans('sections.report.weight') }}</span></a>
			</li>
			<li>
				<a href="{{ URL::to('report?template=25') }}"><i class="icon-tint icon-large"></i><span class="hidden-sm"> {{ trans('sections.report.glucose') }}</span></a>
			</li>-->

			<!-- <li>
				<a href="{{ URL::to('profile/report') }}">
					<span class="hidden-sm">
  						<i class="icon-file-text-alt icon-large" ></i>
  						<i class="icon-star" style="position: absolute; top: 0.5em; left: 1.7em;"></i>  	
					</span>
					<span class="visible-sm">
  						<i class="icon-file-text-alt icon-large" style="margin-left: 0.4em;"></i>
  						<i class="icon-star" style="position: relative; top: -0.5em; left: -1.0em;"></i> 	
					</span>
					<span class="hidden-sm"> {{ trans('sections.profile.report') }}<span class="label">{{ Report::where('user_id', '=', Auth::user()->id)->count() }}</span></span>
				</a>
			</li> -->
			

			<!--  <li class="<?= strstr(Route::currentRouteName(), 'rank' ) !== false ? 'active' : '' ?>">
					<a href="{{ URL::to('rank') }}"><i class="icon-trophy icon-large"></i><span class="hidden-sm"> {{ trans('sections.rank.index') }}</span></a>
			</li> -->

			<li><hr /></li>
			<li class="<?= strstr(Route::currentRouteName(), 'health' ) !== false ? 'active' : '' ?>">
					<a href="{{ URL::to('health') }}"><i class="icon-heart icon-large"></i><span class="hidden-sm"> {{ trans('sections.mortgage.index') }}</span></a>
			</li>
			

			@if( Auth::user()->admin || Auth::user()->hasPermission('VIEW_USERS') || Auth::user()->hasPermission('VIEW_GROUPS') || Auth::user()->hasPermission('VIEW_ROLES') )
				<li><hr /></li>
			@endif
			
			@if( Auth::user()->admin )
				<li class="hidden-sm hidden-xs <?= strstr(Route::currentRouteName(), 'dashboard' ) !== false ? 'active' : '' ?>">
					<a href="{{ URL::to('dashboard') }}"><i class="icon-dashboard icon-large"></i><span class="hidden-sm"> {{ trans('sections.dashboard.index') }}</span></a>
				</li>
				<li></li> {{-- Chapuza para no descuadrar el menu al ocultar dashboard y dashitem --}}
				
				<li class="hidden-sm hidden-xs <?= strstr(Route::currentRouteName(), 'dashitem' ) !== false ? 'active' : '' ?>">
					<a href="{{ URL::to('dashitem') }}"><span style="margin-left: 10px;" class="hidden-sm">
  							<i class="icon-dashboard icon-large"></i>
  							<i class="icon-edit" style="position: absolute; top: 0.5em; left: 2.6em;"></i>  	
						</span>
						<span class="visible-sm">
  							<i class="icon-dashboard icon-large" style="margin-left: 0.4em;"></i>
  							<i class="icon-edit" style="position: relative; top: -0.5em; left: -0.6em;"></i>  	
						</span>
						<span class="hidden-sm"> {{ trans('sections.dashitem.index') }}</span></a>
				</li>

				<li class="<?= strstr(Route::currentRouteName(), 'alert' ) !== false ? 'active' : '' ?>">
					<a href="{{ URL::to('alert') }}"><i class="icon-bell icon-large"></i><span class="hidden-sm"> {{ trans('sections.alert.index') }}</span></a>
				</li>
		<!-- 		<li class="<?= (strstr(Route::currentRouteName(), 'map.' ) !== false && strstr(Route::currentRouteName(), 'map.', true) !== false) ? 'active' : '' ?>">
					<a href="{{ URL::to('map') }}"><i class="icon-globe icon-large"></i><span class="hidden-sm"> {{ trans('sections.map.index') }}</span></a>
				</li> -->
			@endif
			
			@if( Auth::user()->admin )
				<li class="<?= strstr(Route::currentRouteName(), 'user' ) !== false ? 'active' : '' ?>">
					<a href="{{ URL::to('user') }}"><i class="icon-user icon-large"></i><span class="hidden-sm"> {{ trans('sections.user.index') }}<span class="label">{{ User::all()->count() }}</span></span></a>
				</li>
			@endif
			
			@if( Auth::user()->admin )
			<li class="<?= strstr(Route::currentRouteName(), 'group' ) !== false ? 'active' : '' ?>">
				<a href="{{ URL::to('group') }}"><i class="icon-group icon-large"></i><span class="hidden-sm"> {{ trans('sections.group.index') }}<span class="label">{{ Group::all()->count() }}</span></span></a>
			</li>
			@endif
			
			@if( Auth::user()->hasPermission('VIEW_ROLES') )
			<li class="<?= strstr(Route::currentRouteName(), 'role' ) !== false ? 'active' : '' ?>">
				<a href="{{ URL::to('role') }}"><i class="icon-key icon-large"></i><span class="hidden-sm"> {{ trans('sections.role.index') }}<span class="label">{{ Role::all()->count() }}</span></span></a>
			</li>
			@endif
			
			@if( Auth::user()->admin )
			<li class="<?= strstr(Route::currentRouteName(), 'template' ) !== false ? 'active' : '' ?>">
				<a class="submenu" href="{{ URL::to('template') }}"><i class="icon-list-ul icon-large"></i><span class="hidden-sm"> {{ trans('sections.template.index') }}<span class="label">{{ Template::count() }}</span></span></a>
			</li>
			
			@endif
			<!--
			<li>
				<a href="{{ URL::to('setting') }}"><i class="icon-wrench"></i><span class="hidden-sm"> {{ trans('sections.setting') }}</span></a>
			</li>
			-->
		</ul>
	</div>
	
	@else

	<div class="nav-collapse sidebar-nav collapse navbar-collapse bs-navbar-collapse">
		<ul class="nav nav-tabs nav-stacked main-menu">
			<li><a href="{{ URL::to('/') }}"><i class="icon-map-marker"></i><span class="hidden-sm"> {{ trans('sections.landing') }}</span></a></li>
		</ul>
	</div>
	
	@endif
	<div class="nav-collapse sidebar-nav collapse navbar-collapse bs-navbar-collapse">
	<ul class="nav nav-tabs nav-stacked main-menu">
	<li><hr /></li>
	</ul>
	</div>

	<!--
	<footer class="clearfix visible-sm inh_small inh_nopadding">
		<p class="text-center text-muted" style="color:#FFF">
			{{ Config::get('local.site.name') }} &copy; {{ date('Y') }}.
		</p>
		<p class="text-center inh_small">
			<a href="{{ URL::to('about') }}" class="grey-hover">{{ trans('sections.static.about') }}</a><br />
			<a href="{{ URL::to('contact') }}" class="grey-hover">{{ trans('sections.static.contact') }}</a><br />
			<a href="{{ URL::to('terms') }}" class="grey-hover">{{ trans('sections.static.terms') }}</a><br />
			<a href="{{ URL::to('product') }}" class="grey-hover">{{ trans('sections.static.product') }}</a>
		</p>
	</footer>
	-->
	<footer class="clearfix hidden-sm hidden-xs inh_nopadding">
		<p class="text-center text-muted" style="color: white">
			{{ Config::get('local.site.name') }} &copy; {{ date('Y') }}.
		</p>
		<p class="text-center inh_small">
			<a href="{{ URL::to('about') }}" class="grey-hover">{{ trans('sections.static.about') }}</a> -
			<a href="{{ URL::to('contact') }}" class="grey-hover">{{ trans('sections.static.contact') }}</a> -
			<a href="{{ URL::to('terms') }}" class="grey-hover">{{ trans('sections.static.terms') }}</a> -
			<a href="{{ URL::to('product') }}" class="grey-hover">{{ trans('sections.static.product') }}</a>
		</p>
	</footer>
	
</div>
