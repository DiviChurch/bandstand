<?php
/**
 * Videos module.
 *
 * @package   Bandstand\Videos
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

namespace Bandstand\Module;

use Bandstand\AJAX;
use Bandstand\PostType;
use Bandstand\REST;
use Bandstand\Screen;
use Bandstand\Taxonomy;

/**
 * Videos module class.
 *
 * @package Bandstand\Videos
 * @since   1.0.0
 */
class VideosModule extends AbstractModule {
	/**
	 * Admin menu item HTML id.
	 *
	 * Used for hiding menu items when toggling modules.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $admin_menu_id = 'menu-posts-bandstand_video';

	/**
	 * Module id.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $id = 'videos';

	/**
	 * Whether the module should show on the dashboard.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $show_in_dashboard = true;

	/**
	 * Retrieve the name of the module.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_name() {
		return esc_html__( 'Videos', 'bandstand' );
	}

	/**
	 * Retrieve the module description.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_description() {
		return esc_html__( 'Embed videos from services like YouTube and Vimeo to create your own video library.', 'bandstand' );
	}

	/**
	 * Load the module.
	 *
	 * @since 1.0.0
	 *
	 * @return $this
	 */
	public function load() {
		require( $this->plugin->get_path( 'includes/video-template.php' ) );
		return $this;
	}

	/**
	 * Register module hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		$this->plugin->register_hooks( new Taxonomy\VideoCategory( $this ) );
		$this->plugin->register_hooks( new PostType\Video( $this ) );
		$this->plugin->register_hooks( new AJAX\Videos() );

		add_action( 'init',                              array( $this, 'register_archive' ), 20 );
		add_filter( 'bandstand_archive_settings_fields', array( $this, 'register_archive_settings_fields' ), 10, 2 );
		add_action( 'rest_api_init',                     array( $this, 'register_rest_routes' ) );
		add_action( 'template_include',                  array( $this, 'template_include' ) );

		if ( is_admin() ) {
			$this->plugin->register_hooks( new Screen\ManageVideos() );
			$this->plugin->register_hooks( new Screen\EditVideo() );
			$this->plugin->register_hooks( new Screen\EditVideoArchive() );
		}
	}

	/**
	 * Register the discography archive.
	 *
	 * @since 1.0.0
	 */
	public function register_archive() {
		$this->plugin->modules['archives']->add_post_type_archive( 'bandstand_video' );
	}

	/**
	 * Activate default archive setting fields.
	 *
	 * @since 1.0.0
	 *
	 * @param  array  $fields    List of default fields to activate.
	 * @param  string $post_type Post type archive.
	 * @return array
	 */
	public function register_archive_settings_fields( $fields, $post_type ) {
		if ( ! in_array( $post_type, array( 'bandstand_video' ), true ) ) {
			return $fields;
		}

		$fields['columns'] = array(
			'choices' => range( 1, 4 ),
			'default' => 3,
		);

		$fields['order'] = true;
		$fields['posts_per_archive_page'] = true;

		return $fields;
	}

	/**
	 * Register REST routes.
	 *
	 * @since 1.0.0
	 */
	public function register_rest_routes() {
		$controller = new REST\VideosController( 'bandstand/v1', 'videos', 'bandstand_video' );
		$controller->register_routes();
	}

	/**
	 * Get the videos rewrite base. Defaults to 'videos'.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_rewrite_base() {
		global $wp_rewrite;

		$front = '';
		$base  = get_option( 'bandstand_video_rewrite_base', 'videos' );

		if ( $wp_rewrite->using_index_permalinks() ) {
			$front = $wp_rewrite->index . '/';
		}

		return $front . $base;
	}

	/**
	 * Display a button to perform the module's primary action.
	 *
	 * @since 1.0.0
	 */
	public function display_primary_button() {
		printf(
			'<a href="%s" class="button">%s</a>',
			esc_url( admin_url( 'post-new.php?post_type=bandstand_video' ) ),
			esc_html__( 'Add Video', 'bandstand' )
		);
	}

	/**
	 * Load video templates.
	 *
	 * Templates should be included in an /bandstand/ directory within the theme.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Template path.
	 * @return string
	 */
	public function template_include( $template ) {
		$manager = $this->plugin->templates;

		if ( is_post_type_archive( 'bandstand_video' ) || is_tax( 'bandstand_video_category' ) ) {
			if ( is_tax() ) {
				$term = get_queried_object();
				$taxonomy = str_replace( 'bandstand_', '', $term->taxonomy );
				$templates[] = "taxonomy-$taxonomy-{$term->slug}.php";
				$templates[] = "taxonomy-$taxonomy.php";
			}

			$templates[] = 'archive-video.php';

			$manager->set_title( $this->plugin->modules['archives']->get_archive_title() );
			$manager->set_loop_template_part( 'video/loop', 'archive' );
		} elseif ( is_singular( 'bandstand_video' ) ) {
			$templates[] = 'single-video.php';

			$manager->set_title( get_queried_object()->post_title );
			$manager->set_loop_template_part( 'video/loop', 'single' );
		}

		if ( ! empty( $templates ) ) {
			$template = $manager->locate_template( $templates );
			do_action( 'bandstand_template_include', $template );
		}

		return $template;
	}
}
