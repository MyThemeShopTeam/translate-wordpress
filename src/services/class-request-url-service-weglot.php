<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Util\Url;
use Weglot\Util\Server;

use WeglotWP\Models\Mediator_Service_Interface_Weglot;

/**
 * Request URL
 *
 * @since 2.0
 */
class Request_Url_Service_Weglot implements Mediator_Service_Interface_Weglot {
	/**
	 * @since 2.0
	 *
	 * @var string
	 */
	protected $weglot_url = null;

	/**
	 * @since 2.0
	 * @see Mediator_Service_Interface_Weglot
	 * @param array $services
	 * @return void
	 */
	public function use_services( $services ) {
		$this->option_services = $services['Option_Service_Weglot'];
	}

	/**
	 * Use for abstract \Weglot\Util\Url
	 *
	 * @param string $url
	 * @return Weglot\Util\Url
	 */
	public function create_url_object( $url ) {
		return new Url(
			$url,
			$this->option_services->get_option( 'original_language' ),
			$this->option_services->get_option( 'destination_language' ),
			$this->get_home_wordpress_directory()
		);
	}

	/**
	 * @since 2.0
	 *
	 * @return string
	 */
	public function init_weglot_url() {
		$exclude_urls_option = $this->option_services->get_option( 'exclude_urls' );

		$this->weglot_url = new Url(
			$this->get_full_url(),
			$this->option_services->get_option( 'original_language' ),
			$this->option_services->get_option( 'destination_language' ),
			$this->get_home_wordpress_directory()
		);

		$exclude_urls_option[] = '#wpadminbar';

		$this->weglot_url->setExcludedUrls( $exclude_urls_option );

		return $this;
	}

	/**
	 * Get request URL in process
	 * @since 2.0
	 * @return \Weglot\Util\Url
	 */
	public function get_weglot_url() {
		if ( null === $this->weglot_url ) {
			$this->init_weglot_url();
		}

		return $this->weglot_url;
	}

	/**
	 * Abstraction of \Weglot\Util\Url
	 * @since 2.0
	 * @return string
	 */
	public function get_current_language() {
		return $this->get_weglot_url()->detectCurrentLanguage();
	}

	/**
	 * Abstraction of \Weglot\Util\Url
	 * @since 2.0
	 *
	 * @return boolean
	 */
	public function is_translatable_url() {
		return $this->get_weglot_url()->isTranslable();
	}


	/**
	 * @since 2.0
	 *
	 * @return string
	 * @param mixed $use_forwarded_host
	 */
	public function get_full_url( $use_forwarded_host = false ) {
		return Server::fullUrl($_SERVER, $use_forwarded_host); //phpcs:ignore
	}


	/**
	 * @todo : Change this when weglot-php included
	 *
	 * @param string $code
	 * @return boolean
	 */
	public function is_language_rtl( $code ) {
		$rtls = [ 'ar', 'he', 'fa' ];
		if ( in_array( $code, $rtls, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @since 2.0
	 *
	 * @return string|null
	 */
	public function get_home_wordpress_directory() {
		$opt_siteurl   = trim( get_option( 'siteurl' ), '/' );
		$opt_home      = trim( get_option( 'home' ), '/' );
		if ( empty( $opt_siteurl ) || empty( $opt_home ) ) {
			return null;
		}

		if (
			( substr( $opt_home, 0, 7 ) === 'http://' && strpos( substr( $opt_home, 7 ), '/' ) !== false) || ( substr( $opt_home, 0, 8 ) === 'https://' && strpos( substr( $opt_home, 8 ), '/' ) !== false ) ) {
			$parsed_url = parse_url( $opt_home ); // phpcs:ignore
			$path       = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '/';
			return $path;
		}

		return null;
	}


	/**
	 * Is eligible URL
	 *
	 * @param string $url
	 * @return boolean
	 */
	public function is_eligible_url( $url ) {
		$url = urldecode( $this->url_to_relative( $url ) );

		//Format exclude URL
		$exclude_urls_option = weglot_get_exclude_urls();

		if ( ! empty( $exclude_urls_option ) ) {
			$exclude_urls_option    = preg_replace( '#\s+#', ',', trim( $exclude_urls_option ) );

			$excluded_urls  = explode( ',', $exclude_urls_option );
			foreach ( $excluded_urls as $ex_url ) {
				$ex_url = $this->url_to_relative( $ex_url );
			}
			$exclude_urls_option = implode( ',', $excluded_urls );
		}

		$exclusions = preg_replace( '#\s+#', ',', $exclude_urls_option );

		$list_regex = [];
		if ( ! empty( $exclusions ) ) {
			$list_regex  = explode( ',', $exclusions );
		}

		$exclude_amp = weglot_get_exclude_amp_translation();
		if ( $exclude_amp ) {
			$list_regex[] = apply_filters( 'weglot_regex_amp', '([&\?/])amp(/)?$' );
		}

		foreach ( $list_regex as $regex ) {
			$str           = $this->escape_slash( $regex );
			$prepare_regex = sprintf( '/%s/', $str );
			if ( preg_match( $prepare_regex, $url ) === 1 ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @since 2.0
	 *
	 * @param string $str
	 * @return string
	 */
	public function escape_slash( $str ) {
		return str_replace( '/', '\/', $str );
	}


	/**
	 * @since 2.0
	 *
	 * @param string $url
	 * @return string
	 */
	public function url_to_relative( $url ) {
		if ( ( substr( $url, 0, 7 ) === 'http://' ) || ( substr( $url, 0, 8 ) === 'https://' ) ) {
			// the current link is an "absolute" URL - parse it to get just the path
			$parsed   = wp_parse_url( $url );
			$path     = isset( $parsed['path'] ) ? $parsed['path'] : '';
			$query    = isset( $parsed['query'] ) ? '?' . $parsed['query'] : '';
			$fragment = isset( $parsed['fragment'] ) ? '#' . $parsed['fragment'] : '';

			if ( $this->get_home_wordpress_directory() ) {
				$relative = str_replace( $this->get_home_wordpress_directory(), '', $path );

				return ( empty( $relative ) ) ? '/' : $relative;
			} else {
				return $path . $query . $fragment;
			}
		}
		return $url;
	}
}


