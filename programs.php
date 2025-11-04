<?php
/**
 * CoachProAI Programs Shortcode Template
 *
 * @package CoachProAI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get plugin instance
$plugin = \CoachProAI\Public\Public_Platform::instance();
?>

<div class="coachproai-programs-grid" id="coachproai-programs-grid">
	<?php if ( $query->have_posts() ) : ?>
		<div class="coachproai-programs-container">
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
				<?php
				$program_id = get_the_ID();
				$price = $plugin->get_program_price_display( $program_id );
				$duration = $plugin->get_program_duration_display( $program_id );
				$is_available = $plugin->is_program_available( $program_id );
				?>
				
				<div class="coachproai-program-card">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="coachproai-program-thumbnail">
							<a href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail( 'medium' ); ?>
							</a>
						</div>
					<?php endif; ?>
					
					<div class="coachproai-program-content">
						<h3 class="coachproai-program-title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>
						
						<div class="coachproai-program-meta">
							<span class="coachproai-program-price"><?php echo esc_html( $price ); ?></span>
							<?php if ( $duration ) : ?>
								<span class="coachproai-program-duration"><?php echo esc_html( $duration ); ?></span>
							<?php endif; ?>
							
							<?php
							$level = get_post_meta( $program_id, '_coachproai_level', true );
							if ( $level ) :
							?>
								<span class="coachproai-program-level"><?php echo esc_html( ucfirst( $level ) ); ?></span>
							<?php endif; ?>
						</div>
						
						<div class="coachproai-program-excerpt">
							<?php the_excerpt(); ?>
						</div>
						
						<?php
						// Display categories/focus areas
						$categories = get_the_terms( $program_id, 'program_category' );
						$focus_areas = get_the_terms( $program_id, 'coaching_focus' );
						?>
						
						<?php if ( $categories || $focus_areas ) : ?>
							<div class="coachproai-program-tags">
								<?php if ( $categories ) : ?>
									<?php foreach ( $categories as $category ) : ?>
										<span class="coachproai-tag"><?php echo esc_html( $category->name ); ?></span>
									<?php endforeach; ?>
								<?php endif; ?>
								
								<?php if ( $focus_areas ) : ?>
									<?php foreach ( $focus_areas as $area ) : ?>
										<span class="coachproai-tag focus"><?php echo esc_html( $area->name ); ?></span>
									<?php endforeach; ?>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						
						<div class="coachproai-program-actions">
							<a href="<?php the_permalink(); ?>" class="coachproai-btn coachproai-btn-secondary">
								<?php _e( 'View Details', 'coachproai-lms' ); ?>
							</a>
							
							<?php if ( $is_available && $price !== __( 'Free', 'coachproai-lms' ) && is_user_logged_in() ) : ?>
								<?php echo $plugin->generate_enrollment_button( $program_id ); ?>
							<?php elseif ( $is_available && $price === __( 'Free', 'coachproai-lms' ) ) : ?>
								<?php echo $plugin->generate_enrollment_button( $program_id ); ?>
							<?php endif; ?>
						</div>
						
						<?php if ( ! $is_available ) : ?>
							<div class="coachproai-program-full">
								<?php _e( 'Program Full', 'coachproai-lms' ); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endwhile; ?>
		</div>
		
		<?php
		// Pagination
		if ( $query->max_num_pages > 1 ) :
		?>
			<div class="coachproai-pagination">
				<?php
				echo paginate_links( array(
					'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
					'format'    => '?paged=%#%',
					'current'   => max( 1, get_query_var( 'paged' ) ),
					'total'     => $query->max_num_pages,
					'prev_text' => __( '&larr; Previous', 'coachproai-lms' ),
					'next_text' => __( 'Next &rarr;', 'coachproai-lms' ),
				) );
				?>
			</div>
		<?php endif; ?>
		
	<?php else : ?>
		<div class="coachproai-no-programs">
			<p><?php _e( 'No coaching programs found.', 'coachproai-lms' ); ?></p>
		</div>
	<?php endif; ?>
	
	<?php wp_reset_postdata(); ?>
</div>

<script>
// Initialize programs grid
document.addEventListener('DOMContentLoaded', function() {
	if (typeof CoachProAI !== 'undefined') {
		CoachProAI.initProgramsGrid();
	}
});
</script>