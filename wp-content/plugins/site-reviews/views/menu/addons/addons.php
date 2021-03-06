<?php defined( 'WPINC' ) || die; ?>

<p><?= __( 'The following Add-Ons extend the functionality of Site Reviews.', 'site-reviews' ); ?></p>

<div class="glsr-addons wp-clearfix">

<?php

	$html->renderPartial( 'addon', [
		'name'        => 'tripadvisor',
		'title'       => 'Coming soon...',
		'description' => __( 'Sync your Tripadvisor business reviews with an optional minimum rating and display them on your site.', 'site-reviews' ),
		'link'        => '',
	]);

	$html->renderPartial( 'addon', [
		'name'        => 'yelp',
		'title'       => 'Coming soon...',
		'description' => __( 'Sync your Yelp business reviews with an optional minimum rating and display them on your site.', 'site-reviews' ),
		'link'        => '',
	]);

?>

</div>
