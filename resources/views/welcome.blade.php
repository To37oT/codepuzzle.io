@include('inc-top')
<html lang="{{ app()->getLocale() }}">
<head>
	@php
		$description = __('Puzzles de Parsons & Défis pour apprendre Python');
		$description_og = __('Puzzles de Parsons & Défis pour apprendre Python');
	@endphp
	@include('inc-meta')
    <title>{{ strtoupper(config('app.name')) }} | {{__('Puzzles de Parsons & Défis')}}</title>
</head>
<body>
	@php
		$lang_switch = '<a href="lang/fr" class="kbd mr-1">fr</a><a href="lang/en" class="kbd">en</a>';
	@endphp
	@include('inc-nav')

	<div class="container mt-3">
		<div class="row pt-3">

			<div class="intro col-md-4 text-monospace">
				<div class="add">
					<h2>{!!__('Créer et partager des puzzles de Parsons', ['link' => route('about')])!!}</h2>
					<div class="mx-auto mt-3 text-center" style="width:180px">
						<a class="btn btn-success btn-sm btn-block p-2" href="{{ route('puzzle-creer-get')}}" role="button"><i class="fa-solid fa-circle-plus pt-1 pb-1"></i><br />{!!__('puzzle')!!}</a>
					</div>
				</div>
			</div>
			<div class="intro col-md-4 text-monospace">
				<div class="add">
					<h2>{!!__('Créer et partager des défis')!!}</h2>				
					<div class="mx-auto mt-3 text-center" style="width:180px">
						<a class="btn btn-success btn-sm btn-block p-2" href="{{ route('defi-creer-get')}}" role="button"><i class="fa-solid fa-circle-plus pt-1 pb-1"></i><br />{!!__('défi')!!}</a>
					</div>
				</div>
			</div>
			<div class="intro col-md-4 text-monospace">
				<div class="add">
					<h2>{!!__('Créer et partager des entraînements / devoirs')!!} <sup class="text-lowercase text-danger">bêta</sup></h2>				
					<div class="mx-auto mt-3 text-center" style="width:180px">
						<a class="btn btn-success btn-sm btn-block p-2" href="{{ route('devoir-creer-get')}}" role="button"><i class="fa-solid fa-circle-plus pt-1 pb-1"></i><br />{!!__('entraînement / devoir')!!}</a>
					</div>
				</div>
			</div>			
		</div>


		<div class="mx-auto text-center" style="width:200px">
			<a class="btn btn-secondary btn-sm btn-block mt-5 mb-1" style="font-size:80%;opacity:0.6" href="{{route('register')}}" role="button">{{__('créer un compte')}}</a>
			<span style="font-size:70%;color:silver;">{{__('pour créer, sauvegarder, modifier et partager les activités proposées aux élèves')}}</span>
		</div>


		{{--
		<div class="row mt-5 text-monospace">
			<div class="col-md-2">
				<div class="font-weight-bold">Exemple 1</div>
				<div class="font-weight-bold text-success">PUZZLE DE PARSONS</div>
				<div class="small text-muted">en mode "glisser-déposer"</div>
			</div>
			<div class="col-md-10">
				<div id="exemple1_div">
					<iframe id="exemple1_iframe" src="/IP4M9D" height="100%" width="100%" frameborder="0"></iframe>
				</div>
			</div>
		</div>

		<div class="row mt-5 text-monospace">
			<div class="col-md-2">
				<div class="font-weight-bold">Exemple 2</div>
				<div class="font-weight-bold text-success">PUZZLE DE PARSONS</div>
				<div class="small text-muted">en mode "glisser-déposer" avec code à compléter</div>
			</div>
			<div class="col-md-10">
				<div id="exemple2_div">
					<iframe id="exemple2_iframe" src="/IP4M9D" height="100%" width="100%" frameborder="0"></iframe>
				</div>
			</div>
		</div>
		--}}


	</div><!--container -->

	@include('inc-footer')
	@include('inc-bottom-js')

	<script>
		for (var i = 1; i <= 2; i++) {
			(function(index) {
				document.getElementById('exemple' + index + '_iframe').addEventListener('load', function() {
					var iframeHeight = document.getElementById('exemple' + index + '_iframe').contentWindow.document.body.scrollHeight;
					document.getElementById('exemple' + index + '_div').style.height = iframeHeight + 'px';
				});
			})(i);
		}
	</script>


</body>
</html>
