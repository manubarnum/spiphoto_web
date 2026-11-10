<?php
/**
 * Title: Logos des partenaires
 * Slug: spiphoto-fse/footer-partners
 * Categories: footer
 * Inserter: no
 * Description: Intitulé « Partenaires » et logos des partenaires du club, lus dans le gestionnaire de liens.
 *
 * Source : Liens → catégorie « Nos partenaires » (slug nos-partenaires),
 * liens publics seulement (cocher « Garder ce lien privé » retire un
 * partenaire).
 * Ordre : champ « Note » du lien (Avancé), utilisé comme position : 1 en
 * premier, puis 2, 3… ; les liens notés 0 (non renseigné) viennent ensuite.
 * À note égale, ordre alphabétique du nom.
 * - Nom du lien : texte alternatif du logo ;
 * - Adresse web : lien du logo (nouvel onglet) ;
 * - Adresse de l'image (Avancé) : logo. Une image de la médiathèque est
 *   affichée en taille « medium », avec ses dimensions (pas de décalage à
 *   l'affichage) ; elle est reconnue par son chemin dans le dossier des
 *   médias, quel que soit le domaine de l'adresse (site local, production,
 *   /wordpress/…). Une autre adresse est affichée telle quelle. Sans image,
 *   le nom du partenaire s'affiche en texte.
 * Sans partenaire public, ni l'intitulé ni le bloc ne s'affichent.
 */

$spiphoto_cat   = get_term_by( 'slug', 'nos-partenaires', 'link_category' );
$spiphoto_links = $spiphoto_cat ? get_bookmarks(
	array(
		'category'       => $spiphoto_cat->term_id,
		'hide_invisible' => 1,
		'orderby'        => 'name',
		'order'          => 'ASC',
	)
) : array();

if ( ! $spiphoto_links ) {
	return;
}

usort(
	$spiphoto_links,
	static function ( $a, $b ) {
		$ra = (int) $a->link_rating ?: 99;
		$rb = (int) $b->link_rating ?: 99;
		return $ra <=> $rb ?: strcasecmp( remove_accents( $a->link_name ), remove_accents( $b->link_name ) );
	}
);
?>
<!-- wp:paragraph {"style":{"spacing":{"margin":{"bottom":"0.875rem"}}},"className":"footer-partners-label"} -->
<p class="footer-partners-label" style="margin-bottom:0.875rem">Partenaires</p>
<!-- /wp:paragraph -->

<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"},"style":{"spacing":{"blockGap":"1.25rem"}}} -->
<div class="wp-block-group">
<?php
foreach ( $spiphoto_links as $spiphoto_link ) :
	$spiphoto_name = wp_strip_all_tags( html_entity_decode( $spiphoto_link->link_name, ENT_QUOTES, 'UTF-8' ) );
	$spiphoto_url  = $spiphoto_link->link_url;
	$spiphoto_src  = trim( (string) $spiphoto_link->link_image );
	$spiphoto_id   = $spiphoto_src ? attachment_url_to_postid( $spiphoto_src ) : 0;
	if ( ! $spiphoto_id && $spiphoto_src && preg_match( '#/wp-content/uploads/(.+)$#', (string) wp_parse_url( $spiphoto_src, PHP_URL_PATH ), $spiphoto_m ) ) {
		$spiphoto_id = attachment_url_to_postid( trailingslashit( wp_get_upload_dir()['baseurl'] ) . $spiphoto_m[1] );
	}
	$spiphoto_dims = '';
	if ( $spiphoto_id && ( $spiphoto_img = wp_get_attachment_image_src( $spiphoto_id, 'medium' ) ) ) {
		$spiphoto_src  = $spiphoto_img[0];
		$spiphoto_dims = $spiphoto_img[1] && $spiphoto_img[2] ? sprintf( ' width="%d" height="%d"', $spiphoto_img[1], $spiphoto_img[2] ) : '';
	}

	if ( $spiphoto_src ) {
		$spiphoto_inner = sprintf(
			'<img src="%s" alt="%s"%s%s style="width:auto;height:64px" loading="lazy" decoding="async"/>',
			esc_url( $spiphoto_src ),
			esc_attr( $spiphoto_name ),
			$spiphoto_dims,
			$spiphoto_id ? ' class="wp-image-' . (int) $spiphoto_id . '"' : ''
		);
	} else {
		$spiphoto_inner = '<span class="footer-partner-name">' . esc_html( $spiphoto_name ) . '</span>';
	}
	if ( $spiphoto_url ) {
		$spiphoto_inner = sprintf(
			'<a href="%s" target="_blank" rel="noreferrer noopener"%s>%s</a>',
			esc_url( $spiphoto_url ),
			$spiphoto_link->link_description ? ' title="' . esc_attr( wp_strip_all_tags( $spiphoto_link->link_description ) ) . '"' : '',
			$spiphoto_inner
		);
	}

	// Bloc « HTML personnalisé » plutôt que « Image » : l'éditeur valide un
	// bloc Image en régénérant son balisage à partir de ses attributs, et ce
	// balisage (dimensions, chargement différé, infobulle, nom sans logo) ne
	// correspond pas à celui-ci (« Le bloc contient du contenu invalide »). Le
	// rendu public est identique.
	// no-lightbox : exclu par Meow Lightbox (réglage anti_selector), dont le
	// sélecteur .wp-block-image capterait sinon le clic au lieu du lien.
	?>
<!-- wp:html -->
<figure class="wp-block-image size-medium is-resized footer-partner-logo no-lightbox"><?php echo $spiphoto_inner; // Échappé ci-dessus. ?></figure>
<!-- /wp:html -->
<?php endforeach; ?>
</div>
<!-- /wp:group -->
