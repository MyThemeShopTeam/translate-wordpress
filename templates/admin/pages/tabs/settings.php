<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Client\Endpoint\Languages;
use Weglot\Client\Client;

$options_available = [
	'api_key' => [
		'key'         => 'api_key',
		'label'       => __( 'Api key', 'weglot' ),
		'description' => '',
	],
	'original_language' => [
		'key'         => 'original_language',
		'label'       => __( 'Original language', 'weglot' ),
		'description' => '',
	],
	'destination_language' => [
		'key'         => 'destination_language',
		'label'       => __( 'Destination language', 'weglot' ),
		'description' => '',
	],
	'exclude_url' => [
		'key'         => 'exclude_url',
		'label'       => __( 'Exclusion URL', 'weglot' ),
		'description' => '',
	],
];

$client    = new Client( $this->option_services->get_option( 'api_key' ) );
$languages = new Languages( $client );

?>

<h2><?php esc_html_e( 'Settings', 'weglot' ); ?></h2>

<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['api_key']['key'] ); ?>">
					<?php echo esc_html( $options_available['api_key']['label'] ); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<input
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['api_key']['key'] ) ); ?>"
					id="<?php echo esc_attr( $options_available['api_key']['key'] ); ?>"
					type="text"
					value="<?php echo esc_attr( $this->options[ $options_available['api_key']['key'] ] ); ?>"
				>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['original_language']['key'] ); ?>">
					<?php echo esc_html( $options_available['original_language']['label'] ); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<select
					class="weglot-select"
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['original_language']['key'] ) ); ?>"
					id="<?php echo esc_attr( $options_available['original_language']['key'] ); ?>"
				>
					<?php foreach ( $languages->handle() as $language ) : ?>
						<option
							value="<?php echo esc_attr( $language->getIso639() ); ?>"
							<?php selected( $language->getIso639(), $this->options[ $options_available['original_language']['key'] ] ); ?>
						>
							<?php echo esc_html( $language->getLocalName() ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['destination_language']['key'] ); ?>">
					<?php echo esc_html( $options_available['destination_language']['label'] ); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<select
					class="weglot-select"
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['destination_language']['key'] ) ); ?>[]"
					id="<?php echo esc_attr( $options_available['destination_language']['key'] ); ?>"
					multiple="true"
				>
					<?php foreach ( $languages->handle() as $language ) : ?>
						<option
							value="<?php echo esc_attr( $language->getIso639() ); ?>"
							<?php selected( true, in_array( $language->getIso639(), $this->options[ $options_available['destination_language']['key'] ], true ) ); ?>
						>
							<?php echo esc_html( $language->getLocalName() ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
	</tbody>
</table>

<h3><?php esc_html_e( 'Exclusion translation', 'weglot' ); ?> </h3>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['api_key']['key'] ); ?>">
					<?php echo esc_html( $options_available['api_key']['label'] ); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<select
					class="weglot-select-exclusion"
					multiple="multiple"
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['exclude_url']['key'] ) ); ?>[]"
					id="<?php echo esc_attr( $options_available['exclude_url']['key'] ); ?>"
					multiple="true"
				>
					<?php
					if ( ! empty( $this->options[ $options_available['exclude_url']['key'] ] ) ) :
						foreach ( $this->options[ $options_available['exclude_url']['key'] ] as $option ) :
					?>
						<option selected="selected"><?php echo esc_attr( $option ); ?></option>
					<?php
						endforeach;
					endif;
					?>
				</select>
			</td>
		</tr>
	</tbody>
</table>

<?php settings_fields( WEGLOT_OPTION_GROUP ); ?>
