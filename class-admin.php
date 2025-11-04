<?php
/**
 * CoachProAI Admin Class
 *
 * @package CoachProAI\Admin
 */

namespace CoachProAI\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Class
 */
class Admin {

	/**
	 * Plugin instance
	 * @var Admin
	 */
	private static $instance = null;

	/**
	 * Get instance
	 * @return Admin
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'init_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_coaching_program', array( $this, 'save_coaching_program_meta' ) );
		add_action( 'add_meta_boxes_post', array( $this, 'add_program_meta_boxes' ) );
		
		// AJAX handlers
		add_action( 'wp_ajax_coachproai_get_dashboard_stats', array( $this, 'ajax_get_dashboard_stats' ) );
		add_action( 'wp_ajax_coachproai_export_analytics', array( $this, 'ajax_export_analytics' ) );
	}

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		// Main menu
		add_menu_page(
			__( 'CoachProAI LMS', 'coachproai-lms' ),
			__( 'CoachProAI', 'coachproai-lms' ),
			'manage_coaching_programs',
			'coachproai-dashboard',
			array( $this, 'dashboard_page' ),
			'dashicons-admin-network',
			25
		);

		// Dashboard submenu
		add_submenu_page(
			'coachproai-dashboard',
			__( 'Dashboard', 'coachproai-lms' ),
			__( 'Dashboard', 'coachproai-lms' ),
			'manage_coaching_programs',
			'coachproai-dashboard',
			array( $this, 'dashboard_page' )
		);

		// Programs submenu
		add_submenu_page(
			'coachproai-dashboard',
			__( 'Coaching Programs', 'coachproai-lms' ),
			__( 'Programs', 'coachproai-lms' ),
			'manage_coaching_programs',
			'edit.php?post_type=coaching_program'
		);

		// AI Coaches submenu
		add_submenu_page(
			'coachproai-dashboard',
			__( 'AI Coaches', 'coachproai-lms' ),
			__( 'AI Coaches', 'coachproai-lms' ),
			'manage_ai_coaches',
			'edit.php?post_type=ai_coach'
		);

		// Analytics submenu
		add_submenu_page(
			'coachproai-dashboard',
			__( 'Analytics', 'coachproai-lms' ),
			__( 'Analytics', 'coachproai-lms' ),
			'view_coaching_analytics',
			'coachproai-analytics',
			array( $this, 'analytics_page' )
		);

		// Settings submenu
		add_submenu_page(
			'coachproai-dashboard',
			__( 'Settings', 'coachproai-lms' ),
			__( 'Settings', 'coachproai-lms' ),
			'manage_coaching_settings',
			'coachproai-settings',
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Dashboard page
	 */
	public function dashboard_page() {
		include COACHPROAI_PLUGIN_PATH . 'templates/admin/dashboard.php';
	}

	/**
	 * Analytics page
	 */
	public function analytics_page() {
		include COACHPROAI_PLUGIN_PATH . 'templates/admin/analytics.php';
	}

	/**
	 * Settings page
	 */
	public function settings_page() {
		include COACHPROAI_PLUGIN_PATH . 'templates/admin/settings.php';
	}

	/**
	 * Initialize settings
	 */
	public function init_settings() {
		// General settings
		register_setting( 'coachproai_general_settings', 'coachproai_general_settings', array( $this, 'sanitize_general_settings' ) );
		
		add_settings_section(
			'coachproai_general_section',
			__( 'General Settings', 'coachproai-lms' ),
			array( $this, 'general_section_callback' ),
			'coachproai_general_settings'
		);

		add_settings_field(
			'programs_page',
			__( 'Programs Page', 'coachproai-lms' ),
			array( $this, 'programs_page_field' ),
			'coachproai_general_settings',
			'coachproai_general_section'
		);

		add_settings_field(
			'currency',
			__( 'Currency', 'coachproai-lms' ),
			array( $this, 'currency_field' ),
			'coachproai_general_settings',
			'coachproai_general_section'
		);

		// AI settings
		register_setting( 'coachproai_ai_settings', 'coachproai_ai_settings', array( $this, 'sanitize_ai_settings' ) );
		
		add_settings_section(
			'coachproai_ai_section',
			__( 'AI Configuration', 'coachproai-lms' ),
			array( $this, 'ai_section_callback' ),
			'coachproai_ai_settings'
		);

		add_settings_field(
			'openai_api_key',
			__( 'OpenAI API Key', 'coachproai-lms' ),
			array( $this, 'openai_api_key_field' ),
			'coachproai_ai_settings',
			'coachproai_ai_section'
		);

		add_settings_field(
			'ai_response_style',
			__( 'AI Response Style', 'coachproai-lms' ),
			array( $this, 'ai_response_style_field' ),
			'coachproai_ai_settings',
			'coachproai_ai_section'
		);
	}

