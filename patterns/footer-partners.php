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
 * partenaire), par ordre alphabétique du nom.
 * - Nom du lien : texte alternatif du logo ;
 * - Adresse web : lien du logo (nouvel onglet) ;
 * - Adresse de l'image (Avancé) : logo. Une image de la médiathèque est
 *   affichée en taille « medium » ; une autre adresse, telle quelle. Sans
 *   image, le nom du partenaire s'affiche en texte.
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
	if ( $spiphoto_id ) {
		$spiphoto_src = wp_get_attachment_image_url( $spiphoto_id, 'medium' ) ?: $spiphoto_src;
	}

	if ( $spiphoto_src ) {
		$spiphoto_inner = sprintf(
			'<img src="%s" alt="%s"%s style="width:auto;height:64px" loading="lazy"/>',
			esc_url( $spiphoto_src ),
			esc_attr( $spiphoto_name ),
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

	$spiphoto_attrs = array(
		'width'           => 'auto',
		'height'          => '64px',
		'sizeSlug'        => 'medium',
		'linkDestination' => $spiphoto_url ? 'custom' : 'none',
		// no-lightbox : exclu par Meow Lightbox (réglage anti_selector), dont le
		// sélecteur .wp-block-image capterait sinon le clic au lieu du lien.
		'className'       => 'footer-partner-logo no-lightbox',
	);
	if ( $spiphoto_id ) {
		$spiphoto_attrs = array( 'id' => (int) $spiphoto_id ) + $spiphoto_attrs;
	}
	?>
<!-- wp:image <?php echo wp_json_encode( $spiphoto_attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?> -->
<figure class="wp-block-image size-medium is-resized footer-partner-logo no-lightbox"><?php echo $spiphoto_inner; // Échappé ci-dessus. ?></figure>
<!-- /wp:image -->
<?php endforeach; ?>
</div>
<!-- /wp:group -->
