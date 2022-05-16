@if( Auth::check() )

<header id="cr-navbar" class="navbar cr-navbar">
	<div class="container">
		<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".sidebar-nav.nav-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a id="main-menu-toggle" class="hidden-xs open"><i class="icon-reorder"></i> <span class="inh_nobold">{{trans('sections.topbar.menu')}}</span></a>
		<a class="navbar-brand col-lg-2 col-sm-1 col-xs-12" href="{{ URL::to('/') }}" style="background: #FFFFFF !important">
			<span>
				<img src="{{ URL::asset('img/logo.png') }}" alt="{{ Config::get('local.site.name') }}" class="visible-md visible-lg" />
				<img src="{{ URL::asset('img/logo-small.png') }}" alt="{{ Config::get('local.site.name') }}" class="visible-xs visible-sm" />
			</span>
		</a>
		
		{{-- start: Header Menu --}}
		<div class="nav-no-collapse header-nav">
			<ul class="nav navbar-nav reportrange" style="position:relative">
				

					<!--<li>
						<div id="reportrange" class="pull-right" style="width:100%">
							<p id="dt"></p>
						</div>
					</li>-->

				
			</ul>
			<ul class="nav navbar-nav pull-right">
				
				{{-- start: Search icon --}}
				@if( Auth::check() )
					<li class="dropdown">
			
						<div class="search-open">
							<div class="input-append">
								<form action="javascript:void(0)" class="form-search" id="form-search" name="form-search" style="display:inline-block">
									<div id="search-extendible">
										<input type="text" name="q" class="search" autocomplete="off" placeholder="Find reports in map..." />

										<ul class="dropdown-menu searchlist" role="menu" aria-labelledby="search"></ul>
									</div>
								</form>
							</div>
						</div>
						
						<a href="javascript:void(0)" class="btn search-box-opener" title="Search"><i class="icon-search search-btn"></i></a>
					</li>
				@endif
				{{-- end: Search icon --}}
				
				{{-- start: User Dropdown --}}
				<li class="dropdown">
					<a class="btn account dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)">
						<div class="avatar"></div>
						<div class="user">
							<span class="hello">{{trans('sections.topbar.welcome')}}</span>
							<span class="name">{{ Auth::user()->firstname }}</span>
						</div>
					</a>
					<ul class="dropdown-menu">
						<li><a href="{{ URL::route('profile.index') }}"><i class="icon-cog"></i> {{ trans('sections.profile.index') }}</a></li>
						<li><a href="{{ URL::route('profile.show', Auth::user()->id) }}"><i class="icon-user"></i> {{ trans('sections.profile.show') }}</a></li>
						<li><a href="{{ URL::route('auth.logout') }}"><i class="icon-off"></i> {{trans('sections.topbar.logout')}}</a></li>
					</ul>
				</li>
				{{-- end: User Dropdown --}}
				
			</ul>
			
		</div>
		{{-- end: Header Menu --}}

	</div>	
</header>

@else
@if( Route::currentRouteName() !== 'landing' )
<header id="cr-navbar" class="navbar cr-navbar" style="background: white !important">
	<div class="container">
		
		
		<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".sidebar-nav.nav-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a id="main-menu-toggle" class="hidden-xs open"><i class="icon-reorder"></i> <span class="inh_nobold">{{trans('sections.topbar.menu')}}</span></a>
		<a class="navbar-brand col-lg-2 col-sm-1 col-xs-12 center-block" href="{{ URL::to('/') }}" style="margin-top: 35px; height: 80px !important; background: #FFFFFF !important">
			<span>
				<img src="{{ URL::asset('img/logo.png') }}" alt="{{ Config::get('local.site.name') }}" class="visible-md visible-lg" />
				<img src="{{ URL::asset('img/logo-small.png') }}" alt="{{ Config::get('local.site.name') }}" class="visible-xs visible-sm" />
			</span>
		</a>		
	</div>	
</header>
@endif
@endif

<style type="text/css" style="display: none">
	.reportrange {
		position:relative;
		left:10%;
	}

	.reportrange span {
			font-size: 10px !important;	
	}

	@media (min-width: 992px) {
	  .reportrange {
	  		position:relative;
			left:50%;
		}
		.reportrange span {
			font-size: 14px !important;	
		}	
	}

</style>
<!--
<script>
	$(document).ready(function() {
		var date = new Date();
		console.warn(date.getMonth());
		console.warn(date.getDate());
		console.warn(date.getFullYear());
		var month;
		switch(date.getMonth()) {
		    case 0: month="Enero" break;
		    case 1: month="Febrero" break;
		    case 2: month="Marzo" break;
		    case 3: month="Abril" break;
		    case 4: month="Mayo" break;
		    case 5: month="Junio" break;
		    case 6: month="Julio" break;
		    case 7: month="Agosto" break;
		    case 8: month="Septiembre" break;
		    case 9: month="Octubre" break;
		    case 10: month="Noviembre" break;
			case 11: month="Diciembre" break;
			default month="Enero";
		}	
		date= ""+month+"  "+date.getDate()+", "+date.getFullYear()+"";
		document.getElementById("dt").innerHTML = date;
	});
</script>
-->