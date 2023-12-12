<?php
// recuperation du défi en fonction du jeton
$defi = App\Models\Defi::where('jeton', $jeton)->first();
$tests = unserialize($defi->tests);
$asserts = '';
foreach($tests as $test){
	$asserts .=  '["assert '.$test[0].'", "'.addslashes($test[1]).'"],';
}
$asserts = '[' . trim($asserts, ',') . ']';
?>
@include('inc-top')
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
	@php
        $description = __('Générateur et gestionnaire de puzzles de Parsons') . ' | Défi - D' . strtoupper($jeton);
        $description_og = 'Défi - D' . strtoupper($jeton);
    @endphp
	@include('inc-meta-jeton')
	<script src="https://cdn.jsdelivr.net/pyodide/v0.24.1/full/pyodide.js"></script>
    <title>{{ config('app.name') }} | Défi - D{{ $jeton }}</title>
	<style>
		pre {
			padding:10px;
			border: solid 1px #fafcfe;
			background-color:#fafcfe;
			border-radius:4px;
			color:#6c757d;
			font-size:95%;
		}
	</style>
</head>

<body class="no-mathjax" oncontextmenu="return false" onselectstart="return false" ondragstart="return false">

	<div class="container-fluid">

		@if(!$iframe)
		<h1 class="mt-2 mb-5 text-center"><a class="navbar-brand m-1" href="{{ url('/') }}"><img src="{{ asset('img/codepuzzle.png') }}" height="25" alt="CODE PUZZLE" /></a></h1>
		@endif

		@if ($defi->with_chrono == 1 OR $defi->with_nbverif == 1)
		<table align="center" cellpadding="2" style="text-align:center;margin-bottom:10px;color:#bdc3c7;">
			<tr>
				@if ($defi->with_chrono == 1)
				<td><i class="fas fa-clock"></i></td>
				@endif
				@if ($defi->with_nbverif == 1)
				<td><i class="fas fa-check"></i></td>
				@endif
			</tr>
			<tr>
				@if ($defi->with_chrono == 1)
				<td><span id="chrono" class="dashboard">00:00</span></td>
				@endif
				@if ($defi->with_nbverif == 1)
				<td><span id="nb_tentatives" class="dashboard">0</span></td>
				@endif
			</tr>
		</table>
		@endif

        @if ($defi->titre_eleve !== NULL OR $defi->consignes_eleve !== NULL)
        <div class="row" style="padding-top:10px;">
            <div class="col-md-10 offset-md-1">
                <div class="frame text-monospace">
                    @if ($defi->titre_eleve !== NULL)
                        <div class="mb-1">{{ $defi->titre_eleve }}</div>
                    @endif

					<?php
					include('lib/parsedownmath/ParsedownMath.php');
					$Parsedown = new ParsedownMath([
						'math' => [
							'enabled' => true, // Write true to enable the module
							'matchSingleDollar' => true // default false
						]
					]);
					?>

                    @if ($defi->consignes_eleve !== NULL)
                        <div class="consignes mathjax" style="text-align:justify;">
							<?php
							echo $Parsedown->text($defi->consignes_eleve);
							?>
                        </div>
					@endif

					<div id="consignes_hidden" class="mathjax" style="padding:30px 20px 0px 20px;width:1200px;height:630px;background-color:white;display:none;">
						<img src="{{ asset('img/codepuzzle.png') }}" height="30" />
						<div class="consignes" style="text-align:justify;padding:20px 40px 20px 40px;margin-top:25px;border-radius:10px;font-size:28px;background-color:#F8FAFC;">
							<?php
							echo $Parsedown->text($defi->consignes_eleve);
							?>
						</div>
					</div>

                </div>
            </div>
        </div><!-- row -->
        @endif

    </div>

    <div class="container-fluid pb-5">
        <div class="row">
            <div class="col-md-10 offset-md-1 text-center" style="position:relative;height:30px;">
				<!-- bouton reinitialiser -->
				<a id="reinitialiser" href="{{ request()->fullUrl() }}" style="position:absolute;left:25px;top:10px;" class="text-muted" data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-trigger="hover" title="{{__('réinitialiser')}}"><i class="fas fa-sync-alt"></i></a>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-10 offset-md-1 text-center">
                <textarea name="code" style="display:none;" id="code"></textarea>
		        <div style="width:100%;margin:0px auto 0px auto;"><div id="editor_code" style="border-radius:5px;">{{$defi->code}}</div></div>
                <!-- bouton verifier -->
                <button onclick="evaluatePython()" type="button" class="btn btn-primary mt-2 pl-4 pr-4" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-trigger="hover" title="{{__('vérifier')}}" style="display:inline"><i class="fas fa-check"></i></button>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-md-4 offset-md-4 text-monospace small">
				<table style="width:100%">
                @foreach($tests AS $test)
				<tr>
				<td class="text-center" style="vertical-align:top"><div id="test_{{$loop->index}}" class="test"><i class="fas fa-question-circle"></i></div></td>
				<td style="width:100%;">
					<div id="test_message_{{$loop->index}}" class="text-muted pl-2" style="height:100%;">
						<div>Test {{$loop->index + 1}}</div>
					</div>
				</td>
				</tr>
                @endforeach
				</table>
            </div>
        </div>

        <div class="row mt-3 pb-5" @if(!$defi->with_console) style="display:none" @endif  >
            <div class="col-md-10 offset-md-1">
                <div>Console</div>
                <pre id="output" class="text-monospace p-2 small text-muted" style="border-radius:4px;border:1px solid silver"></pre>
                <pre id="output2" class="text-monospace p-3 text-white bg-dark" style="border-radius:4px;border:1px solid silver;min-height:100px;"></pre>
            </div>
        </div>    
		  
    </div><!-- container -->

    @include('inc-bottom-js')

	<script src="{{ asset('js/html2canvas.min.js') }}" type="text/javascript" charset="utf-8"></script>
	<script>
		html2canvas(document.getElementById('consignes_hidden'), {
			onclone: function (clonedDoc) {
				clonedDoc.getElementById('consignes_hidden').style.display = 'block';
			}	
		}).then(function (canvas) {

			var imgData = canvas.toDataURL('image/png');
			// Envoie des données de l'image au serveur (voir l'étape suivante)
			var formData = new URLSearchParams();
			formData.append('imgData', imgData);
			formData.append('jeton', '{{ 'D'.$jeton }}');
			fetch('/save-opengraph-image', {
				method: 'POST',
				mode: "cors",
				headers: {"Content-Type": "application/x-www-form-urlencoded", "X-CSRF-Token": "{{ csrf_token() }}"},
				body: formData
			})
			.then(response => {
				if (response.ok) {
					// Le serveur a répondu avec succès, vous pouvez traiter la réponse ici
					return response.text();
				}
				throw new Error('Erreur lors de la sauvegarde de la capture d\'écran.');
			})
			.then(data => {
				console.log('Capture d\'écran sauvegardée avec succès sur le serveur.');
				console.log('Chemin de l\'image sauvegardée : ' + data);
			})
			.catch(error => {
				// Il y a eu une erreur lors de la requête
				console.error(error);
			});
		});
	</script>
	<script src="{{ asset('js/ace/ace.js') }}" type="text/javascript" charset="utf-8"></script>
	<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>	

	<script>
		MathJax = {
			tex: {
				inlineMath: [['$', '$'], ['\\(', '\\)']],
				displayMath: [ ['$$','$$'], ["\\[","\\]"] ],
				processEscapes: true
			},
			options: {
				ignoreHtmlClass: "no-mathjax",
				processHtmlClass: "mathjax"
			}
		};        
	</script>  
	<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
	<script type="text/javascript" id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js"></script> 

	<script>
		var editor_code = ace.edit("editor_code", {
			theme: "ace/theme/puzzle_code",
			mode: "ace/mode/python",
			maxLines: 500,
			minLines: 4,
			fontSize: 14,
			wrap: true,
			useWorker: false,
			autoScrollEditorIntoView: true,
			highlightActiveLine: false,
			highlightSelectedWord: false,
			highlightGutterLine: true,
			showPrintMargin: false,
			displayIndentGuides: true,
			showLineNumbers: true,
			showGutter: true,
			showFoldWidgets: false,
			useSoftTabs: true,
			navigateWithinSoftTabs: false,
			tabSize: 4
		});
		editor_code.container.style.lineHeight = 1.5;
		var textarea_code = $('#code');
		editor_code.getSession().on('change', function () {
			textarea_code.val(editor_code.getSession().getValue());
		});
		textarea_code.val(editor_code.getSession().getValue());
	</script>   
	
	<script>
		var count;
		var intervalRef = null;

		var chrono = {
			start: function () {
				let start = new Date();
				intervalRef = setInterval(_ => {
					let current = new Date();
					count = +current - +start;
					let s = Math.floor((count /  1000)) % 60;
					let m = Math.floor((count / 60000)) % 60;
					if (s < 10) {
						s_display = '0' + s;
					} else {
						s_display = s;
					}
					if (m < 10) {
						m_display = '0' + m;
					} else {
						m_display = m;
					}
					$('#chrono').text(m_display + ":" + s_display);
				}, 1000);
			},
			stop: function () {
				clearInterval(intervalRef);
				delete intervalRef;
			},
		}
		chrono.start();	
	</script>

	<script>
		function bravo() {
			var defaults = {
				spread: 360,
				ticks: 400,
				gravity: 1,
				decay: 0.94,
				startVelocity: 40,
				shapes: ['star'],
				colors: ['FFE400', 'FFBD00', 'E89400', 'FFCA6C', 'FDFFB8']
			};
			function shoot() {
				confetti({
					...defaults,
					particleCount: 80,
					scalar: 2,
					shapes: ['star']
				});

				confetti({
					...defaults,
					particleCount: 40,
					scalar: 5,
					shapes: ['circle']
				});
			}
			setTimeout(shoot, 0);
			setTimeout(shoot, 200);
			setTimeout(shoot, 400);
			setTimeout(shoot, 600);
			setTimeout(shoot, 800);
			setTimeout(shoot, 1000);
			setTimeout(shoot, 1200);
			setTimeout(shoot, 1400);
			setTimeout(shoot, 1500);
		}
	</script>	

    <script>
		const output = document.getElementById("output");
		const code = document.getElementById("code");
		var nb_tentatives = 1;

		function addToOutput(s) {
			//output.innerText += ">>>" + code.value + "\n" + s + "\n";
			output.innerText = ""
			if (typeof(s) !== 'undefined'){
				output.innerText = s
			}
		}

		var globals_keys = []

		output.innerText = "Initialisation...\n";

		// init Pyodide
		async function main() {
			let pyodide = await loadPyodide();
			output.innerText = "Prêt!\n";

			// Liste des clés de globals présentes lors de la première excécution
			for (const key of pyodide.globals.keys()) {
				globals_keys.push(key);
			}

			return pyodide;
		}
		
		let pyodideReadyPromise = main();

		async function evaluatePython() {
			let pyodide = await pyodideReadyPromise;
			await pyodide.loadPackagesFromImports(code.value);
			var asserts_tab = {!!$asserts!!};			
			var error_message = ""
			@if ($defi->with_nbverif == 1)
			document.getElementById('nb_tentatives').innerText = nb_tentatives++;
			@endif

			// REINITIALISATION DE GLOBALS (on supprime les cles qui
			// n'étaient pas présentes lors de la première exécution)
			const clesASupprimer = [];
			for (const key of pyodide.globals.keys()) {
				if (!globals_keys.includes(key)) {
					clesASupprimer.push(key);
				}
			}
			for (const key of clesASupprimer) {
				pyodide.globals.delete(key);
			}

			try {

				// redirection output vers div
				document.getElementById("output2").innerText = "";
				pyodide.setStdout({batched: (str) => {
					document.getElementById("output2").innerText += str+"\n";
					console.log(str);
				}})

				let output = pyodide.runPython(code.value);

				var n = 0;
				var ok = true;

				for (assert of asserts_tab){
					console.log("ASSERT: "+assert)

					try {
						// pas d'erreur python
						// assert valide

						// redirection output vers console pour ne pas afficher des print dans le div output quand on teste un assert
						pyodide.setStdout({batched: (str) => {
							console.log(str);
						}})

						// REINITIALISATION DE GLOBALS (on supprime les cles qui
						// n'étaient pas présentes lors de la première exécution)
						const clesASupprimer = [];
						for (const key of pyodide.globals.keys()) {
							if (!globals_keys.includes(key)) {
								clesASupprimer.push(key);
							}
						}
						for (const key of clesASupprimer) {
							pyodide.globals.delete(key);
						}

						pyodide.runPython(code.value + "\n" + assert[0] + ', "' + assert[1] + '"');	
						document.getElementById('test_'+n).innerHTML = '<i class="fas fa-check-circle"></i>';
						document.getElementById('test_message_'+n).innerHTML = 'Test validé!';
						document.getElementById('test_'+n).className = "test_success";
						console.log("pas d'erreur python et assert validé")

					} catch (err) {
						// pas d'erreur python
						// assert non valide
						console.log("Pas d'erreur Python mais assert non validé")
						console.log("Errors: " + err)

						var test_message = "Test non validé :-/";
						@if($defi->with_message)
						if (assert[1]) {
							var test_message = assert[1];
						}
						@endif	

						document.getElementById('test_'+n).innerHTML = '<i class="fas fa-times-circle"></i>';
						document.getElementById('test_message_'+n).innerHTML = test_message;
						document.getElementById('test_'+n).className = "test_failed";

						error_message += "Test "+ (n+1) + ": échec\n\n";

						let errors = err.message.split("File \"<exec>\", ");
						errors.forEach((error) => {
							if (typeof(error) !== 'undefined' && !error.includes('Traceback')) {

								// on recupere la ligne de l'erreur
								regex = /line (\d+)/;
    							let error_line = regex.exec(error)[1];

								// on retire la premiere ligne pour ne garder que le message
								let error_string = error.replace(/^.*\n/, '');

								console.log("error_line: " + error_line)
								console.log("error_string: " + error_string)

								if (code.value.split('\n').length) {
									nb_code_lines = code.value.split('\n').length;
								} else {
									nb_code_lines = 0;
								}
								console.log("nb_code_lines: " + nb_code_lines)
								console.log("code: " + code.value)
								var error_info = ""
								if (error_line <= nb_code_lines) error_info += "Erreur ligne " + error_line + "\n";
								if (error_string) error_info += error_string;
								if (error_info.trim()) {
									error_message += error_info.trim() + "\n\n";
								}
							}
						});						
						
						ok = false;
					}
					n++;			
				}
				
				if (ok) {
					error_message = "Code correct et tests validés. Bravo!";
					bravo();
				} 	
			} catch (err) {
				// erreur python
				console.log('ERROR')
				console.log("Errors: " + err)

				let errors = err.message.split("File \"<exec>\", ");
				errors.forEach((error) => {
					if (typeof(error) !== 'undefined' && !error.includes('Traceback')) {

						// on recupere la ligne de l'erreur
						regex = /line (\d+)/;
						let error_line = regex.exec(error)[1];

						// on retire la premiere ligne pour ne garder que le message
						let error_string = error.replace(/^.*\n/, '');

						var error_info = "";
						error_info += "Erreur ligne " + error_line + "\n";
						if (error_string) error_info += error_string;
						error_message += error_info.trim() + "\n\n";
					}
				});
			}	
			addToOutput(error_message.trim());			
		}
	</script>

	@if(!Auth::check())
	<script>
	/*		
		editor_code.on("paste", function(texteColle) {
			console.log("Text collé: " + texteColle.text);
			if (!editor_code.getSession().getValue().includes(texteColle.text)) {
				texteColle.text = "";
				console.log("Le collage de ce texte N'est PAS autorisé.");
			} else {
				console.log("Le collage de ce texte est autorisé.");
			}
		});
		*/
	</script>	
	@endif

    <script>
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
		var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		  return new bootstrap.Tooltip(tooltipTriggerEl)
		})
	</script>

</body>
</html>
