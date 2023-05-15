<?php

namespace WPCOMSpecialProjects\ExternalLinksInNewTab;

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
class Plugin {
	// region MAGIC METHODS

	/**
	 * Plugin constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	protected function __construct() {
		/* Empty on purpose. */
	}

	/**
	 * Prevent cloning.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	private function __clone() {
		/* Empty on purpose. */
	}

	/**
	 * Prevent unserializing.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function __wakeup() {
		/* Empty on purpose. */
	}

	// endregion

	// region METHODS

	/**
	 * Returns the singleton instance of the plugin.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  Plugin
	 */
	public static function get_instance(): self {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Initializes the plugin components.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function initialize(): void {
		\add_filter( 'the_content', array( $this, 'external_link_target_blank' ), 999 );
	}

	// endregion

	// region HOOKS

	/**
	 * Filters given content to add target="_blank" to external links.
	 *
	 * @see https://developer.wordpress.org/reference/files/wp-includes/html-api/class-wp-html-tag-processor.php/
	 * @see https://adamadam.blog/2023/02/16/how-to-modify-html-in-a-php-wordpress-plugin-using-the-new-tag-processor-api/
	 *
	 * @param string $content Content of the post.
	 *
	 * @return string
	 */
	public function external_link_target_blank( string $content ): string {

		// Instantiate the processor.
		$processor = new \WP_HTML_Tag_Processor( $content );

		// Get the domain of the site without scheme.
		$site_domain = wp_parse_url( site_url(), PHP_URL_HOST );

		// Loop through all the A tags and parse href to see if it's an external link.
		while ( $processor->next_tag( 'A' ) ) {
			$href        = $processor->get_attribute( 'href' );
			$root_domain = wp_parse_url( $href, PHP_URL_HOST );

			// If root domain is null, it's an internal link (no host), skip.
			if ( null === $root_domain ) {
				continue;
			}

			// If the root domain is not the same as the site domain, it's an external link.
			if ( $root_domain !== $site_domain ) {
				$processor->set_attribute( 'target', '_blank' );
				$processor->set_attribute( 'rel', 'nofollow external noopener noreferrer' );
			}
		}

		return $processor->get_updated_html();
	}

	// endregion
}