	/**
	 * Add meta boxes
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'coachproai_program_settings',
			__( 'Program Settings', 'coachproai-lms' ),
			array( $this, 'program_settings_meta_box' ),
			'coaching_program',
			'side',
			'default'
		);

		add_meta_box(
			'coachproai_ai_coach_settings',
			__( 'AI Coach Settings', 'coachproai-lms' ),
			array( $this, 'ai_coach_settings_meta_box' ),
			'ai_coach',
			'side',
			'default'
		);
	}

	/**
	 * Program settings meta box
	 */
	public function program_settings_meta_box( $post ) {
		wp_nonce_field( 'coachproai_program_meta', 'coachproai_program_meta_nonce' );
		
		$price = get_post_meta( $post->ID, '_coachproai_price', true );
		$duration = get_post_meta( $post->ID, '_coachproai_duration', true );
		$level = get_post_meta( $post->ID, '_coachproai_level', true );
		$max_students = get_post_meta( $post->ID, '_coachproai_max_students', true );

		include COACHPROAI_PLUGIN_PATH . 'templates/admin/program-meta-box.php';
	}

	/**
	 * AI coach settings meta box
	 */
	public function ai_coach_settings_meta_box( $post ) {
		wp_nonce_field( 'coachproai_coach_meta', 'coachproai_coach_meta_nonce' );
		
		$specialty = get_post_meta( $post->ID, '_coach_specialty', true );
		$personality = get_post_meta( $post->ID, '_coach_personality', true );
		$active = get_post_meta( $post->ID, '_coach_active', true );

		include COACHPROAI_PLUGIN_PATH . 'templates/admin/coach-meta-box.php';
	}

