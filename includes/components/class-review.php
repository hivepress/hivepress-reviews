<?php
namespace HivePress\Reviews;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Manages reviews.
 *
 * @class Review
 */
class Review extends \HivePress\Component {

	/**
	 * Class constructor.
	 *
	 * @param array $settings
	 */
	public function __construct( $settings ) {
		parent::__construct( $settings );

		// Manage rating field.
		add_filter( 'hivepress/form/field_value/rating', [ $this, 'sanitize_rating_field' ] );
		add_filter( 'hivepress/form/field_html/rating', [ $this, 'render_rating_field' ], 10, 4 );

		// Submit review.
		add_filter( 'hivepress/form/form_values/review__submit', [ $this, 'set_form_values' ] );
		add_action( 'hivepress/form/submit_form/review__submit', [ $this, 'submit' ] );

		// Set rating.
		add_action( 'wp_insert_post', [ $this, 'set_rating' ], 10, 3 );

		// Update rating.
		add_action( 'comment_post', [ $this, 'update_rating' ] );
		add_action( 'wp_set_comment_status', [ $this, 'update_rating' ] );
		add_action( 'delete_comment', [ $this, 'update_rating' ] );

		if ( ! is_admin() ) {

			// Add sorting options.
			add_filter( 'hivepress/form/form_fields/listing__sort', [ $this, 'add_sorting_options' ], 20 );

			// Set sorting query.
			add_action( 'pre_get_posts', [ $this, 'set_sorting_query' ] );

			// Set template context.
			add_filter( 'hivepress/template/template_context/single_listing', [ $this, 'set_template_context' ] );
		}
	}

	/**
	 * Sanitizes rating field.
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function sanitize_rating_field( $value ) {
		if ( '' !== $value ) {
			$value = absint( $value );

			if ( $value < 1 ) {
				$value = 1;
			} elseif ( $value > 5 ) {
				$value = 5;
			}
		}

		return $value;
	}

	/**
	 * Renders rating field.
	 *
	 * @param string $output
	 * @param string $id
	 * @param array  $args
	 * @param mixed  $value
	 * @return string
	 */
	public function render_rating_field( $output, $id, $args, $value ) {

		// Render field.
		$output = '<div class="hp-rating hp-js-rating" data-id="' . esc_attr( $id ) . '" data-value="' . esc_attr( $value ) . '"></div>';

		return $output;
	}

	/**
	 * Sets form values.
	 *
	 * @param array $values
	 * @return array
	 */
	public function set_form_values( $values ) {
		$values['post_id'] = get_the_ID();

		return $values;
	}

	/**
	 * Submits review.
	 *
	 * @param array $values
	 */
	public function submit( $values ) {

		// Get post ID.
		$post_id = hp_get_post_id(
			[
				'post__in'    => [ absint( $values['post_id'] ) ],
				'post_type'   => 'hp_listing',
				'post_status' => 'publish',
			]
		);

		if ( 0 !== $post_id && count( hivepress()->form->get_messages() ) === 0 ) {

			// Get review IDs.
			$review_ids = get_comments(
				[
					'type'    => 'comment',
					'user_id' => get_current_user_id(),
					'post_id' => $post_id,
					'fields'  => 'ids',
				]
			);

			if ( empty( $review_ids ) ) {

				// Add review.
				$review_id = wp_insert_comment(
					[
						'user_id'              => get_current_user_id(),
						'comment_author'       => hivepress()->user->get_name(),
						'comment_author_email' => hivepress()->user->get_email(),
						'comment_post_ID'      => $post_id,
						'comment_content'      => $values['review'],
						'comment_approved'     => 0,
					]
				);

				if ( false !== $review_id ) {

					// Set rating.
					update_comment_meta( $review_id, 'hp_rating', $values['rating'] );
				}
			} else {
				hivepress()->form->add_error( esc_html__( 'Your review is already submitted.', 'hivepress-reviews' ) );
			}
		}
	}

