<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Option services
 *
 * @since 2.0
 */
class Option_Service_Weglot {

	/**
	 * @var array
	 */
	protected $options_default = [
		'api_key'                    => '',
		'original_language'          => 'en',
		'destination_language'       => [],
		'translate_amp'              => false,
		'version'                    => null,
		'exclude_blocks'             => [],
		'exclude_urls'               => [],
		'auto_redirect'              => false,
		'email_translate'            => false,
		'is_fullname'                => false,
		'with_name'                  => true,
		'is_dropdown'                => true,
		'type_flags'                 => 0,
		'with_flags'                 => true,
		'override_css'               => '',
		'has_first_settings'         => true,
		'show_box_first_settings'    => false,
	];

	/**
	 * Get options default
	 *
	 * @since 2.0
	 * @return array
	 */
	public function get_options_default() {
		return $this->options_default;
	}

	/**
	 * @since 2.0
	 * @return array
	 */
	public function get_options() {
		return wp_parse_args( get_option( WEGLOT_SLUG ), $this->get_options_default() );
	}

	/**
	 * @since 2.0
	 * @param string $name
	 * @return array
	 */
	public function get_option( $name ) {
		$options = $this->get_options();
		if ( ! array_key_exists( $name, $options ) ) {
			return null; // @TODO : throw exception
		}

		return $options[ $name ];
	}

	/**
	 * @since 2.0
	 * @return array
	 */
	public function get_exclude_blocks() {
		$exclude_blocks     = $this->get_option( 'exclude_blocks' );
		$exclude_blocks[]   = '#wpadminbar';
		$exclude_blocks[]   = '#query-monitor';

		return $exclude_blocks;
		;
	}


	/**
	 * @since 2.0
	 * @param array $options
	 * @return Option_Service_Weglot
	 */
	public function set_options( $options ) {
		update_option( WEGLOT_SLUG, $options );
		return $this;
	}

	/**
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return Option_Service_Weglot
	 */
	public function set_option_by_key( $key, $value ) {
		$options         = $this->get_options();
		$options[ $key ] = $value;
		$this->set_options( $options );
		return $this;
	}
}
