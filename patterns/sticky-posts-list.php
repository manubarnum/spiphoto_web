<?php
/**
 * Title: À ne pas manquer (articles épinglés)
 * Slug: spiphoto-fse/sticky-posts-list
 * Categories: spiphoto-fse-posts
 * Keywords: épinglés, à la une, liste, sidebar, à ne pas manquer
 * Block Types: core/query
 * Description: Encadré listant les articles épinglés, avec vignette, catégorie et date, comme dans la barre latérale de l'accueil.
 */
?>
<!-- wp:group {"className":"sidebar-widget","layout":{"type":"default"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|5","bottom":"var:preset|spacing|5","left":"var:preset|spacing|5","right":"var:preset|spacing|5"}}}} -->
<div class="wp-block-group sidebar-widget" style="padding-top:var(--wp--preset--spacing--5);padding-right:var(--wp--preset--spacing--5);padding-bottom:var(--wp--preset--spacing--5);padding-left:var(--wp--preset--spacing--5)">
<!-- wp:heading {"level":3,"className":"widget-title"} -->
<h3 class="wp-block-heading widget-title">À ne pas manquer</h3>
<!-- /wp:heading -->

<!-- wp:query {"queryId":22,"query":{"perPage":5,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"only","inherit":false}} -->
<div class="wp-block-query">
<!-- wp:post-template -->
<!-- wp:group {"className":"post-list-item","layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top"},"style":{"spacing":{"padding":{"bottom":"var:preset|spacing|3"},"margin":{"bottom":"var:preset|spacing|3"}}}} -->
<div class="wp-block-group post-list-item" style="margin-bottom:var(--wp--preset--spacing--3);padding-bottom:var(--wp--preset--spacing--3)">
<!-- wp:post-featured-image {"isLink":true,"width":"72px","height":"72px"} /-->

<!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group">
<!-- wp:post-terms {"term":"category","className":"category-badge"} /-->

<!-- wp:post-title {"level":4,"isLink":true,"style":{"spacing":{"margin":{"top":"var:preset|spacing|1","bottom":"var:preset|spacing|1"}}}} /-->

<!-- wp:post-date {"format":"j M Y"} /-->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p>Aucun article épinglé pour le moment.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->
</div>
<!-- /wp:group -->