	/**
	 * Gets rating.
	 *
	 * @param array $args
	 * @return array
	 */
	private function get_rating( $args ) {

		// Get review IDs.
		$review_ids = get_comments(
			array_merge(
				[
					'post_type' => 'hp_listing',
					'type'      => 'comment',
					'status'    => 'approve',
					'fields'    => 'ids',
				],
				$args
			)
		);

		// Set defaults.
		$rating_count = count( $review_ids );
		$rating_value = 0;

		// Calculate rating.
		foreach ( $review_ids as $review_id ) {
			$rating = absint( get_comment_meta( $review_id, 'hp_rating', true ) );

			if ( $rating < 1 ) {
				$rating = 1;
			} elseif ( $rating > 5 ) {
				$rating = 5;
			}

			$rating_value += $rating;
		}

		if ( $rating_count > 0 ) {
			$rating_value = round( $rating_value / $rating_count, 1 );
		}

		return [
			'value' => $rating_value,
			'count' => $rating_count,
		];
	}

	/**
	 * Sets rating.
	 *
	 * @param int     $post_id
	 * @param WP_Post $post
	 * @param bool    $update
	 */
	public function set_rating( $post_id, $post, $update ) {
		if ( 'hp_listing' === $post->post_type && ! $update ) {
			add_post_meta( $post_id, 'hp_rating', '', true );
		}
	}

	/**
	 * Updates rating.
	 *
	 * @param int $review_id
	 */
	public function update_rating( $review_id ) {

		// Get review.
		$review = get_comment( $review_id );

		if ( ! is_null( $review ) && '' === $review->comment_type && 'hp_listing' === $review->post_type ) {

			// Update post rating.
			$post_rating = $this->get_rating( [ 'post_id' => $review->comment_post_ID ] );

			if ( $post_rating['count'] > 0 ) {
				update_post_meta( $review->comment_post_ID, 'hp_rating_count', $post_rating['count'] );
				update_post_meta( $review->comment_post_ID, 'hp_rating', $post_rating['value'] );
			} else {
				delete_post_meta( $review->comment_post_ID, 'hp_rating_count' );
				update_post_meta( $review->comment_post_ID, 'hp_rating', '' );
			}

			// Update vendor rating.
			$vendor_id     = get_post_field( 'post_author', $review->comment_post_ID );
			$vendor_rating = $this->get_rating( [ 'post_author' => $vendor_id ] );

			if ( $vendor_rating['count'] > 0 ) {
				update_user_meta( $vendor_id, 'hp_rating_count', $vendor_rating['count'] );
				update_user_meta( $vendor_id, 'hp_rating', $vendor_rating['value'] );
			} else {
				delete_user_meta( $vendor_id, 'hp_rating_count' );
				delete_user_meta( $vendor_id, 'hp_rating' );
			}
		}
	}

	/**
	 * Adds sorting options.
	 *
	 * @param array $fields
	 * @return array
	 */
	public function add_sorting_options( $fields ) {
		$fields['sort']['options']['rating'] = esc_html__( 'Rating', 'hivepress-reviews' );

		return $fields;
	}

	/**
	 * Sets sorting query.
	 *
	 * @param WP_Query $query
	 */
	public function set_sorting_query( $query ) {
		if ( $query->is_main_query() && is_post_type_archive( 'hp_listing' ) ) {

			// Sort results.
			$sort_filters = hivepress()->form->validate_form( 'listing__sort' );

			if ( false !== $sort_filters && 'rating' === $sort_filters['sort'] ) {
				$query->set( 'meta_key', 'hp_rating' );
				$query->set( 'orderby', 'meta_value_num' );
				$query->set( 'order', 'DESC' );
			}
		}
	}

	/**
	 * Sets template context.
	 *
	 * @param array $context
	 * @return array
	 */
	public function set_template_context( $context ) {

		// Set defaults.
		$context['review_allowed'] = true;

		// Get reviews.
		$context['reviews'] = get_comments(
			[
				'type'    => 'comment',
				'status'  => 'approve',
				'post_id' => get_the_ID(),
			]
		);

		if ( is_user_logged_in() ) {

			// Get reviews.
			$reviews = get_comments(
				[
					'type'    => 'comment',
					'user_id' => get_current_user_id(),
					'post_id' => get_the_ID(),
					'number'  => 1,
					'fields'  => 'ids',
				]
			);

			if ( ! empty( $reviews ) ) {
				$context['review_allowed'] = false;
			}
		} else {
			$context['review_allowed'] = false;
		}

		return $context;
	}
}
