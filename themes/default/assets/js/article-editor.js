document.addEventListener('DOMContentLoaded', () => {

	const textarea = document.getElementById('content');

	if (!textarea) {
		return;
	}

	new EasyMDE({
		element: textarea,

		spellChecker: false,

		autofocus: false,

		status: false,
		
		toolbar: [
			'bold',
			'italic',
			'heading',
			'|',
			'unordered-list',
			'ordered-list',
			'quote',
			'|',
			'link',
			'code',
			'guide',
			'|',
			'side-by-side',
			'fullscreen'
		],

		autosave: {
			enabled: false
		},

		minHeight: '600px',

		placeholder: 'Inizia a scrivere il tuo articolo...',

		renderingConfig: {
			singleLineBreaks: false,
			codeSyntaxHighlighting: true
		}

	});

});
