<?php

namespace WeglotWP\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Schema_Option_V3 {

	/**
	 * @since 3.0.0
	 * @return array
	 */
	public static function get_schema_switch_option_to_v3() {
		return $schema = [
			'api_key'           => 'api_key',
			'original_language' => (object) [
				'path' => 'language_from',
				'fn'   => function($language_from) {
					return $language_from['code'];
				}
			],
			'destination_language' => (object) [
				'path' => 'language_to',
				'fn'   => function($language_to) {
					$languages = [];
					foreach ($language_to as $item) {
						$languages[] = $item['language']['code'];
					}
					return $languages;
				}
			],
			'translate_amp'               => 'translate_amp',
			'translate_email'             => 'translate_email',
			'autoswitch'                  => 'autoswitch',
			'autoswitch_fallback'         => 'autoswitch_fallback',
			'excluded_paths'              => (object) [
				'path' => 'excluded_paths',
				'fn'   => function($excluded_paths) {
					$excluded = [];
					foreach ($excluded_paths as $item) {
						$excluded[] = $item['value'];
					}
					return $excluded;
				}
			],
			'excluded_blocks'              => (object) [
				'path' => 'excluded_blocks',
				'fn'   => function($excluded_blocks) {
					$excluded = [];
					foreach ($excluded_blocks as $item) {
						$excluded[] = $item['value'];
					}
					return $excluded;
				}
			]
		];
	}
}
