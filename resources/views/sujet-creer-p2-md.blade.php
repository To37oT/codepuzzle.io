<?php
if (isset($jeton_secret)) {
	$devoir = App\Models\Devoir::where('jeton_secret', $jeton_secret)->first();
	if (!$devoir) {
		echo "<pre>Ce devoir n'existe pas.</pre>";
		exit();
	} else {
		$titre_enseignant = $devoir->titre_enseignant;
		$sous_titre_enseignant = $devoir->sous_titre_enseignant;
		$titre_eleve = $devoir->titre_eleve;
		$consignes = $devoir->consignes_eleve;
		$code_eleve = $devoir->code_eleve;
		$code_enseignant = $devoir->code_enseignant;
		$solution = $devoir->solution;
		$with_chrono = $devoir->with_chrono;
		$with_console = $devoir->with_console;

		if ($devoir->user_id !== 0 && (!Auth::check() || (Auth::check() && Auth::id() !== $devoir->user_id))) {
			echo "<pre>Vous ne pouvez pas accéder à ce devoir.</pre>";
			exit();
		}
	}
}
?>
<!doctype html>
<html lang="fr">
<head>
	@include('inc-meta')
    <title>SUJET | CRÉER / MODIFIER</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
    <script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
    <style>
        button.table {
            width:30px !important;
        }
        .editor-toolbar {
            position: relative;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            -o-user-select: none;
            user-select: none;
            padding: 4px;
            border: 1px solid #dae0e5;
            border-radius: 4px;
            background-color: none;;
        }
        .EasyMDEContainer .CodeMirror {
            box-sizing: border-box;
            height: auto;
            border: 1px solid #dae0e5;
            border-radius: 4px;
            padding: 10px;
            font: inherit;
            z-index: 0;
            word-wrap: break-word;
            margin-top: 2px;
        }
        .editor-toolbar button.active, .editor-toolbar button:hover {
            background: white;
            border-color: #dae0e5;
            border-radius: 4px;
        }
    </style>

