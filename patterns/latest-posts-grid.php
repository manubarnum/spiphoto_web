<?php
/**
 * Title: Derniers articles en grille
 * Slug: spiphoto-fse/latest-posts-grid
 * Categories: spiphoto-fse-posts
 * Keywords: articles, derniers, grille, actualités, cartes
 * Block Types: core/query
 * Description: Titre de section et grille de trois colonnes des six derniers articles, au format des cartes de l'accueil.
 */
?>
<!-- wp:group {"layout":{"type":"constrained","contentSize":"1200px"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|7","bottom":"var:preset|spacing|7"}}}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--7);margin-bottom:var(--wp--preset--spacing--7)">
<!-- wp:group {"className":"section-header","layout":{"type":"default"}} -->
<div class="wp-block-group section-header">
<!-- wp:heading -->
<h2 class="wp-block-heading">Derniers articles</h2>
<!-- /wp:heading -->
</div>
<!-- /wp:group -->

<!-- wp:query {"queryId":21,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false}} -->
<div class="wp-block-query">
<!-- wp:post-template {"layout":{"type":"grid","columnCount":3},"style":{"spacing":{"blockGap":"var:preset|spacing|5"}}} -->
<!-- wp:group {"className":"post-card","layout":{"type":"default"}} -->
<div class="wp-block-group post-card">
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2"} /-->

<!-- wp:group {"className":"post-card-body","layout":{"type":"default"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|4","bottom":"var:preset|spacing|5","left":"var:preset|spacing|5","right":"var:preset|spacing|5"}}}} -->
<div class="wp-block-group post-card-body" style="padding-top:var(--wp--preset--spacing--4);padding-right:var(--wp--preset--spacing--5);padding-bottom:var(--wp--preset--spacing--5);padding-left:var(--wp--preset--spacing--5)">
<!-- wp:post-terms {"term":"category","className":"category-badge"} /-->

<!-- wp:post-title {"level":3,"isLink":true,"style":{"spacing":{"margin":{"top":"var:preset|spacing|2","bottom":"var:preset|spacing|2"}}}} /-->

<!-- wp:post-date {"format":"j M Y"} /-->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p>Aucun article pour le moment.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->
</div>
<!-- /wp:group -->
