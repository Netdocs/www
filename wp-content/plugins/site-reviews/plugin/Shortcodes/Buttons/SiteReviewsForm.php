<?php

/**
 * Site Reviews Form shortcode button
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv3
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Shortcodes\Buttons;

use GeminiLabs\SiteReviews\Shortcodes\Buttons\Generator;

class SiteReviewsForm extends Generator
{
	/**
	 * @return array
	 */
	public function fields()
	{
		$terms = glsr_resolve( 'Database' )->getTerms();

		if( !empty( $terms )) {
			$category = [
				'type'    => 'listbox',
				'name'    => 'category',
				'label'   => esc_html__( 'Category', 'site-reviews' ),
				'options' => $terms,
				'tooltip' => esc_attr__( 'Automatically assign a category to reviews submitted with this shortcode.', 'site-reviews' ),
			];
		}

		return [
			[
				'type' => 'container',
				'html' => sprintf( '<p class="strong">%s</p>', esc_html__( 'All settings are optional.', 'site-reviews' )),
			],[
				'type'    => 'textbox',
				'name'    => 'title',
				'label'   => esc_html__( 'Title', 'site-reviews' ),
				'tooltip' => esc_attr__( 'Enter a custom shortcode heading.', 'site-reviews' ),
			],[
				'type'    => 'textbox',
				'name'    => 'description',
				'label'   => esc_html__( 'Description', 'site-reviews' ),
				'tooltip' => esc_attr__( 'Enter a custom shortcode description.', 'site-reviews' ),
				'minWidth' => 240,
				'minHeight' => 60,
				'multiline' => true,
			],
			( isset( $category ) ? $category : [] ),
			[
				'type'      => 'textbox',
				'name'      => 'assign_to',
				'label'     => esc_html__( 'Post ID', 'site-reviews' ),
				'tooltip'   => esc_attr__( 'Assign submitted reviews to a custom page/post ID.', 'site-reviews' ),
			],[
				'type'     => 'textbox',
				'name'     => 'class',
				'label'    => esc_html__( 'Classes', 'site-reviews' ),
				'tooltip'  => esc_attr__( 'Add custom CSS classes to the shortcode.', 'site-reviews' ),
			],[
				'type'    => 'container',
				'label'   => esc_html__( 'Hide', 'site-reviews' ),
				'layout'  => 'grid',
				'columns' => 2,
				'spacing' => 5,
				'items'   => [
					[
						'type' => 'checkbox',
						'name' => 'hide_email',
						'text' => esc_html__( 'Email', 'site-reviews' ),
						'tooltip' => esc_attr__( 'Hide the email field?', 'site-reviews' ),
					],[
						'type' => 'checkbox',
						'name' => 'hide_name',
						'text' => esc_html__( 'Name', 'site-reviews' ),
						'tooltip' => esc_attr__( 'Hide the name field?', 'site-reviews' ),
					],[
						'type' => 'checkbox',
						'name' => 'hide_terms',
						'text' => esc_html__( 'Terms', 'site-reviews' ),
						'tooltip' => esc_attr__( 'Hide the terms field?', 'site-reviews' ),
					],[
						'type' => 'checkbox',
						'name' => 'hide_title',
						'text' => esc_html__( 'Title', 'site-reviews' ),
						'tooltip' => esc_attr__( 'Hide the title field?', 'site-reviews' ),
					],
				],
			],[
				'type'   => 'textbox',
				'name'   => 'id',
				'hidden' => true,
			],
		];
	}
}
