<?php
/**
 * CoachProAI Settings Template
 *
 * @package CoachProAI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Handle form submission
if ( isset( $_POST['submit'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'coachproai_settings' ) ) {
	update_option( 'coachproai_general_settings', $_POST['coachproai_general_settings'] );
	update_option( 'coachproai_ai_settings', $_POST['coachproai_ai_settings'] );
	update_option( 'coachproai_coaching_settings', $_POST['coachproai_coaching_settings'] );
	
	echo '<div class="notice notice-success"><p>' . __( 'Settings saved successfully!', 'coachproai-lms' ) . '</p></div>';
}

$general_settings = get_option( 'coachproai_general_settings', array() );
$ai_settings = get_option( 'coachproai_ai_settings', array() );
$coaching_settings = get_option( 'coachproai_coaching_settings', array() );
$pages = get_pages();
?>

<div class="wrap coachproai-admin-wrap">
	<h1><?php _e( 'CoachProAI LMS Settings', 'coachproai-lms' ); ?></h1>

	<div class="coachproai-tabs">
		<div class="coachproai-tab-nav">
			<a href="#general" class="active"><?php _e( 'General', 'coachproai-lms' ); ?></a>
			<a href="#ai"><?php _e( 'AI Configuration', 'coachproai-lms' ); ?></a>
			<a href="#coaching"><?php _e( 'Coaching', 'coachproai-lms' ); ?></a>
		</div>

		<form method="post" class="coachproai-settings-form">
			<?php wp_nonce_field( 'coachproai_settings' ); ?>

			<div id="general" class="coachproai-tab-content active">
				<div class="coachproai-form-section">
					<h2 class="coachproai-form-title"><?php _e( 'General Settings', 'coachproai-lms' ); ?></h2>
					
					<div class="coachproai-form-field">
						<label class="coachproai-form-label"><?php _e( 'Programs Page', 'coachproai-lms' ); ?></label>
						<select name="coachproai_general_settings[programs_page]" class="coachproai-form-select">
							<option value=""><?php _e( 'Select a page', 'coachproai-lms' ); ?></option>
							<?php foreach ( $pages as $page ) : ?>
								<option value="<?php echo $page->ID; ?>" <?php selected( $general_settings['programs_page'] ?? '', $page->ID ); ?>>
									<?php echo esc_html( $page->post_title ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<p class="coachproai-form-description"><?php _e( 'Select the page that displays your coaching programs.', 'coachproai-lms' ); ?></p>
					</div>

					<div class="coachproai-form-field">
						<label class="coachproai-form-label"><?php _e( 'Currency', 'coachproai-lms' ); ?></label>
						<input 
							type="text" 
							name="coachproai_general_settings[currency]" 
							value="<?php echo esc_attr( $general_settings['currency'] ?? 'USD' ); ?>" 
							maxlength="3" 
							class="coachproai-form-input"
						>
						<span style="margin: 0 10px;"><?php _e( 'Symbol:', 'coachproai-lms' ); ?></span>
						<input 
							type="text" 
							name="coachproai_general_settings[currency_symbol]" 
							value="<?php echo esc_attr( $general_settings['currency_symbol'] ?? '$' ); ?>" 
							maxlength="5" 
							class="coachproai-form-input"
							style="width: 100px;"
						>
						<p class="coachproai-form-description"><?php _e( 'Currency code and symbol for pricing.', 'coachproai-lms' ); ?></p>
					</div>

					<div class="coachproai-form-field">
						<label class="coachproai-form-label"><?php _e( 'Date Format', 'coachproai-lms' ); ?></label>
						<select name="coachproai_general_settings[date_format]" class="coachproai-form-select">
							<option value="M j, Y" <?php selected( $general_settings['date_format'] ?? 'M j, Y', 'M j, Y' ); ?>><?php echo date( 'M j, Y' ); ?></option>
							<option value="F j, Y" <?php selected( $general_settings['date_format'] ?? '', 'F j, Y' ); ?>><?php echo date( 'F j, Y' ); ?></option>
							<option value="d/m/Y" <?php selected( $general_settings['date_format'] ?? '', 'd/m/Y' ); ?>><?php echo date( 'd/m/Y' ); ?></option>
							<option value="Y-m-d" <?php selected( $general_settings['date_format'] ?? '', 'Y-m-d' ); ?>><?php echo date( 'Y-m-d' ); ?></option>
						</select>
						<p class="coachproai-form-description"><?php _e( 'Date format for displaying dates throughout the plugin.', 'coachproai-lms' ); ?></p>
					</div>
				</div>
			</div>

			<div id="ai" class="coachproai-tab-content">
				<div class="coachproai-form-section">
					<h2 class="coachproai-form-title"><?php _e( 'AI Configuration', 'coachproai-lms' ); ?></h2>
					
					<div class="coachproai-form-field">
						<label class="coachproai-form-label"><?php _e( 'OpenAI API Key', 'coachproai-lms' ); ?></label>
						<input 
							type="password" 
							name="coachproai_ai_settings[openai_api_key]" 
							value="<?php echo esc_attr( $ai_settings['openai_api_key'] ?? '' ); ?>" 
							class="coachproai-form-input"
							placeholder="sk-..."
						>
						<p class="coachproai-form-description">
							<?php _e( 'Get your API key from ', 'coachproai-lms' ); ?>
							<a href="https://platform.openai.com/api-keys" target="_blank"><?php _e( 'OpenAI Dashboard', 'coachproai-lms' ); ?></a>
						</p>
					</div>

					<div class="coachproai-form-field">
						<label class="coachproai-form-label"><?php _e( 'AI Response Style', 'coachproai-lms' ); ?></label>
						<select name="coachproai_ai_settings[ai_response_style]" class="coachproai-form-select">
							<option value="conversational" <?php selected( $ai_settings['ai_response_style'] ?? 'conversational', 'conversational' ); ?>><?php _e( 'Conversational', 'coachproai-lms' ); ?></option>
							<option value="formal" <?php selected( $ai_settings['ai_response_style'] ?? '', 'formal' ); ?>><?php _e( 'Formal', 'coachproai-lms' ); ?></option>
							<option value="encouraging" <?php selected( $ai_settings['ai_response_style'] ?? '', 'encouraging' ); ?>><?php _e( 'Encouraging', 'coachproai-lms' ); ?></option>
							<option value="direct" <?php selected( $ai_settings['ai_response_style'] ?? '', 'direct' ); ?>><?php _e( 'Direct', 'coachproai-lms' ); ?></option>
						</select>
						<p class="coachproai-form-description"><?php _e( 'Default AI response communication style.', 'coachproai-lms' ); ?></p>
					</div>

					<div class="coachproai-form-field">
						<label class="coachproai-form-label"><?php _e( 'Max Response Length', 'coachproai-lms' ); ?></label>
						<input 
							type="number" 
							name="coachproai_ai_settings[ai_max_response_length]" 
							value="<?php echo esc_attr( $ai_settings['ai_max_response_length'] ?? 500 ); ?>" 
							min="100" 
							max="1000" 
							class="coachproai-form-input"
						>
						<p class="coachproai-form-description"><?php _e( 'Maximum characters for AI responses.', 'coachproai-lms' ); ?></p>
					</div>

					<div class="coachproai-form-field">
						<label>
							<input 
								type="checkbox" 
								name="coachproai_ai_settings[ai_learning_adaptation]" 
								value="enabled" 
								<?php checked( $ai_settings['ai_learning_adaptation'] ?? 'enabled', 'enabled' ); ?>
							>
							<?php _e( 'Enable AI Learning Adaptation', 'coachproai-lms' ); ?>
						</label>
						<p class="coachproai-form-description"><?php _e( 'Allow AI to adapt responses based on student learning patterns.', 'coachproai-lms' ); ?></p>
					</div>
				</div>
			</div>

			<div id="coaching" class="coachproai-tab-content">
				<div class="coachproai-form-section">
					<h2 class="coachproai-form-title"><?php _e( 'Coaching Settings', 'coachproai-lms' ); ?></h2>
					
					<div class="coachproai-form-field">
						<label class="coachproai-form-label"><?php _e( 'Default Session Duration', 'coachproai-lms' ); ?></label>
						<input 
							type="number" 
							name="coachproai_coaching_settings[session_duration]" 
							value="<?php echo esc_attr( $coaching_settings['session_duration'] ?? 30 ); ?>" 
							min="5" 
							max="120" 
							class="coachproai-form-input"
						>
						<p class="coachproai-form-description"><?php _e( 'Default AI coaching session duration in minutes.', 'coachproai-lms' ); ?></p>
					</div>

					<div class="coachproai-form-field">
						<label>
							<input 
								type="checkbox" 
								name="coachproai_coaching_settings[session_reminders]" 
								value="enabled" 
								<?php checked( $coaching_settings['session_reminders'] ?? 'enabled', 'enabled' ); ?>
							>
							<?php _e( 'Enable Session Reminders', 'coachproai-lms' ); ?>
						</label>
						<p class="coachproai-form-description"><?php _e( 'Send reminders for coaching sessions.', 'coachproai-lms' ); ?></p>
					</div>

					<div class="coachproai-form-field">
						<label>
							<input 
								type="checkbox" 
								name="coachproai_coaching_settings[goal_setting]" 
								value="enabled" 
								<?php checked( $coaching_settings['goal_setting'] ?? 'enabled', 'enabled' ); ?>
							>
							<?php _e( 'Enable Goal Setting', 'coachproai-lms' ); ?>
						</label>
						<p class="coachproai-form-description"><?php _e( 'Allow students to set and track coaching goals.', 'coachproai-lms' ); ?></p>
					</div>

					<div class="coachproai-form-field">
						<label>
							<input 
								type="checkbox" 
								name="coachproai_coaching_settings[progress_tracking]" 
								value="enabled" 
								<?php checked( $coaching_settings['progress_tracking'] ?? 'enabled', 'enabled' ); ?>
							>
							<?php _e( 'Enable Progress Tracking', 'coachproai-lms' ); ?>
						</label>
						<p class="coachproai-form-description"><?php _e( 'Track student progress throughout coaching programs.', 'coachproai-lms' ); ?></p>
					</div>
				</div>
			</div>

			<div class="coachproai-form-actions" style="margin-top: 30px;">
				<?php submit_button( __( 'Save Settings', 'coachproai-lms' ), 'primary', 'submit' ); ?>
			</div>
		</form>
	</div>
</div>