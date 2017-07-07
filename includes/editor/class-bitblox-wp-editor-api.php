<?php

class BitBlox_WP_Editor_API {

	const nonce = 'bitblox-wp-api';
	const AJAX_PING = 'bitblox_wp_editor_ping';
	const AJAX_GET = 'bitblox_wp_editor_get_items';
	const AJAX_UPDATE = 'bitblox_wp_update_item';
	const AJAX_GET_GLOBALS = 'bitblox_wp_get_globals';
	const AJAX_SET_GLOBALS = 'bitblox_wp_set_globals';
	const AJAX_MEDIA = 'bitblox_wp_media';
	const AJAX_SIDEBARS = 'bitblox_wp_sidebars';
	const AJAX_BUILD = 'bitblox_wp_build';

	public static function init() {
		static $instance;

		if ( ! $instance ) {
			$instance = new self();
		}
	}

	protected function __construct() {
		add_action( 'wp_ajax_' . self::AJAX_PING, array( $this, 'ping' ) );
		add_action( 'wp_ajax_' . self::AJAX_GET, array( $this, 'get_item' ) );
		add_action( 'wp_ajax_' . self::AJAX_UPDATE, array( $this, 'update_item' ) );
		add_action( 'wp_ajax_' . self::AJAX_GET_GLOBALS, array( $this, 'get_globals' ) );
		add_action( 'wp_ajax_' . self::AJAX_SET_GLOBALS, array( $this, 'set_globals' ) );
		add_action( 'wp_ajax_' . self::AJAX_MEDIA, array( $this, 'media' ) );
		add_action( 'wp_ajax_' . self::AJAX_SIDEBARS, array( $this, 'get_sidebars' ) );
		add_action( 'wp_ajax_' . self::AJAX_BUILD, array( $this, 'build_content' ) );
	}

	/**
	 * @internal
	 **/
	public function ping() {
		try {
			$this->authorize();
			$this->success( array() );
		} catch ( Exception $exception ) {
			$this->error( $exception->getCode(), $exception->getMessage() );
			exit;
		}
	}

	/**
	 * @internal
	 **/
	public function get_globals() {
		try {
			$this->authorize();
			$id = $this->param( 'id' );
			$this->success( self::create_post_globals( BitBlox_WP_Post::get( $id ) ) );
		} catch ( Exception $exception ) {
			$this->error( $exception->getCode(), $exception->getMessage() );
			exit;
		}
	}

	/**
	 * @internal
	 */
	public function set_globals() {
		try {
			$this->authorize();
			$post = BitBlox_WP_Post::get( $this->param( 'id' ) );
			$data = $this->param( 'data' );

			$post->set_globals( stripslashes( $data['globals'] ) );
			$this->success( self::create_post_globals( $post ) );
		} catch ( Exception $exception ) {
			$this->error( $exception->getCode(), $exception->getMessage() );
			exit;
		}
	}

	/**
	 * @internal
	 **/
	public function get_item() {
		try {
			$this->authorize();
			$id   = $this->param( 'id' );
			$post = BitBlox_WP_Post::get( $id );

			$this->success( array( self::create_post_arr( $post ) ) );
		} catch ( Exception $exception ) {
			$this->error( $exception->getCode(), $exception->getMessage() );
			exit;
		}
	}

	/**
	 * @internal
	 **/
	public function update_item() {
		try {
			$id      = $this->param( 'id' );
			$content = $this->param( 'data' );

			$post = BitBlox_WP_Post::get( $id );

			try {
				$post->set_title( $this->param( 'title' ) );
			} catch ( Exception $exception ) {

			}

			try {
				$post->set_template( $this->param( 'template' ) );
			} catch ( Exception $exception ) {

			}

			$post->set_draft( stripslashes( $content ) );

			$this->success( self::create_post_arr( $post ) );
		} catch ( Exception $exception ) {
			$this->error( $exception->getCode(), $exception->getMessage() );
		}
	}

	/**
	 * @internal
	 */
	public function build_content() {
		try {
			$id   = $this->param( 'id' );
			$post = BitBlox_WP_Post::get( $id );
			$post->update_html();

			$this->success( self::create_post_arr( $post ) );
		} catch ( Exception $exception ) {
			$this->error( $exception->getCode(), $exception->getMessage() );
		}
	}

	public function get_sidebars() {
		global $wp_registered_sidebars;

		$items = array();

		foreach ( $wp_registered_sidebars as $sidebar ) {
			$item    = array(
				'id'    => $sidebar['id'],
				'title' => $sidebar['name'],
			);
			$items[] = $item;
		}

		$this->success( $items );
	}

	/**
	 * @internal
	 **/
	public function media() {
		try {
			$this->authorize();
			$project       = BitBlox_WP_Post::get( $this->param( 'id' ) );
			$attachment_id = $this->param( 'attachmentId' );

			$this->success( BitBlox_WP_User::get()->get_media_id(
				new BitBlox_WP_API_Project(
					$project->get_id(),
					$project->get_page_id()
				),
				$attachment_id
			) );
		} catch ( Exception $exception ) {
			$this->error( $exception->getCode(), $exception->getMessage() );
		}
	}

	protected function param( $name ) {
		if ( isset( $_POST[ $name ] ) ) {
			return $_POST[ $name ];
		}

		throw new BitBlox_WP_Exception_Not_Found( "Parameter '$name' is missing", 400 );
	}

	protected function error( $code, $message ) {
		wp_send_json_error( array( 'code' => $code, 'message' => $message ), $code );
	}

	protected function success( $data ) {
		wp_send_json( $data );
	}

	protected function static_url() {
		return bitblox_wp()->get_url( '/includes/editor/static' );
	}

	private function authorize() {
		if ( ! wp_verify_nonce( $_POST['hash'], self::nonce ) ) {
			throw new BitBlox_WP_Exception_Access_Denied();
		}
	}

	public static function create_post_arr( BitBlox_WP_Post $post ) {
		return array(
			'title'    => get_the_title( $post->ID() ),
			'slug'     => sanitize_title( get_the_title( $post->ID() ) ),
			'data'     => $post->get_draft(),
			'id'       => $post->ID(),
			'is_index' => true,
			'template' => get_page_template_slug( $post->ID() ),
			'status'   => get_post_status( $post->ID() ),
			'url'      => get_the_permalink( $post->ID() )
		);
	}

	public static function create_post_globals( BitBlox_WP_Post $post ) {
		return array(
			'createdAt' => $post->get_wp_post()->post_date,
			'updatedAt' => $post->get_wp_post()->post_date,
			'id'        => $post->ID(),
			'name'      => $post->get_wp_post()->post_name,
			'globals'   => $post->get_globals(),
			'user'      => array(
				'email' => null,
				'id'    => null,
			),
		);
	}
}