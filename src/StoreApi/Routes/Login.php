<?php
namespace Automattic\WooCommerce\Blocks\StoreApi\Routes;

/**
 * Cart class.
 *
 * @internal This API is used internally by Blocks--it is still in flux and may be subject to revisions.
 */
class Login extends AbstractRoute {
	/**
	 * Get the path of this REST route.
	 *
	 * @return string
	 */
	public function get_path() {
		return '/login';
	}

	/**
	 * Get method arguments for this REST route.
	 *
	 * @return array An array of endpoints.
	 */
	public function get_args() {
		return [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'current_user' ],
				'permission_callback' => '__return_true',
				'args'                => [
					'context' => $this->get_context_param( [ 'default' => 'view' ] ),
				],
			],
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'get_response' ],
				'permission_callback' => '__return_true',
				'args'                => [
					'username'      => [
						'description' => __( 'username.', 'woo-gutenberg-products-block' ),
						'type'        => 'string',
					],
					'password'      => [
						'description' => __( 'password.', 'woo-gutenberg-products-block' ),
						'type'        => 'string',
					],
					'username'      => [
						'custom_auth' => __( 'custom_auth.', 'woo-gutenberg-products-block' ),
						'type'        => 'string',
					],
				],
			],
			'schema' => [ $this->schema, 'get_public_item_schema' ],
		];
	}

	/**
	 * Authenticate user either via wp_authenticate or custom auth (e.g: OTP).
	 *
	 * @param string $username The username.
	 * @param string $password The password.
	 * @param mixed  $custom_auth The custom auth data (if any).
	 *
	 * @return WP_User|WP_Error $user Returns WP_User object if success, or WP_Error if failed.
	 */
	public function authenticate_user( $username, $password, $custom_auth = '' ) {
		// If using custom authentication.
		if ( $custom_auth ) {
			$custom_auth_error = new WP_Error( 'jwt_auth_custom_auth_failed', __( 'Custom authentication failed.', 'jwt-auth' ) );

			/**
			 * Do your own custom authentication and return the result through this filter.
			 * It should return either WP_User or WP_Error.
			 */
			$user = apply_filters( 'jwt_auth_do_custom_auth', $custom_auth_error, $username, $password, $custom_auth );
		} else {
			$user = wp_authenticate( $username, $password );
		}

		return $user;
	}

	public function current_user() {
		$user = array(
			'id'    => get_current_user_id(),
		);
		return rest_ensure_response( $this->schema->get_item_response( $user ) );
	}
	/**
	 * Handle the request and return a valid response for this endpoint.
	 *
	 * @throws RouteException On error.
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	protected function get_route_post_response( \WP_REST_Request $request ) {
		$user = $this->authenticate_user( $request['username'], $request['password'], $request['custom_auth'] );
		$user_item = array(
			'ID'          => $user->ID,
			'user_email'       => $user->user_email,
			'user_nicename'    => $user->user_nicename,
			'first_name'   => $user->first_name,
			'last_name'    => $user->last_name,
			'display_name' => $user->display_name
		);
		error_log(print_r($user_item, TRUE)); 
		return rest_ensure_response( $this->schema->get_item_response( $user_item ) );
	}
}
