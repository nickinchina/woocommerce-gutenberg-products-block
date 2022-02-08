<?php
namespace Automattic\WooCommerce\Blocks\StoreApi\Schemas;

/**
 * TermSchema class.
 *
 * @internal This API is used internally by Blocks--it is still in flux and may be subject to revisions.
 * @since 2.5.0
 */
class UserSchema extends AbstractSchema {
	/**
	 * The schema item name.
	 *
	 * @var string
	 */
	protected $title = 'user';

	/**
	 * The schema item identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'user';

	/**
	 * Term properties.
	 *
	 * @return array
	 */
	public function get_properties() {
		return [
			'ID'          => array(
				'description' => __( 'Unique identifier for the resource.', 'woo-gutenberg-products-block' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'user_email'        => array(
				'description' => __( 'nick name.', 'woo-gutenberg-products-block' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'user_nicename'        => array(
				'description' => __( 'nick name.', 'woo-gutenberg-products-block' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'first_name'        => array(
				'description' => __( 'first Name.', 'woo-gutenberg-products-block' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'last_name' => array(
				'description' => __( 'last Name.', 'woo-gutenberg-products-block' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'display_name'        => array(
				'description' => __( 'display Name.', 'woo-gutenberg-products-block' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
		];
	}

	/**
	 * Convert a term object into an object suitable for the response.
	 *
	 * @param \WP_Term $term Term object.
	 * @return array
	 */
	public function get_item_response( $user ) {
		return [
			'id'          => (int) $$user["ID"],
			'email'        => $user["user_email"],
			'nicename'        => $user["user_nicename"],
			'firstName'        => $user["first_name"],
			'lastName'        => $user["last_name"],
			'displayName'        => $user["display_name"],
			'nonce'        => wp_create_nonce("wp-json"),
		];
	}
}