	/**
	 * Save coaching program meta
	 */
	public function save_coaching_program_meta( $post_id ) {
		if ( ! isset( $_POST['coachproai_program_meta_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['coachproai_program_meta_nonce'], 'coachproai_program_meta' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields = array(
			'_coachproai_price' => 'floatval',
			'_coachproai_duration' => 'intval',
			'_coachproai_level' => 'sanitize_text_field',
			'_coachproai_max_students' => 'intval',
		);

	 foreach ( $fields as $field => $sanitize_function ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_post_meta( $post_id, $field, $sanitize_function( $_POST[ $field ] ) );
			}
		}
	}

	/**
	 * Sanitize general settings
	 */
	public function sanitize_general_settings( $input ) {
		$output = array();

		if ( isset( $input['programs_page'] ) ) {
			$output['programs_page'] = intval( $input['programs_page'] );
		}

		if ( isset( $input['currency'] ) ) {
			$output['currency'] = sanitize_text_field( $input['currency'] );
		}

		if ( isset( $input['currency_symbol'] ) ) {
			$output['currency_symbol'] = sanitize_text_field( $input['currency_symbol'] );
		}

		return $output;
	}

	/**
	 * Sanitize AI settings
	 */
	public function sanitize_ai_settings( $input ) {
		$output = array();

		if ( isset( $input['openai_api_key'] ) ) {
			$output['openai_api_key'] = sanitize_text_field( $input['openai_api_key'] );
		}

		if ( isset( $input['ai_response_style'] ) ) {
			$allowed_styles = array( 'conversational', 'formal', 'encouraging', 'direct' );
			$output['ai_response_style'] = in_array( $input['ai_response_style'], $allowed_styles ) ? $input['ai_response_style'] : 'conversational';
		}

		if ( isset( $input['ai_max_response_length'] ) ) {
			$output['ai_max_response_length'] = intval( $input['ai_max_response_length'] );
		}

		return $output;
	}

	/**
	 * Settings field callbacks
	 */
	public function general_section_callback() {
		echo '<p>' . __( 'Configure general settings for CoachProAI LMS.', 'coachproai-lms' ) . '</p>';
	}

	public function ai_section_callback() {
		echo '<p>' . __( 'Configure AI settings and API keys.', 'coachproai-lms' ) . '</p>';
	}

	public function programs_page_field() {
		$settings = get_option( 'coachproai_general_settings', array() );
		$pages = get_pages();
		$selected = isset( $settings['programs_page'] ) ? $settings['programs_page'] : '';

		echo '<select name="coachproai_general_settings[programs_page]">';
		echo '<option value="">' . __( 'Select a page', 'coachproai-lms' ) . '</option>';
	 foreach ( $pages as $page ) {
			echo '<option value="' . $page->ID . '"' . selected( $selected, $page->ID, false ) . '>' . esc_html( $page->post_title ) . '</option>';
		}
		echo '</select>';
	}

	public function currency_field() {
		$settings = get_option( 'coachproai_general_settings', array() );
		$currency = isset( $settings['currency'] ) ? $settings['currency'] : 'USD';
		$symbol = isset( $settings['currency_symbol'] ) ? $settings['currency_symbol'] : '$';

		echo '<input type="text" name="coachproai_general_settings[currency]" value="' . esc_attr( $currency ) . '" maxlength="3" style="width: 100px;" /> ';
		echo '<input type="text" name="coachproai_general_settings[currency_symbol]" value="' . esc_attr( $symbol ) . '" maxlength="5" style="width: 100px;" />';
	}

	public function openai_api_key_field() {
		$settings = get_option( 'coachproai_ai_settings', array() );
		$api_key = isset( $settings['openai_api_key'] ) ? $settings['openai_api_key'] : '';

		echo '<input type="password" name="coachproai_ai_settings[openai_api_key]" value="' . esc_attr( $api_key ) . '" style="width: 100%;" />';
		echo '<p class="description">' . __( 'Get your API key from OpenAI dashboard.', 'coachproai-lms' ) . '</p>';
	}

	public function ai_response_style_field() {
		$settings = get_option( 'coachproai_ai_settings', array() );
		$style = isset( $settings['ai_response_style'] ) ? $settings['ai_response_style'] : 'conversational';

		$styles = array(
			'conversational' => __( 'Conversational', 'coachproai-lms' ),
			'formal' => __( 'Formal', 'coachproai-lms' ),
			'encouraging' => __( 'Encouraging', 'coachproai-lms' ),
			'direct' => __( 'Direct', 'coachproai-lms' ),
		);

		echo '<select name="coachproai_ai_settings[ai_response_style]">';
	 foreach ( $styles as $value => $label ) {
			echo '<option value="' . $value . '"' . selected( $style, $value, false ) . '>' . $label . '</option>';
		}
		echo '</select>';
	}

	/**
	 * AJAX: Get dashboard stats
	 */
	public function ajax_get_dashboard_stats() {
		check_ajax_referer( 'coachproai_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'view_coaching_analytics' ) ) {
			wp_send_json_error( __( 'Unauthorized access.', 'coachproai-lms' ) );
		}

		$db = \CoachProAI\Database::instance();
		
		// Get overall stats
		$total_programs = wp_count_posts( 'coaching_program' )->publish;
		$total_students = $db->export_analytics_data( 'json' );
		
		// Get recent activity
		$recent_sessions = $db->get_ai_session( 0 ); // This would need modification for recent sessions

		wp_send_json_success( array(
			'total_programs' => $total_programs,
			'total_students' => 0, // This would need proper implementation
			'recent_sessions' => 0,
		) );
	}

	/**
	 * AJAX: Export analytics
	 */
	public function ajax_export_analytics() {
		check_ajax_referer( 'coachproai_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'view_coaching_analytics' ) ) {
			wp_send_json_error( __( 'Unauthorized access.', 'coachproai-lms' ) );
		}

		$format = sanitize_text_field( $_POST['format'] ?? 'json' );
		$date_range = sanitize_text_field( $_POST['date_range'] ?? '30 days' );

		$db = \CoachProAI\Database::instance();
		$data = $db->export_analytics_data( $format, $date_range );

		if ( $format === 'csv' ) {
			wp_send_json_success( array(
				'data' => $data,
				'filename' => 'coachproai-analytics-' . date( 'Y-m-d' ) . '.csv',
			) );
		} else {
			wp_send_json_success( $data );
		}
	}
}