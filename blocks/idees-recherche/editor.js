/* Bloc « Idées de recherche » : rendu côté serveur, simple repère dans l'éditeur. */
(function (blocks, element, blockEditor) {
	var el = element.createElement;
	blocks.registerBlockType('spiphoto/idees-recherche', {
		edit: function (props) {
			return el('div', blockEditor.useBlockProps({ className: 'sp-search-ideas-repere' }),
				el('p', null, props.attributes.label || 'Idées de recherche :'),
				el('small', null, 'Les mots-clés du thème s’afficheront ici.')
			);
		},
		save: function () { return null; }
	});
})(window.wp.blocks, window.wp.element, window.wp.blockEditor);
