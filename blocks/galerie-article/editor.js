/* Bloc « Galerie de l'article » : rendu côté serveur, simple repère dans l'éditeur. */
(function (blocks, element, blockEditor) {
	var el = element.createElement;
	blocks.registerBlockType('spiphoto/galerie-article', {
		edit: function () {
			return el('div', blockEditor.useBlockProps({ className: 'sp-galerie-repere' }),
				el('p', null, 'Galerie de l’article'),
				el('small', null, 'Les galeries placées dans l’article s’afficheront ici.')
			);
		},
		save: function () { return null; }
	});
})(window.wp.blocks, window.wp.element, window.wp.blockEditor);
