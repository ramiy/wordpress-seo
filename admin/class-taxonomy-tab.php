<?php
/**
 * @package WPSEO\Admin
 */

/**
 * Class WPSEO_Taxonomy_Tab
 *
 * Contains the basics for each class extending this one.
 */
abstract class WPSEO_Taxonomy_Tab {

	/**
	 * The Yoast SEO configuration from the WPSEO_Options
	 *
	 * @var array
	 */
	protected $options = array();

	/**
	 * The current term data
	 *
	 * @var stdClass
	 */
	protected $term;

	/**
	 * Setting the class properties
	 *
	 * @param stdClass $term
	 */
	public function __construct( $term ) {
		$this->term     = $term;
		$this->options  = WPSEO_Options::get_all();
	}

	/**
	 * This method should return the fields
	 *
	 * @return array
	 */
	abstract public function get_fields();

	/**
	 * Returns array with the field data
	 *
	 * @param string       $label       The label displayed before the field.
	 * @param string       $description Description which will explain the field.
	 * @param string       $type        The field type, for example: input, select.
	 * @param string|array $options		Optional array with additional attributes for the field.
	 * @param bool         $hide		Should the field be hidden?
	 *
	 * @return array
	 */
	protected function get_field_config( $label, $description, $type = 'text', $options = '', $hide = false ) {
		return array(
			'label'       => $label,
			'description' => $description,
			'type'        => $type,
			'options'     => $options,
			'hide'        => $hide
		);
	}

	/**
	 * Filter the hidden fields.
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	protected function filter_hidden_fields( array $fields ) {
		foreach ( $fields as $field_name => $field_options ) {
			if ( ! empty( $field_options['hide'] ) ) {
				unset( $fields[ $field_name ] );
			}
		}

		return $fields;
	}

}