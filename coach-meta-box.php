<?php
/**
 * CoachProAI Meta Box Template for AI Coaches
 *
 * @package CoachProAI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$specialty = get_post_meta( $post->ID, '_coach_specialty', true );
$personality = get_post_meta( $post->ID, '_coach_personality', true );
$active = get_post_meta( $post->ID, '_coach_active', true );
?>

<table class="form-table">
	<tbody>
		<tr>
			<th scope="row">
				<label for="coach_specialty"><?php _e( 'Specialty', 'coachproai-lms' ); ?></label>
			</th>
			<td>
				<input 
					type="text" 
					id="coach_specialty" 
					name="_coach_specialty" 
					value="<?php echo esc_attr( $specialty ); ?>" 
					class="regular-text" 
					placeholder="e.g., Leadership, Career Development, Wellness"
				>
				<p class="description"><?php _e( 'Coach specialty or focus area.', 'coachproai-lms' ); ?></p>
			</td>
		</tr>
		
		<tr>
			<th scope="row">
				<label for="coach_personality"><?php _e( 'Personality Style', 'coachproai-lms' ); ?></label>
			</th>
			<td>
				<select id="coach_personality" name="_coach_personality" class="regular-text">
					<option value="supportive" <?php selected( $personality, 'supportive' ); ?>><?php _e( 'Supportive', 'coachproai-lms' ); ?></option>
					<option value="professional" <?php selected( $personality, 'professional' ); ?>><?php _e( 'Professional', 'coachproai-lms' ); ?></option>
					<option value="calm" <?php selected( $personality, 'calm' ); ?>><?php _e( 'Calm', 'coachproai-lms' ); ?></option>
					<option value="encouraging" <?php selected( $personality, 'encouraging' ); ?>><?php _e( 'Encouraging', 'coachproai-lms' ); ?></option>
					<option value="direct" <?php selected( $personality, 'direct' ); ?>><?php _e( 'Direct', 'coachproai-lms' ); ?></option>
				</select>
				<p class="description"><?php _e( 'Communication style and personality traits.', 'coachproai-lms' ); ?></p>
			</td>
		</tr>
		
		<tr>
			<th scope="row">
				<label for="coach_active"><?php _e( 'Status', 'coachproai-lms' ); ?></label>
			</th>
			<td>
				<label>
					<input 
						type="checkbox" 
						id="coach_active" 
						name="_coach_active" 
						value="yes" 
						<?php checked( $active, 'yes' ); ?>
					>
					<?php _e( 'Active (available for coaching sessions)', 'coachproai-lms' ); ?>
				</label>
			</td>
		</tr>
		
		<tr>
			<th scope="row">
				<label for="coach_knowledge_base"><?php _e( 'Knowledge Base', 'coachproai-lms' ); ?></label>
			</th>
			<td>
				<textarea 
					id="coach_knowledge_base" 
					name="_coach_knowledge_base" 
					rows="5" 
					class="large-text" 
					placeholder="<?php esc_attr_e( 'Enter knowledge base content for AI responses...', 'coachproai-lms' ); ?>"
				><?php echo esc_textarea( get_post_meta( $post->ID, '_coach_knowledge_base', true ) ); ?></textarea>
				<p class="description"><?php _e( 'Specific knowledge, techniques, and expertise for this coach.', 'coachproai-lms' ); ?></p>
			</td>
		</tr>
		
		<tr>
			<th scope="row">
				<label for="coach_response_style"><?php _e( 'Response Style', 'coachproai-lms' ); ?></label>
			</th>
			<td>
				<select id="coach_response_style" name="_coach_response_style" class="regular-text">
					<option value="conversational" <?php selected( get_post_meta( $post->ID, '_coach_response_style', true ), 'conversational' ); ?>><?php _e( 'Conversational', 'coachproai-lms' ); ?></option>
					<option value="questioning" <?php selected( get_post_meta( $post->ID, '_coach_response_style', true ), 'questioning' ); ?>><?php _e( 'Questioning', 'coachproai-lms' ); ?></option>
					<option value="advisory" <?php selected( get_post_meta( $post->ID, '_coach_response_style', true ), 'advisory' ); ?>><?php _e( 'Advisory', 'coachproai-lms' ); ?></option>
					<option value="motivational" <?php selected( get_post_meta( $post->ID, '_coach_response_style', true ), 'motivational' ); ?>><?php _e( 'Motivational', 'coachproai-lms' ); ?></option>
				</select>
				<p class="description"><?php _e( 'How the coach should respond to student queries.', 'coachproai-lms' ); ?></p>
			</td>
		</tr>
	</tbody>
</table>