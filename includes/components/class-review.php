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

		// todo
		add_filter( 'hivepress/form/form_values/review__submit', [ $this, 'set_form_values' ] );
		add_action( 'hivepress/form/submit_form/review__submit', [ $this, 'submit' ] );

		add_filter( 'hivepress/form/field_value/rating', [ $this, 'sanitize_rating_field' ], 10, 2 );
		add_filter( 'hivepress/form/field_html/rating', [ $this, 'render_rating_field' ], 10, 4 );

		// Update rating.
		add_action( 'comment_post', [ $this, 'update_rating' ] );
		add_action( 'wp_set_comment_status', [ $this, 'update_rating' ] );
		add_action( 'delete_comment', [ $this, 'update_rating' ] );

		add_filter( 'hivepress/template/template_context/single_listing', [ $this, 'todo' ] );
	}

	public function todo( $context ) {
		$context['reviews'] = get_comments(
			[
				'type'    => 'comment',
				'status'  => 'approve',
				'post_id' => get_the_ID(),
			]
		);

		if ( is_user_logged_in() ) {
			$reviews = get_comments(
				[
					'type'    => 'comment',
					'user_id' => get_current_user_id(),
					'post_id' => get_the_ID(),
					'number'  => 1,
				]
			);

			if ( ! empty( $reviews ) ) {
				$context['review'] = reset( $reviews );
			}
		}

		return $context;
	}

	/**
	 * Sanitizes rating field.
	 *
	 * @param mixed $value
	 * @param array $args
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
		$output .= '<div class="hp-rating hp-js-rating" data-id="' . esc_attr( $id ) . '" data-value="' . esc_attr( $value ) . '"></div>';

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

		if ( 0 !== $post_id ) {

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
				hivepress()->form->add_error( esc_html__( 'Review is already submitted.', 'hivepress-reviews' ) );
			}
		}
	}

	/**
	 * Updates rating.
	 *
	 * @param int $review_id
	 */
	public function update_rating( $review_id ) {

		// Get current review.
		$current_review = get_comment( $review_id );

		if ( ! is_null( $current_review ) && 'comment' === $current_review->comment_type && 'hp_listing' === $current_review->post_type ) {
			$rating_count = 0;
			$rating_value = 0;

			// Get all reviews.
			$reviews = get_comments(
				[
					'type'    => 'comment',
					'status'  => 'approve',
					'post_id' => $current_review->comment_post_ID,
				]
			);

			// Calculate rating.
			foreach ( $reviews as $review ) {
				$rating = absint( get_comment_meta( $review->comment_ID, 'hp_rating', true ) );

				if ( $rating < 1 ) {
					$rating = 1;
				} elseif ( $rating > 5 ) {
					$rating = 5;
				}

				$rating_count++;
				$rating_value += $rating;
			}

			if ( $rating_count > 0 ) {
				$rating_value = round( $rating_value / $rating_count, 1 );

				// Update rating.
				update_post_meta( $current_review->comment_post_ID, 'hp_rating_count', $rating_count );
				update_post_meta( $current_review->comment_post_ID, 'hp_rating_value', $rating_value );
			} else {

				// Delete rating.
				delete_post_meta( $current_review->comment_post_ID, 'hp_rating_count' );
				delete_post_meta( $current_review->comment_post_ID, 'hp_rating_value' );
			}
		}
	}
}
