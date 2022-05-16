<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>{{ Config::get('_custom.site_name') }}</title>

<style>*{margin:0;padding:0;}</style>
</head>

{{-- http://premailer.dialect.ca/ --}}

<body bgcolor="#FFFFFF" style="width:100%!important;height:100%;font-family:Helvetica,Arial,sans-serif;margin:0;padding:0;">

	{{-- HEADER --}}
	<table style="width:100%" bgcolor="#94999C">
		<tr>
			<td></td>
			<td style="display:block!important;max-width:600px!important;margin:0 auto!important;clear:both!important;">

				<div style="padding:10px;max-width:600px;margin:0 auto;display:block;">
					<table bgcolor="#94999C" style="width:100%">
						<tr>
							<td style="color:#FFFFFF;font-size:20px">
								{{ Config::get('local.site.name') }}
							</td>
						</tr>
					</table>
				</div>

			</td>
			<td></td>
		</tr>
	</table>

	{{-- BODY --}}
	<table style="width:100%">
		<tr>
			<td></td>
			<td style="display:block!important;max-width:600px!important;margin:0 auto!important;clear:both!important;" bgcolor="#FFFFFF">
			
				@yield('content')

			</td>
			<td></td>
		</tr>
	</table>
	

	{{-- FOOTER --}}
	<table style="width:100%;clear:both!important">
		<tr>
			<td></td>
			<td style="display:block!important;max-width:600px!important;margin:0 auto!important;clear:both!important;">

				<div style="padding:15px;max-width:600px;margin:0 auto;display:block;">
					<table style="width:100%">
						<tr>
							<td align="center" style="border-top:1px dashed #D7D7D7;padding-top:15px;">
								<a href="{{ Config::get('app.url') }}">{{ Config::get('app.url') }}</a>
							</td>
						</tr>
					</table>
				</div>

			</td>
			<td></td>
		</tr>
	</table>

</body>
</html>