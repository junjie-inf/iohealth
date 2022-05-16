@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

	<!--=== Content Part ===-->
	<div class="container">
	
		<div class="page-header inh_nomargin-top">
			<h1>{{ trans('sections.' . Route::currentRouteName()) }} <small>(template)</small></h1>
		</div
		
		<div class="row privacy">
			<p>Los siguientes términos de uso de la plataforma <a href="{{ url('/') }}">{{ url('/') }}</a> (en adelante, plataforma {{ Config::get('local.site.name') }}), con CIF B00000000 y domicilio social en calle [calle], [numero], [piso], [cp], Madrid, tendrán el valor de un contrato y constituirán un acuerdo vinculante entre {{ Config::get('local.site.name') }} y el usuario.</p><br />

			<h4 class="inh_bold">Aceptación de las condiciones generales</h4>
			<p>El hecho de acceder a esta página implica el conocimiento y la aceptación de los siguientes términos y condiciones de uso. En consecuencia, si es usted visitante, estudiante, profesor, o representante de una empresa, una organización profesional o una institución educativa, antes de acceder, navegar o registrarse en la plataforma {{ Config::get('local.site.name') }}, le solicitamos que lea las condiciones generales que encontrará a continuación.</p>
			<p>Le comunicamos que no podrá aceptar estos términos, ni tampoco aceptar los servicios que le ofrece {{ Config::get('local.site.name') }}:</p>
			<ol>
				<li>Si no está legalmente capacitado para celebrar un contrato vinculante con {{ Config::get('local.site.name') }}.</li>
				<li>Si tiene prohibida o legalmente excluida la recepción o utilización de este tipo de servicios, en virtud de la legislación de su país de residencia o desde el que accede o utiliza la plataforma {{ Config::get('local.site.name') }}.</li>
			</ol><br />

			<h4 class="inh_bold">Cuenta de usuario. Registro y uso debido</h4>
			<p>Para acceder como usuario registrado a la plataforma {{ Config::get('local.site.name') }}, deberá crear una cuenta personal. Para ello, tendrá que facilitar determinada información correcta y completa. Es su responsabilidad:</p>
			<ul>
				<li>mantener esta información actualizada,</li>
				<li>tratar de forma segura y confidencial su contraseña para evitar accesos no autorizados e indebidos.</li>
			</ul>
			<p>{{ Config::get('local.site.name') }} se reserva el derecho de cancelar su inscripción o derechos de acceso a la plataforma en el caso de haber comunicado datos falsos o incorrectos.</p>
			<p>Por otro lado, deberá notificar a {{ Config::get('local.site.name') }} inmediatamente cualquier incumplimiento de las normas de seguridad o cualquier uso no autorizado de su cuenta del que tenga conocimiento.</p>
			<p>Al registrarse en la plataforma {{ Config::get('local.site.name') }}, usted acepta ser el único responsable (frente a {{ Config::get('local.site.name') }} y a terceros) de toda la actividad que tenga lugar en su cuenta de usuario.</p>
			<p>{{ Config::get('local.site.name') }} podría solicitarle información adicional, de carácter personal, con el objetivo de compartirla con terceros y proporcionarle así servicios adicionales que puedan reportarle  beneficios, como la participación en procesos de selección y reclutamiento profesional iniciados por las empresas y organizaciones vinculadas a {{ Config::get('local.site.name') }}.</p><br />

			<h4 class="inh_bold">Política de privacidad de datos y confidencialidad</h4>
			<p>En cumplimiento de la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y de Comercio Electrónico (LSSICE), la plataforma {{ Config::get('local.site.name') }} no enviará por correo electrónico comunicaciones publicitarias no autorizadas por los usuarios.</p>
			<p>De conformidad con la Ley Orgánica de Protección de Datos (LOPD) 15/1999, de 13 de Diciembre, sus datos formarán parte de un fichero cuyo responsable y propietario es {{ Config::get('local.site.name') }}, que los tratará de forma confidencial con la única finalidad de formalizar su matrícula y participación en los programas de formación, así como gestionar servicios de valor añadido (exámenes, diplomas y certificaciones) que puedan ser solicitados por usted.</p>
			<p>El usuario autoriza expresamente a {{ Config::get('local.site.name') }} para que estos datos puedan ser facilitados a terceras empresas, escuelas de negocios y universidades, si la finalidad es un posible reclutamiento profesional o la consideración como potenciales clientes de sus propias ofertas formativas. En todos los casos, dichos datos, se limitarán a los estrictamente necesarios para la actividad concreta que se vaya a realizar.</p>
			<p>Siempre que {{ Config::get('local.site.name') }} le solicite datos de carácter personal, incluirá el correspondiente clausulado legal y un vínculo al presente documento con el propósito de hacerle partícipe de sus derechos y obligaciones establecidos en la LOPD y LSSICE.</p>
			<p>Los usuarios que hayan facilitado sus datos a {{ Config::get('local.site.name') }} podrán dirigirse a ésta, en su calidad de Responsable del fichero, con el fin de poder ejercitar gratuitamente sus derechos de acceso, rectificación, cancelación y oposición respecto de los datos incorporados en sus ficheros. Dado el carácter confidencial de la información, el usuario no podrá ejercitar sus derechos telefónicamente, debido a que este medio no permite acreditar su identidad como titular de los datos registrados. El interesado podrá ejercitar sus derechos mediante comunicación por escrito dirigida al correo electrónico {{ Config::get('local.site.email') }} o por correo ordinario a la siguiente dirección postal: [EMPRESA]. [calle], [numero], [piso], [cp], Madrid.</p>
			<p>{{ Config::get('local.site.name') }} se exime del posible incumplimiento, por causas ajenas a su voluntad, de la norma de no admitir el registro de menores de edad en su plataforma.</p>
			<p>Los titulares se reservan el derecho a modificar su Política de Privacidad. Cualquier modificación de esta política será publicada al menos 10 días antes de su efectiva aplicación. El uso de la plataforma después de dichos cambios implicará la expresa aceptación de los mismos.</p><br />

			<h4 class="inh_bold">Exclusión de responsabilidad</h4>
			<p>Las aportaciones realizadas por cualquier usuario registrado, en los diversos espacios colaborativos de {{ Config::get('local.site.name') }} no están sujetas a moderación, aprobación o revisión y pueden  incluir opiniones personales expresadas libremente, así como enlaces a otros sitios web, ajenos a {{ Config::get('local.site.name') }}.</p>
			<p>No obstante, {{ Config::get('local.site.name') }} se reserva el derecho (no la obligación) de eliminar cualquier aportación y comentario siempre que incumpla su Código Ético y sin que ello pueda dar lugar a reclamación alguna.</p><br />

			<h4 class="inh_bold">Código Ético</h4>
			<p>Todos los usuarios matriculados en los programas de {{ Config::get('local.site.name') }} deben conocer y respetar el Código Ético.</p>
			<p>El incumplimiento de esas normas de conducta supondrá la cancelación inmediata de la cuenta de usuario y la expulsión de la plataforma {{ Config::get('local.site.name') }}. Una vez demostrado ese incumplimiento, haber abonado alguno de los servicios de valor añadido no evitará la cancelación y la expulsión.</p><br />

			<h4 class="inh_bold">Cambios en nuestra política de privacidad</h4>
			<p>Estas condiciones de uso y política de privacidad están sujetas a revisión permanente y, en consecuencia, a posibles cambios derivados, sobre todo, de modificaciones en la legislación aplicable o en las funcionalidades de la plataforma. </p>
			<p>Los usuarios deberán tener en cuenta que cualquier cambio o modificación entrará en vigor inmediatamente después de su publicación en la web de {{ Config::get('local.site.name') }}, lo que será adecuadamente comunicado.</p>
			<p>El uso de {{ Config::get('local.site.name') }} tras la publicación de las diversas actualizaciones implica la aceptación de las modificaciones y las nuevas condiciones de uso.</p>
			<p>Recomendamos que revise este espacio informativo con frecuencia para estar al día de los cambios que se hayan incorporado.</p><br />

			<h4 class="inh_bold">Extinción de la relación entre {{ Config::get('local.site.name') }} y los usuarios</h4>
			<p>Estos términos del servicio serán de aplicación hasta su resolución, conforme a las siguientes causas:</p>
			<ul>
				<li>Mediante la notificación por escrito dirigida a la dirección de correo electrónico {{ Config::get('local.site.email') }} en cualquier momento indicando su intención de dejar de ser usuario de la plataforma {{ Config::get('local.site.name') }}. </li>
				<li>Cuando se evidencie incumplimiento de estos términos del servicio o del Código Ético de {{ Config::get('local.site.name') }}.</li>
				<li>Por la incorporación reiterada de contenidos protegidos por derechos de propiedad intelectual de terceros sin contar con la preceptiva licencia para su incorporación en la plataforma {{ Config::get('local.site.name') }}.</li>
				<li>Cuando por mandato judicial o administrativo sea necesario eliminar su participación en la plataforma {{ Config::get('local.site.name') }}.</li>
				<li>Por cualquier otra causa contemplada en la legislación vigente.</li>
			</ul><br />

			<h4 class="inh_bold">Legislación aplicable y jurisdicción </h4>
			<p>Las presentes condiciones generales, términos de uso y política de privacidad, así como las relaciones establecidas entre {{ Config::get('local.site.name') }} y el usuario, se regirán en todo momento por la legislación española vigente.</p>
			<p>Cualquier controversia que pueda surgir entre {{ Config::get('local.site.name') }} y el usuario se someterá a los Juzgados y Tribunales de Madrid, con renuncia expresa a cualquier otro fuero que pudiera corresponderles.  </p>
			<p>Estos términos del servicio fueron actualizados, por última vez, el 1 de septiembre de 2013.</p>	
		</div>
	</div><!--/container-->
	<!--=== End Content Part ===-->

@stop