</head>
<body>

	@if(Auth::check())
		@include('inc-nav-console')
	@else
		@include('inc-nav')
	@endif

	<!-- MODAL MARKDOWN HELP -->
	<div class="modal fade" id="markdown_help" tabindex="-1" aria-labelledby="markdown_helpLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<b class="modal-title" id="exampleModalLabel">Formatage du texte</b>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<table class="table table-bordered table-hover small">
						<tr>
							<td></td>
							<td class="p-2 text-center">SYNTAXE</td>
							<td class="p-2 text-center">RENDU</td>
						</tr>
						<tr>
							<td class="p-2">CODE DANS DU TEXTE</td>
							<td class="p-2 text-monospace text-muted">Écrire la fonction `puissance(a, n)`.</td>
							<td class="p-2" style="vertical-align:top">Écrire la fonction <code>puissance(a, n)</code>.</td>
						</tr>		
						<tr>
							<td class="p-2">BLOC DE CODE</td>
							<td class="p-2 text-monospace text-muted">Soit la fonction:<br />```<br />def carre(n):<br />&nbsp;&nbsp;&nbsp;&nbsp;return n**2<br />```</td>
							<td class="p-2" style="vertical-align:top">Soit la fonction:<pre>def carre(n):<br />&nbsp;&nbsp;&nbsp;&nbsp;return n**2<br /></pre></td>
						</tr>					
						<tr>
							<td class="p-2">PARAGRAPHES</td>
							<td class="p-2 text-monospace text-muted">paragraphe<br /><br />paragraphe<p class="mt-2 mb-0" style="color:silver">Laisser une ligne vide pour marquer un nouveau paragraphe.</p></td>
							<td class="p-2" style="vertical-align:top"><p class="mb-1">paragraphe</p>paragraphe</td>
						</tr>
						<tr>
							<td class="p-2">RETOUR À LA LIGNE</td>
							<td class="p-2 text-monospace text-muted">ligne &lt;br&gt;<br />ligne<p class="mt-2 mb-0" style="color:silver">Ajouter &lt;br&gt; en bout de ligne pour forcer le retour à la ligne.</p></td>
							<td class="p-2" style="vertical-align:top">ligne<br />ligne</td>
						</tr>
						<tr>
							<td class="p-2">LISTES</td>
							<td class="p-2 text-monospace text-muted">* point 1<br />* point 2<br /></td>
							<td class="p-2" style="vertical-align:top"><ul style="padding-left:20px;margin-left:0;margin-bottom:0"><li>point 1</li><li>point 2</li></ul></td>
						</tr>
						<tr>
							<td class="p-2">ITALIQUE</td>
							<td class="p-2 text-monospace text-muted">*italique*</td>
							<td class="p-2"><em>italique</em></td>
						</tr>
						<tr>
							<td class="p-2">GRAS</td>
							<td class="p-2 text-monospace text-muted">**gras**</td>
							<td class="p-2"><b>gras</b></td>
						</tr>
						<tr>
							<td class="p-2">SOULIGNÉ</td>
							<td class="p-2 text-monospace text-muted">__souligné__</td>
							<td class="p-2"><u>souligné</u></td>
						</tr>
						<tr>
							<td class="p-2">IMAGE</td>
							<td class="p-2 text-monospace text-muted">
								<p>![](url-image)</p>
								<p class="mb-0"><i>Exemple : ![](https://www.codepuzzle.io/img/codepuzzle.png)<i></p>
							</td>
							<td class="p-2"><img src="https://www.codepuzzle.io/img/codepuzzle.png" width="160"/></td>
						</tr>
						<tr>
							<td class="p-2">LIEN</td>
							<td class="p-2 text-monospace text-muted">
								<p>[texte-cliquable](url-site)</p>
								<p class="mb-1"><i>Exemple 1 : Un [lien](https://eduscol.education.fr) vers Eduscol.</i></p>
								<p class="mb-0"><i>Exemple 2 : Un lien vers [Eduscol](https://eduscol.education.fr).</i></p>
							</td>
							<td class="p-2">
								<p><br /></p>
								<p class="mb-1">Un <a href="http://pep8online.com/" target="_blank">lien</a> vers Eduscol.</p>
								<p class="mb-0">Un lien vers <a href="http://pep8online.com/" target="_blank">Eduscol</a>.</p>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
	<!-- MODAL MARKDOWN HELP -->


	<div class="container mt-4 mb-5">

		<div class="row">

			<div class="col-md-2 text-right">
				@if(Auth::check())
				<a class="btn btn-light btn-sm" href="/console/devoirs" role="button"><i class="fas fa-arrow-left"></i></a>
				@else
				<a class="btn btn-light btn-sm" href="/" role="button"><i class="fas fa-arrow-left"></i></a>
				<div class="mt-3 small text-monospace text-muted">Vous pouvez <a href="/creer-un-compte" target="_blank">créer un compte</a> pour regrouper, gérer et partager vos sujets.</div>
				@endif
			</div>

			<div class="col-md-10 pl-4 pr-4">

				<h1>{{__('nouveau sujet')}}</h1>
				<div>Étape 1</div>
				<div>Sujet: format Markdown ou document PDF</div>
                <div id="md"></div>

				<form method="POST" action="{{route('devoir-creer-post')}}">

					@csrf
					
					<div class="mt-4 text-monospace">{{strtoupper(__('sujet'))}}</div>

                    

					<textarea id="sujet_markdown"></textarea>











                    <script>
                    //const easyMDE = new EasyMDE({element: document.getElementById('my-text-area')});
                    const editor = new EasyMDE({
                        
                        /*autofocus: true,*/
                        showIcons: ["code", "upload-image", "undo", "redo", "table"],
                        autosave: {
                            enabled: true,
                            uniqueId: "MyUniqueID",
                            delay: 1000,
                            submit_delay: 5000,
                            timeFormat: {
                                locale: 'en-US',
                                format: {
                                    year: 'numeric',
                                    month: 'long',
                                    day: '2-digit',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                },
                            },
                            text: "Autosaved: "
                        },
                        element: document.getElementById('sujet_markdown'),
                        /*
                        blockStyles: {
                            bold: "__",
                            italic: "_",
                        },
                        unorderedListStyle: "-",
                        
                        forceSync: true,
                        hideIcons: ["guide", "heading"],
                        indentWithTabs: false,
                        initialValue: "Hello world!",
                        insertTexts: {
                            horizontalRule: ["", "\n\n-----\n\n"],
                            image: ["![](http://", ")"],
                            link: ["[", "](https://)"],
                            table: ["", "\n\n| Column 1 | Column 2 | Column 3 |\n| -------- | -------- | -------- |\n| Text     | Text      | Text     |\n\n"],
                        },
                        lineWrapping: false,
                        minHeight: "500px",
                        parsingConfig: {
                            allowAtxHeaderWithoutSpace: true,
                            strikethrough: false,
                            underscoresBreakWords: true,
                        },
                        placeholder: "Type here...",

                        previewClass: "my-custom-styling",
                        previewClass: ["my-custom-styling", "more-custom-styling"],

                        previewRender: (plainText) => customMarkdownParser(plainText), // Returns HTML from a custom parser
                        previewRender: (plainText, preview) => { // Async method
                            setTimeout(() => {
                                preview.innerHTML = customMarkdownParser(plainText);
                            }, 250);

                            // If you return null, the innerHTML of the preview will not
                            // be overwritten. Useful if you control the preview node's content via
                            // vdom diffing.
                            // return null;

                            return "Loading...";
                        },
                        promptURLs: true,
                        promptTexts: {
                            image: "Custom prompt for URL:",
                            link: "Custom prompt for URL:",
                        },
                        renderingConfig: {
                            singleLineBreaks: false,
                            codeSyntaxHighlighting: true,
                            sanitizerFunction: (renderedHTML) => {
                                // Using DOMPurify and only allowing <b> tags
                                return DOMPurify.sanitize(renderedHTML, {ALLOWED_TAGS: ['b']})
                            },
                        },
                        shortcuts: {
                            drawTable: "Cmd-Alt-T"
                        },
                        showIcons: ["code", "table"],
                        spellChecker: false,
                        status: false,
                        status: ["autosave", "lines", "words", "cursor"], // Optional usage
                        status: ["autosave", "lines", "words", "cursor", {
                            className: "keystrokes",
                            defaultValue: (el) => {
                                el.setAttribute('data-keystrokes', 0);
                            },
                            onUpdate: (el) => {
                                const keystrokes = Number(el.getAttribute('data-keystrokes')) + 1;
                                el.innerHTML = `${keystrokes} Keystrokes`;
                                el.setAttribute('data-keystrokes', keystrokes);
                            },
                        }], // Another optional usage, with a custom status bar item that counts keystrokes
                        styleSelectedText: false,
                        tabSize: 4,
                        toolbarTips: false,
                        toolbarButtonClassPrefix: "mde",
                        toolbar: [
                            {
                                name: "bold",
                                action: EasyMDE.toggleBold,
                                className: "fa-solid fa-bold",
                                title: "Bold",
                            },
                        ],
                        */
                    });


                    // Voir https://github.com/Ionaru/easy-markdown-editor/issues/245
                    //editor.togglePreview(editor)
                    //console.log(editor.markdown(editor.value()));
                    //console.log(marked.parse(editor.value())); 
                    </script>
                    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
                    <script>
                        // pour limiter les options
                        marked.use({
                            tokenizer: {
                                //space() { return undefined },
                                //code() { return undefined },
                                //fences() { return undefined },
                                heading() { return undefined },
                                hr() { return undefined },
                                blockquote() { return undefined },
                                //list() { return undefined },
                                html() { return undefined },
                                //def() { return undefined },
                                table() { return undefined },
                                lheading() { return undefined },
                                //paragraph() { return undefined },
                                //text() { return undefined },
                                //escape() { return undefined },
                                tag() { return undefined },
                                link() { return undefined },
                                reflink() { return undefined },
                                //emStrong() { return undefined },
                                //codespan() { return undefined },
                                br() { return undefined },
                                del() { return undefined },
                                autolink() { return undefined },
                                url() { return undefined },
                                //inlineText() { return undefined },
                            }
                        })
                        console.log(marked.parse(editor.value())); 
                        document.getElementById("md").innerHTML = marked.parse(editor.value());
                    </script>

                    



					<button class="btn btn-primary fw-medium py-3 px-4 mt-3" id="formSubmit" type="submit">
						<span class="spinner-border spinner-border-sm d-none me-2" aria-hidden="true"></span>
						Submit Form
					</button>
				</form>

			</div>
		</div><!-- /row -->
	</div><!-- /container -->

	@include('inc-bottom-js')

</body>
</html>

