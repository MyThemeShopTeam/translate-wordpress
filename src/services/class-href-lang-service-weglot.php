<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.3.0
 */
class Href_Lang_Service_Weglot {
	protected $languages = null;

	/**
	 * @since 2.3.0
	 */
	public function __construct() {
		$this->custom_url_services      = weglot_get_service( 'Custom_Url_Service_Weglot' );
		$this->request_url_services     = weglot_get_service( 'Request_Url_Service_Weglot' );
	}

	/**
	 * @since 2.3.0
	 */
	public function generate_href_lang_tags() {
		$destination_languages = weglot_get_all_languages_configured();
		$render                = '';
		try {
			foreach ( $destination_languages as $language ) {
				$url = $this->custom_url_services->get_link( $language );
				$render .= '<link rel="alternate" href="' . $url . '" hreflang="' . $language . '"/>' . "\n";
			}
		} catch ( \Exception $e ) {
			$render = $this->request_url_services->get_weglot_url()->generateHrefLangsTags();
		}

		return apply_filters( 'weglot_href_lang', $render );
	}
}
