<?php
/**
 * CoachProAI Meta Box Template for Programs
 *
 * @package CoachProAI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$price = get_post_meta( $post->ID, '_coachproai_price', true );
$duration = get_post_meta( $post->ID, '_coachproai_duration', true );
$level = get_post_meta( $post->ID, '_coachproai_level', true );
$max_students = get_post_meta( $post->ID, '_coachproai_max_students', true );
?>

<table class="form-table">
	<tbody>
		<tr>
			<th scope="row">
				<label for="coachproai_price"><?php _e( 'Price', 'coachproai-lms' ); ?></label>
			</th>
			<td>
				<input 
					type="number" 
					id="coachproai_price" 
					name="_coachproai_price" 
					value="<?php echo esc_attr( $price ); ?>" 
					step="0.01" 
					min="0" 
					class="regular-text" 
					placeholder="0.00"
				>
				<p class="description"><?php _e( 'Set program price. Leave empty for free programs.', 'coachproai-lms' ); ?></p>
			</td>
		</tr>
		
		<tr>
			<th scope="row">
				<label for="coachproai_duration"><?php _e( 'Duration (Weeks)', 'coachproai-lms' ); ?></label>
			</th>
			<td>
				<input 
					type="number" 
					id="coachproai_duration" 
					name="_coachproai_duration" 
					value="<?php echo esc_attr( $duration ); ?>" 
					min="1" 
					class="small-text" 
					placeholder="4"
				>
				<p class="description"><?php _e( 'Program duration in weeks.', 'coachproai-lms' ); ?></p>
			</td>
		</tr>
		
		<tr>
			<th scope="row">
				<label for="coachproai_level"><?php _e( 'Level', 'coachproai-lms' ); ?></label>
			</th>
			<td>
				<select id="coachproai_level" name="_coachproai_level" class="regular-text">
					<option value=""><?php _e( 'Select Level', 'coachproai-lms' ); ?></option>
					<option value="beginner" <?php selected( $level, 'beginner' ); ?>><?php _e( 'Beginner', 'coachproai-lms' ); ?></option>
					<option value="intermediate" <?php selected( $level, 'intermediate' ); ?>><?php _e( 'Intermediate', 'coachproai-lms' ); ?></option>
					<option value="advanced" <?php selected( $level, 'advanced' ); ?>><?php _e( 'Advanced', 'coachproai-lms' ); ?></option>
					<option value="expert" <?php selected( $level, 'expert' ); ?>><?php _e( 'Expert', 'coachproai-lms' ); ?></option>
				</select>
				<p class="description"><?php _e( 'Difficulty level of the program.', 'coachproai-lms' ); ?></p>
			</td>
		</tr>
		
		<tr>
			<th scope="row">
				<label for="coachproai_max_students"><?php _e( 'Maximum Students', 'coachproai-lms' ); ?></label>
			</th>
			<td>
				<input 
					type="number" 
					id="coachproai_max_students" 
					name="_coachproai_max_students" 
					value="<?php echo esc_attr( $max_students ); ?>" 
					min="0" 
					class="small-text" 
					placeholder="Unlimited"
				>
				<p class="description"><?php _e( 'Maximum number of students. Leave empty for unlimited.', 'coachproai-lms' ); ?></p>
			</td>
		</tr>
		
		<tr>
			<th scope="row">
				<label for="coachproai_ai_enabled"><?php _e( 'AI Coaching', 'coachproai-lms' ); ?></label>
			</th>
			<td>
				<label>
					<input 
						type="checkbox" 
						id="coachproai_ai_enabled" 
						name="_coachproai_ai_enabled" 
						value="1" 
						<?php checked( get_post_meta( $post->ID, '_coachproai_ai_enabled', true ), '1' ); ?>
					>
					<?php _e( 'Enable AI coaching for this program', 'coachproai-lms' ); ?>
				</label>
				<p class="description"><?php _e( 'Enable AI-powered coaching sessions for enrolled students.', 'coachproai-lms' ); ?></p>
			</td>
		</tr>
		
		<tr>
			<th scope="row">
				<label for="coachproai_ai_coach"><?php _e( 'Assigned AI Coach', 'coachproai-lms' ); ?></label>
			</th>
			<td>
				<select id="coachproai_ai_coach" name="_coachproai_ai_coach" class="regular-text">
					<option value=""><?php _e( 'Auto-select', 'coachproai-lms' ); ?></option>
					<?php
					$coaches = get_posts(
						array(
							'post_type'      => 'ai_coach',
							'posts_per_page' => -1,
							'post_status'    => 'publish',
						)
					);
					$selected_coach = get_post_meta( $post->ID, '_coachproai_ai_coach', true );
					
				 foreach ( $coaches as $coach ) :
					 ?>
						<option value="<?php echo $coach->ID; ?>" <?php selected( $selected_coach, $coach->ID ); ?>>
							<?php echo esc_html( $coach->post_title ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<p class="description"><?php _e( 'Select specific AI coach for this program.', 'coachproai-lms' ); ?></p>
			</td>
		</tr>
	</tbody>
</table>