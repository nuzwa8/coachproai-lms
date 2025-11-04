<?php
/**
 * CoachProAI Admin Dashboard Template
 *
 * @package CoachProAI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();
$db = \CoachProAI\Database::instance();
?>

<div class="wrap coachproai-admin-wrap">
	<div class="coachproai-admin-header">
		<h1><?php _e( 'CoachProAI LMS Dashboard', 'coachproai-lms' ); ?></h1>
		<p><?php _e( 'Welcome to your AI-powered coaching platform', 'coachproai-lms' ); ?></p>
	</div>

	<div class="coachproai-dashboard-stats">
		<div class="coachproai-stat-card total-students" data-stat="total_students">
			<div class="coachproai-stat-value"><?php echo wp_count_posts( 'coaching_program' )->publish ?? 0; ?></div>
			<div class="coachproai-stat-label"><?php _e( 'Total Programs', 'coachproai-lms' ); ?></div>
			<div class="coachproai-stat-change positive">+12%</div>
		</div>

		<div class="coachproai-stat-card active-sessions" data-stat="active_sessions">
			<div class="coachproai-stat-value">0</div>
			<div class="coachproai-stat-label"><?php _e( 'Active AI Sessions', 'coachproai-lms' ); ?></div>
			<div class="coachproai-stat-change positive">+5%</div>
		</div>

		<div class="coachproai-stat-card completion-rate" data-stat="completion_rate">
			<div class="coachproai-stat-value">85%</div>
			<div class="coachproai-stat-label"><?php _e( 'Average Completion', 'coachproai-lms' ); ?></div>
			<div class="coachproai-stat-change positive">+3%</div>
		</div>

		<div class="coachproai-stat-card" data-stat="total_revenue">
			<div class="coachproai-stat-value">$0</div>
			<div class="coachproai-stat-label"><?php _e( 'Total Revenue', 'coachproai-lms' ); ?></div>
			<div class="coachproai-stat-change positive">+0%</div>
		</div>
	</div>

	<div class="coachproai-charts-grid">
		<div class="coachproai-chart-card">
			<h3 class="coachproai-chart-title"><?php _e( 'Program Enrollments', 'coachproai-lms' ); ?></h3>
			<canvas id="enrollment-chart" width="400" height="200"></canvas>
		</div>

		<div class="coachproai-chart-card">
			<h3 class="coachproai-chart-title"><?php _e( 'Progress Overview', 'coachproai-lms' ); ?></h3>
			<canvas id="progress-chart" width="400" height="200"></canvas>
		</div>
	</div>

	<div class="coachproai-tabs">
		<div class="coachproai-tab-nav">
			<a href="#recent-activity" class="active"><?php _e( 'Recent Activity', 'coachproai-lms' ); ?></a>
			<a href="#ai-coaches"><?php _e( 'AI Coaches', 'coachproai-lms' ); ?></a>
			<a href="#top-programs"><?php _e( 'Top Programs', 'coachproai-lms' ); ?></a>
		</div>

		<div id="recent-activity" class="coachproai-tab-content active">
			<table class="coachproai-table">
				<thead>
					<tr>
						<th><?php _e( 'Student', 'coachproai-lms' ); ?></th>
						<th><?php _e( 'Program', 'coachproai-lms' ); ?></th>
						<th><?php _e( 'Activity', 'coachproai-lms' ); ?></th>
						<th><?php _e( 'Time', 'coachproai-lms' ); ?></th>
						<th><?php _e( 'Actions', 'coachproai-lms' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php _e( 'No recent activity', 'coachproai-lms' ); ?></td>
						<td>-</td>
						<td>-</td>
						<td>-</td>
						<td>-</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div id="ai-coaches" class="coachproai-tab-content">
			<div class="coachproai-coaches-grid">
				<?php
				$coaches = get_posts(
					array(
						'post_type'      => 'ai_coach',
						'posts_per_page' => 6,
						'post_status'    => 'publish',
					)
				);

				if ( ! empty( $coaches ) ) :
				 foreach ( $coaches as $coach ) :
					$specialty = get_post_meta( $coach->ID, '_coach_specialty', true );
					$active = get_post_meta( $coach->ID, '_coach_active', true ) === 'yes';
				 ?>
					<div class="coachproai-coach-card">
						<div class="coachproai-coach-avatar">
							<?php echo get_avatar( 0, 60, '', '', array( 'class' => 'coachproai-coach-avatar' ) ); ?>
						</div>
						<h4 class="coachproai-coach-name"><?php echo esc_html( $coach->post_title ); ?></h4>
						<p class="coachproai-coach-specialty"><?php echo esc_html( $specialty ?: 'General Coaching' ); ?></p>
						<div class="coachproai-coach-status <?php echo $active ? 'active' : 'inactive'; ?>">
							<?php echo $active ? __( 'Active', 'coachproai-lms' ) : __( 'Inactive', 'coachproai-lms' ); ?>
						</div>
					</div>
				 <?php endforeach; ?>
				<?php else : ?>
					<p><?php _e( 'No AI coaches created yet.', 'coachproai-lms' ); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<div id="top-programs" class="coachproai-tab-content">
			<table class="coachproai-table">
				<thead>
					<tr>
						<th><?php _e( 'Program', 'coachproai-lms' ); ?></th>
						<th><?php _e( 'Students', 'coachproai-lms' ); ?></th>
						<th><?php _e( 'Completion Rate', 'coachproai-lms' ); ?></th>
						<th><?php _e( 'Revenue', 'coachproai-lms' ); ?></th>
						<th><?php _e( 'Actions', 'coachproai-lms' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$programs = get_posts(
						array(
							'post_type'      => 'coaching_program',
							'posts_per_page' => 10,
							'post_status'    => 'publish',
						)
					);

					if ( ! empty( $programs ) ) :
					 foreach ( $programs as $program ) :
					 ?>
						<tr>
							<td>
								<strong><?php echo esc_html( $program->post_title ); ?></strong>
								<br><small><?php echo wp_trim_words( $program->post_excerpt, 10 ); ?></small>
							</td>
							<td>0</td>
							<td>0%</td>
							<td>$0</td>
							<td class="actions">
								<a href="<?php echo get_edit_post_link( $program->ID ); ?>" class="btn btn-edit"><?php _e( 'Edit', 'coachproai-lms' ); ?></a>
								<a href="<?php echo get_permalink( $program->ID ); ?>" class="btn btn-view"><?php _e( 'View', 'coachproai-lms' ); ?></a>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="5"><?php _e( 'No programs found.', 'coachproai-lms' ); ?></td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="coachproai-admin-actions">
		<button class="button button-primary" data-action="refresh-dashboard">
			<span class="dashicons dashicons-update"></span> <?php _e( 'Refresh Dashboard', 'coachproai-lms' ); ?>
		</button>
		<button class="button" data-action="export-analytics" data-format="json">
			<span class="dashicons dashicons-download"></span> <?php _e( 'Export Analytics', 'coachproai-lms' ); ?>
		</button>
		<a href="<?php echo admin_url( 'edit.php?post_type=coaching_program' ); ?>" class="button button-secondary">
			<span class="dashicons dashicons-plus"></span> <?php _e( 'Add New Program', 'coachproai-lms' ); ?>
		</a>
	</div>
</div>

<style>
.coachproai-admin-actions {
	margin-top: 30px;
	padding: 20px;
	background: #f8f9fa;
	border-radius: 8px;
	display: flex;
	gap: 10px;
	flex-wrap: wrap;
}

.coachproai-admin-actions .button {
	display: inline-flex;
	align-items: center;
	gap: 8px;
}
</style>

<script>
// Initialize dashboard charts with demo data
if (typeof Chart !== 'undefined') {
	// Enrollment chart data (last 7 days)
	const enrollmentLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
	const enrollmentData = [12, 19, 3, 5, 2, 3, 7];
	
	const enrollmentCtx = document.getElementById('enrollment-chart');
	if (enrollmentCtx) {
		new Chart(enrollmentCtx, {
			type: 'line',
			data: {
				labels: enrollmentLabels,
				datasets: [{
					label: 'Enrollments',
					data: enrollmentData,
					borderColor: '#3498db',
					backgroundColor: 'rgba(52, 152, 219, 0.1)',
					tension: 0.4,
					fill: true
				}]
			},
			options: {
				responsive: true,
				plugins: {
					legend: {
						display: false
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						ticks: {
							stepSize: 1
						}
					}
				}
			}
		});
	}

	// Progress chart data
	const progressCtx = document.getElementById('progress-chart');
	if (progressCtx) {
		new Chart(progressCtx, {
			type: 'doughnut',
			data: {
				labels: ['Completed', 'In Progress', 'Not Started'],
				datasets: [{
					data: [65, 25, 10],
					backgroundColor: ['#27ae60', '#f39c12', '#e74c3c']
				}]
			},
			options: {
				responsive: true,
				plugins: {
					legend: {
						position: 'bottom'
					}
				}
			}
		});
	}
}
</script>