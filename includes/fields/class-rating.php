<?php
/**
 * Rating field.
 *
 * @package HivePress\Fields
 */

namespace HivePress\Fields;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Rating field class.
 *
 * @class Rating
 */
class Rating extends Number {

	/**
	 * Class initializer.
	 *
	 * @param array $meta Field meta.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				'label'      => null,
				'editable'   => false,
				'filterable' => false,
			],
			$meta
		);

		parent::init( $meta );
	}

	/**
	 * Class constructor.
	 *
	 * @param array $args Field arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			$args,
			[
				'decimals'  => 1,
				'min_value' => 1,
				'max_value' => 5,
			]
		);

		parent::__construct( $args );
	}

	/**
	 * Bootstraps field properties.
	 */
	protected function boot() {

		// Set attributes.
		$this->attributes = hp\merge_arrays(
			$this->attributes,
			[
				'class'          => [ 'hp-rating-stars', 'hp-rating-stars--large' ],
				'data-component' => 'rating',
				'data-name'      => $this->name,
				'data-value'     => $this->value,
			]
		);

		Field::boot();
	}

	/**
	 * Renders field HTML.
	 *
	 * @return string
	 */
	public function render() {
		return '<div ' . hp\html_attributes( $this->attributes ) . '></div>';
	}
}
