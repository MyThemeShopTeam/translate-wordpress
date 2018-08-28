<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


use Weglot\Client\Endpoint\Languages;
use Weglot\Client\Client;
use Weglot\Client\Api\LanguageCollection;
use Weglot\Client\Factory\Languages as LanguagesFactory;

/**
 * Language service
 *
 * @since 2.0
 */
class Language_Service_Weglot {
	protected $languages = null;

	/**
	 * @since 2.0
	 */
	public function __construct() {
		$this->option_services = weglot_get_service( 'Option_Service_Weglot' );
	}

	/**
	 * @since 2.0.6
	 * @param array $a
	 * @param array $b
	 * @return bool
	 */
	protected function compare_language( $a, $b ) {
		return strcmp( $a['local'], $b['local'] );
	}

	/**
	 * Get languages available from API
	 * @since 2.0
	 * @version 2.0.6
	 * @param array $params
	 * @return array
	 */
	public function get_languages_available( $params = [] ) {
		if ( null !== $this->languages ) {
			return $this->languages;
		}

		$client           = new Client( $this->option_services->get_option( 'api_key' ) );
		$languages        = new Languages( $client );

		$this->languages  = $languages->handle();

		if ( isset( $params['sort'] ) && $params['sort'] ) {
			$this->languages = $this->languages->jsonSerialize();
			usort( $this->languages, [ $this, 'compare_language' ] );

			$language_collection = new LanguageCollection();

			foreach ( $this->languages as $language ) {
				$factory = new LanguagesFactory( $language );
				$language_collection->addOne( $factory->handle() );
			}

			$this->languages = $language_collection;
		}

		return $this->languages;
	}

	/**
	 * Get language entry
	 * @since 2.0
	 * @param string $key_code
	 * @return array
	 */
	public function get_language( $key_code ) {
		return $this->get_languages_available()[ $key_code ];
	}

	/**
	 * @since 2.0
	 * @return array
	 * @param null|string $type
	 */
	public function get_languages_configured( $type = null ) {
		$languages[]      = weglot_get_original_language();
		$languages        = array_merge( $languages, weglot_get_destination_languages() );

		$languages_object = [];

		foreach ( $languages as $language ) {
			switch ( $type ) {
				case 'code':
					$languages_object[] = $this->get_language( $language )->getIso639();
					break;
				default:
					$languages_object[] = $this->get_language( $language );
					break;
			}
		}

		return $languages_object;
	}
}
