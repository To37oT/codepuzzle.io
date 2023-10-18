<?php
$devoir = App\Models\Devoir::where('jeton_secret', $jeton_secret)->first();
if (!$devoir){
    echo "<pre>Cet entraînement n'existe pas</pre>";
    exit();
}
$devoir_eleves = App\Models\Devoir_eleve::where('jeton_devoir', $devoir->jeton)->get();
?>
<!doctype html>
<html lang="fr">
<head>
    @include('inc-meta')

    <script src="https://cdn.jsdelivr.net/pyodide/v0.24.1/full/pyodide.js"></script>
    <title>ENTRAÎNEMENT - CONSOLE</title>
</head>
<body>

    <!-- Écran de démarrage -->
    <div id="splashscreen" class="splashscreen">
        <i class="fas fa-spinner fa-spin" style="color:black;opacity:1;z-index:2000"></i>
    </div>

	<div class="container mb-5">

		<div class="row pt-3">

			<div class="col-md-2">

                <div class="text-right mb-3">
                    <a class="btn btn-light btn-sm" href="/" role="button"><i class="fas fa-arrow-left"></i></a>
                </div>

                <a class="btn btn-success btn-sm pl-3 pr-3 text-monospace" style="width:100%" href="{{route('devoir-creer-get')}}" role="button">{{__('nouvel entraînement')}}</a>

                <a href="https://github.com/codepuzzle-io/www.codepuzzle.io/discussions" target="_blank" role="button" class="mt-2 btn btn-light btn-sm text-left text-muted" style="width:100%;opacity:0.8;">
                	<span style="font-size:80%"><i class="fas fa-comment-alt" style="float:left;margin:4px 8px 5px 0px;"></i> {{__('discussions')}} <span style="opacity:0.6;font-size:90%;">&</span> {{__('annonces')}}</span>
                </a>

                <a href="https://github.com/codepuzzle-io/www.codepuzzle.io/issues/new/choose" target="_blank" role="button"  class="mt-1 btn btn-light text-left btn-sm text-muted" style="width:100%;opacity:0.8;">
                	<span style="font-size:80%"><i class="fas fa-bug" style="float:left;margin:4px 8px 5px 0px;"></i> {{__('signalement de bogue')}} <span style="opacity:0.6;font-size:90%;">&</span> {{__('questions techniques')}}</span>
                </a>

                <div class="mt-3 text-muted text-monospace pl-1 mb-5" style="font-size:70%;opacity:0.8;">
                	<span><i class="fa fa-envelope"></i> contact@codepuzzle.io</span>
                </div>

            </div>

			<div class="col-md-10 pl-4 pr-4">

                <div id="frame" class="frame">

                    <div class="row">
                        <div class="col-md-12">

                            @if(isset($_GET['w']))
                                <div class="text-monospace text-danger text-center font-weight-bold m-2">SAUVEGARDEZ LES INFORMATIONS CI-DESSOUS AVANT DE QUITTER CETTE PAGE</div>
                            @endif

                            <table class="table table-borderless text-monospace m-0" style="border-spacing:5px;border-collapse:separate;">
                                <tr>
                                    <td class="text-center font-weight-bold p-0" style="width:33%">lien secret</td>
                                    <td class="text-center font-weight-bold p-0" style="width:33%">code secret</td>
                                    <td class="text-center font-weight-bold p-0" style="width:33%">lien élèves</td>
                                </tr>
                                <tr>
                                    <td class="text-center text-white p-2 text-break align-middle" style="background-color:#d14d41;border-radius:3px;"><a href="/console-devoir/E{{strtoupper($devoir->jeton_secret)}}" target="_blank" class="text-white">www.codepuzzle.net/console-devoir/E{{strtoupper($devoir->jeton_secret)}}</a></td>
                                    <td class="text-center text-white p-2 text-break align-middle" style="background-color:#d0a215;border-radius:3px;">{{$devoir->mot_secret}}</td>
                                    <td class="text-center text-white p-2 text-break align-middle" style="background-color:#879a39;border-radius:3px;"><a href="/E{{strtoupper($devoir->jeton)}}" target="_blank" class="text-white">www.codepuzzle.net/E{{strtoupper($devoir->jeton)}}</a></td>
                                </tr>
                                <tr>
                                    <td class="small text-muted p-0"><span class="text-danger"><i class="fas fa-exclamation-circle"></i> Ne pas partager ce lien</span><br />Il permet d'accéder à la console de l'entraînement (sujet, lien pour les élèves, correction...).</td>
                                    <td class="small text-muted p-0"><span class="text-danger"><i class="fas fa-exclamation-circle"></i> Ne pas partager ce code</span><br />Il permet de déverrouiller la copie d'un élève.</td>
                                    <td class="small text-muted p-0">Lien à fournir aux élèves.<br />QR code: <img src="https://api.qrserver.com/v1/create-qr-code/?data={{urlencode('https://www.codepuzzle.io/E' . strtoupper($devoir->jeton))}}&amp;size=200x200" style="width:50px" alt="www.codepuzzle.io/E{{strtoupper($devoir->jeton)}}" data-toggle="tooltip" data-placement="bottom" title="{{__('clic droit + Enregistrer l image sous... pour sauvegarder l image')}}" /></td>
                                </tr>
                            </table>

                        </div>
                    </div>

                </div>

                <div id="frame" class="frame">

                    <div class="row">
                        <div class="col-md-12">

                            <div class="text-monospace font-weight-bold"><a data-toggle="collapse" href="#collapseSujet" role="button" aria-expanded="false" aria-controls="collapseSujet"><i class="fas fa-plus-square"></i></a> SUJET</div>
                            

                            <div class="collapse mb-3" id="collapseSujet">

                                <!-- CONSIGNES -->
                                <div class="text-monospace mt-3">{{strtoupper(__('consignes'))}}</div>
                                <div class="card card-body">
                                    <div class="text-monospace consignes">
                                        <?php
                                        $Parsedown = new Parsedown();
                                        echo $Parsedown->text($devoir->consignes_eleve);
                                        ?>
                                    </div>
                                </div>
                                <!-- CONSIGNES -->

                                <!-- CODE ELEVE --> 
                                <div class="mt-2 text-monospace">{{strtoupper(__('code ÉlÈve'))}}</div>
                                <textarea name="code_eleve" style="display:none;" id="code_eleve"></textarea>
                                <div id="editor_code_eleve" style="border-radius:5px;">{{ $devoir->code_eleve }}</div>
                                <!-- /CODE ELEVE -->

                                <!-- CODE ENSEIGNANT --> 
                                <div class="mt-2 text-monospace">{{strtoupper(__('code enseignant'))}}</div>
                                <textarea name="code_enseignant" style="display:none;" id="code_enseignant"></textarea>
                                <div id="editor_code_enseignant" style="border-radius:5px;">{{ $devoir->code_enseignant }}</div>
                                <!-- /CODE ENSEIGNANT -->                            

                                <!-- SOLUTION --> 
                                <div class="mt-2 text-monospace">{{strtoupper(__('solution possible'))}}</div>
                                <textarea name="solution" style="display:none;" id="solution"></textarea>
                                <div id="editor_solution" style="border-radius:5px;">{{ $devoir->solution }}</div>
                                <!-- /SOLUTION --> 	

                            </div>

                        </div>
                    </div>

                </div>                

                <div class="row mt-3 mb-5">
                    <div class="col-md-12">
                        @foreach($devoir_eleves as $devoir_eleve)

                            <div id="frame" class="frame mt-1 mb-1">

                                <div class="text-monospace">
                                    <div style="float:right;right:10px">
                                        @if($devoir_eleve->revised == 1)
                                            <i class="fas fa-check-circle text-success"></i></div>
                                        @else
                                            <i class="fas fa-check-circle" style="color:silver;"></i></div>
                                        @endif
                                    <a data-toggle="collapse" class="text-dark" href="#collapseEntrainement-{{$loop->iteration}}" role="button" aria-expanded="false" aria-controls="collapseEntrainement-{{$loop->iteration}}"><i class="fas fa-plus-square"></i></a>

                                    <span class="">{{$devoir_eleve->pseudo}}</span>
                                </div>

                                <div class="collapse" id="collapseEntrainement-{{$loop->iteration}}">

                                    <div class="text-monospace mt-2">Code élève <i class="text-muted small">en lecture seule</i></div>
                                    <div>
                                        <div id="editor_code_eleve_devoir-{{$loop->iteration}}" style="border-radius:5px;">{{$devoir_eleve->code_eleve}}</div>
                                    </div>

                                    <div class="text-monospace mt-2">Code enseignant</div>
                                    <div>
                                        <?php
                                        if($devoir_eleve->code_enseignant == NULL){
                                            $code_enseignant = $devoir->code_enseignant;
                                        }else{
                                            $code_enseignant = $devoir_eleve->code_enseignant;
                                        }
                                        ?>
                                        <div id="editor_code_enseignant_devoir-{{$loop->iteration}}" style="border-radius:5px;">{{$code_enseignant}}</div>
                                    </div>

                                    <table class="mt-2 mb-2" style="width:100%">
                                        <tr>
                                            <td style="width:50%">
                                                <div style="line-height:1">
                                                    <div class="form-check d-block m-0 text-right">
                                                        <span class="text-monospace small text-muted">code élève</span>
                                                        <input id="code_option_1_devoir-{{$loop->iteration}}" name="code_option_devoir-{{$loop->iteration}}" class="align-middle" style="display:inline;cursor:pointer" type="radio" />
                                                        
                                                    </div>
                                                    <div class="form-check d-block m-0 text-right">
                                                        <span class="text-monospace small text-muted">code élève + code enseignant</span>
                                                        <input id="code_option_2_devoir-{{$loop->iteration}}" name="code_option_devoir-{{$loop->iteration}}" class="align-middle" style="display:inline;cursor:pointer" type="radio" checked />
                                                    </div>
                                                    <div class="form-check d-block m-0 text-right">
                                                        <span class="text-monospace small text-muted">code enseignant</span>
                                                        <input id="code_option_3_devoir-{{$loop->iteration}}" name="code_option_devoir-{{$loop->iteration}}" class="align-middle" style="display:inline;cursor:pointer" type="radio" />
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="width:50%;vertical-align:top">
                                                <div class="text-left pl-2">
                                                    <button onclick="evaluate_python({{$loop->iteration}})" type="button" class="btn btn-primary btn-sm text-monospace pt-2 pb-2 pl-3 pr-3" style="display:inline"><i class="fas fa-play"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>

                                    <div>
                                        <div class="text-monospace">Console</div>
                                        <pre id="terminal-{{$loop->iteration}}" class="bg-dark text-monospace p-3 small text-white" style="border-radius:4px;border:1px solid silver;min-height:80px;"></pre>
                                    </div>

                                    <div class="text-monospace text-success font-weight-bold mt-2">Commentaires</div>
                                    <textarea id="commentaires-{{$loop->iteration}}" class="form-control border border-success text-success" rows="3">{{$devoir_eleve->commentaires}}</textarea>
                                    <button onclick="save_commentaires({{$loop->iteration}}, {{$devoir_eleve->id}})" type="button" class="btn btn-success btn-sm text-monospace mt-2 pt-2 pb-2 pl-3 pr-3" style="display:inline"><i class="fas fa-save"></i></button>
                                
                                </div>

                            </div>

                        @endforeach
                    </div>
                </div>

            </div>
        </div>
	</div><!-- /container -->

    @include('inc-bottom-js')

    <script>
        MathJax = {
			tex: {
				inlineMath: [['$', '$'], ['\\(', '\\)']]
			},
			options: {
				ignoreHtmlClass: "no-mathjax",
				processHtmlClass: "mathjax"
			},
			svg: {
				fontCache: 'global'
			}
        };
    </script>    
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script type="text/javascript" id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js"></script> 	

    <script src="{{ asset('js/ace/ace.js') }}" type="text/javascript" charset="utf-8"></script>

	<script>
		// Chargement de ace et initialisation des éditeurs.
		
		
		async function init_editors() {
			var editor_code_eleve = ace.edit("editor_code_eleve", {
				theme: "ace/theme/puzzle_code",
				mode: "ace/mode/python",
				maxLines: 500,
				minLines: 1,
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
                readOnly: true,
				tabSize: 4
			});
			editor_code_eleve.container.style.lineHeight = 1.5;

			var editor_code_enseignant = ace.edit("editor_code_enseignant", {
				theme: "ace/theme/puzzle_code",
				mode: "ace/mode/python",
				maxLines: 500,
				minLines: 1,
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
                readOnly: true,
				tabSize: 4
			});
			editor_code_enseignant.container.style.lineHeight = 1.5;

			var editor_solution = ace.edit("editor_solution", {
				theme: "ace/theme/puzzle_fakecode",
				mode: "ace/mode/python",
				maxLines: 500,
				minLines: 1,
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
                readOnly: true,                
				tabSize: 4
			});
			editor_solution.container.style.lineHeight = 1.5;

        }

        (async function() {
			// Chargement asynchrone de ace et initialisation des éditeurs
			const editors_initialized_promise = init_editors();
			// Pour être sur que ace est chargé et les éditeurs initialisés.
			await editors_initialized_promise;		
		})();	

            var editor_code_eleve_devoir = []
            var editor_code_enseignant_devoir = []
            for (var i = 1; i <= {{$devoir_eleves->count() }}; i++) {
                editor_code_eleve_devoir[i] = ace.edit('editor_code_eleve_devoir-' + i, {
                    theme: "ace/theme/puzzle_code",
                    mode: "ace/mode/python",
                    maxLines: 500,
                    fontSize: 14,
                    wrap: true,
                    useWorker: false,
                    highlightActiveLine: false,
                    highlightGutterLine: false,
                    showPrintMargin: false,
                    displayIndentGuides: true,
                    showLineNumbers: true,
                    showGutter: true,
                    showFoldWidgets: false,
                    useSoftTabs: true,
                    navigateWithinSoftTabs: false,
                    tabSize: 4,
                    readOnly: true
                });
                editor_code_eleve_devoir[i].container.style.lineHeight = 1.5;

                editor_code_enseignant_devoir[i] = ace.edit('editor_code_enseignant_devoir-' + i, {
                    theme: "ace/theme/puzzle_code",
                    mode: "ace/mode/python",
                    maxLines: 500,
                    fontSize: 14,
                    wrap: true,
                    useWorker: false,
                    highlightActiveLine: false,
                    highlightGutterLine: false,
                    showPrintMargin: false,
                    displayIndentGuides: true,
                    showLineNumbers: true,
                    showGutter: true,
                    showFoldWidgets: false,
                    useSoftTabs: true,
                    navigateWithinSoftTabs: false,
                    tabSize: 4
                });
                editor_code_enseignant_devoir[i].container.style.lineHeight = 1.5;
            }        

	</script>

    <script>
		//document.getElementById("output").innerText = "Initialisation...\n";
		console.log("Initialisation...");

		// init Pyodide
		async function main() {
			let pyodide = await loadPyodide();
			//document.getElementById("output").innerText = "Prêt!\n";
            document.getElementById('splashscreen').remove();
            console.log("Prêt!");
			return pyodide;
		}

		let pyodideReadyPromise = main();

		async function evaluate_python(i) {
			console.log('EVALUATE PYTHON')
            var code = "";
            if (document.getElementById("code_option_1_devoir-" + i).checked) {
                code = editor_code_eleve_devoir[i].getValue();
            } else if (document.getElementById("code_option_2_devoir-" + i).checked) {
                code = editor_code_eleve_devoir[i].getValue() + "\n" + editor_code_enseignant_devoir[i].getValue();
            } else if (document.getElementById("code_option_3_devoir-" + i).checked) {
                code = editor_code_enseignant_devoir[i].getValue();
            }
            console.log("Code:\n" + code + "\n----------\n");
            
			let pyodide = await pyodideReadyPromise;
			await pyodide.loadPackagesFromImports(code);
			
			try {
				// pas d'erreur python
				document.getElementById("terminal-" + i).innerText = "";
				pyodide.setStdout({batched: (str) => {
					document.getElementById("terminal-" + i).innerText += str+"\n";
					console.log(str);
				}})
				let output_content = pyodide.runPython(code);
                if (typeof(output_content) !== 'undefined'){
                    document.getElementById("terminal-" + i).innerText += output_content
                }
			} catch (err) {
				// erreur python
				let error_message = err.message.split("File \"<exec>\", ");
				error_message = "Error " + error_message[1];
                if (typeof(error_message) !== 'undefined'){
                    document.getElementById("terminal-" + i).innerText += error_message
                }
			}		
		}
	</script>


    <script>
        function save_commentaires(i, id) {
            var formData = new URLSearchParams();
            formData.append('devoir_eleve_id', id);
            formData.append('code_enseignant', encodeURIComponent(editor_code_enseignant_devoir[i].getValue()));
            formData.append('commentaires', encodeURIComponent(document.getElementById('commentaires-'+i).value));
            fetch('/devoir-save-commentaires', {
                method: 'POST',
                headers: {"Content-Type": "application/x-www-form-urlencoded", "X-CSRF-Token": "{{ csrf_token() }}"},
                body: formData
            })
            .then(function(response) {
                // Renvoie la réponse du serveur (peut contenir un message de confirmation)
                return response.text();
            })
            .then(function(data) {
                // Affiche la réponse du serveur dans la console
                console.log('Réponse du serveur:', data); 
            })
            .catch(function(error) {
                // Gère les erreurs liées à la requête Fetch
                console.error('Erreur:', error); 
            });
        }
    </script>

    <script>
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
		var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		  return new bootstrap.Tooltip(tooltipTriggerEl)
		})
	</script>

</body>
</html>
