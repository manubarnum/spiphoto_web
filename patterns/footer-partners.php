<?php
/**
 * Title: Logos des partenaires
 * Slug: spiphoto-fse/footer-partners
 * Categories: footer
 * Inserter: no
 * Description: Logos des partenaires du club, inclus dans le pied de page.
 *
 * Composition PHP plutot que blocs Image en dur dans parts/footer.html : les
 * URL des medias different entre le local (/wp-content/uploads) et la prod
 * (/wordpress/wp-content/uploads), seuls les identifiants sont communs.
 * Reprend l'ancien widget « NOS PARTENAIRES » de Hueman.
 */

$spiphoto_partners = [
	[
		'id'  => 64141,
		'alt' => 'Sport-Santé Bien-être, Team de Gournay-en-Bray',
		'url' => 'https://www.facebook.com/fouleesrosesgournay',
	],
	[
		'id'  => 64142,
		'alt' => 'Marc Kraskowski, sculptures murales',
		'url' => 'https://kraskowski-sculpture.e-monsite.com/',
	],
	[
		'id'  => 64143,
		'alt' => 'Ville de Gournay-en-Bray',
		'url' => 'https://www.gournay-en-bray.fr/',
	],
];
?>
<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"},"style":{"spacing":{"blockGap":"1.25rem"}}} -->
<div class="wp-block-group">
<?php
foreach ( $spiphoto_partners as $partner ) :
	$src = wp_get_attachment_image_url( $partner['id'], 'medium' );
	if ( ! $src ) {
		continue;
	}
	$img = sprintf(
		'<img src="%s" alt="%s" class="wp-image-%d" style="width:auto;height:40px"/>',
		esc_url( $src ),
		esc_attr( $partner['alt'] ),
		$partner['id']
	);
	if ( $partner['url'] ) {
		$img = sprintf( '<a href="%s" target="_blank" rel="noreferrer noopener">%s</a>', esc_url( $partner['url'] ), $img );
	}
	$attrs = [
		'id'              => $partner['id'],
		'width'           => 'auto',
		'height'          => '40px',
		'sizeSlug'        => 'medium',
		'linkDestination' => $partner['url'] ? 'custom' : 'none',
		// no-lightbox : exclu par Meow Lightbox (reglage anti_selector), dont le
		// selecteur .wp-block-image capterait sinon le clic au lieu du lien.
		'className'       => 'footer-partner-logo no-lightbox',
	];
	?>
<!-- wp:image <?php echo wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES ); ?> -->
<figure class="wp-block-image size-medium is-resized footer-partner-logo no-lightbox"><?php echo $img; ?></figure>
<!-- /wp:image -->
<?php endforeach; ?>
</div>
<!-- /wp:group -->
