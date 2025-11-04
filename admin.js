/**
 * CoachProAI LMS Admin JavaScript
 * Version: 1.0.0
 */

(function($) {
    'use strict';

    /**
     * CoachProAI Admin Class
     */
    class CoachProAIAdmin {
        constructor() {
            this.ajaxUrl = coachproai_admin_ajax.ajax_url;
            this.nonce = coachproai_admin_ajax.nonce;
            
            this.init();
        }

        init() {
            this.bindEvents();
            this.initComponents();
            this.initTabs();
            this.initCharts();
        }

        bindEvents() {
            // Settings form
            $(document).on('submit', '.coachproai-settings-form', this.handleSettingsSave.bind(this));
            
            // Meta box operations
            $(document).on('click', '[data-action="add-meta-row"]', this.addMetaRow.bind(this));
            $(document).on('click', '[data-action="remove-meta-row"]', this.removeMetaRow.bind(this));
            
            // AI Coach management
            $(document).on('click', '[data-action="activate-coach"]', this.activateCoach.bind(this));
            $(document).on('click', '[data-action="deactivate-coach"]', this.deactivateCoach.bind(this));
            
            // Analytics export
            $(document).on('click', '[data-action="export-analytics"]', this.exportAnalytics.bind(this));
            
            // Dashboard refresh
            $(document).on('click', '[data-action="refresh-dashboard"]', this.refreshDashboard.bind(this));
            
            // Program builder
            $(document).on('click', '[data-action="add-lesson"]', this.addLesson.bind(this));
            $(document).on('click', '[data-action="add-module"]', this.addModule.bind(this));
            $(document).on('click', '[data-action="reorder-items"]', this.reorderItems.bind(this));
        }

        initComponents() {
            // Initialize color picker
            if ($.fn.wpColorPicker) {
                $('.coachproai-color-picker').wpColorPicker();
            }

            // Initialize date picker
            if ($.fn.datepicker) {
                $('.coachproai-date-picker').datepicker({
                    dateFormat: 'yy-mm-dd'
                });
            }

            // Initialize sortable lists
            if ($.fn.sortable) {
                $('.coachproai-sortable').sortable({
                    handle: '.sortable-handle',
                    placeholder: 'sortable-placeholder'
                });
            }

            // Initialize tooltips
            $('.coachproai-tooltip').tooltip();
        }

        initTabs() {
            $('.coachproai-tab-nav a').on('click', function(e) {
                e.preventDefault();
                
                const target = $(this).attr('href');
                
                // Update nav
                $('.coachproai-tab-nav a').removeClass('active');
                $(this).addClass('active');
                
                // Update content
                $('.coachproai-tab-content').removeClass('active');
                $(target).addClass('active');
                
                // Store in sessionStorage
                sessionStorage.setItem('coachproai_active_tab', target);
            });

            // Restore last active tab
            const activeTab = sessionStorage.getItem('coachproai_active_tab');
            if (activeTab && $(activeTab).length) {
                $(`.coachproai-tab-nav a[href="${activeTab}"]`).click();
            }
        }

        initCharts() {
            if (typeof Chart === 'undefined') return;

            // Initialize charts
            this.initEnrollmentChart();
            this.initProgressChart();
            this.initEngagementChart();
        }

        initEnrollmentChart() {
            const canvas = $('#enrollment-chart');
            if (!canvas.length) return;

            const ctx = canvas[0].getContext('2d');
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: canvas.data('labels') || [],
                    datasets: [{
                        label: 'Enrollments',
                        data: canvas.data('data') || [],
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

        initProgressChart() {
            const canvas = $('#progress-chart');
            if (!canvas.length) return;

            const ctx = canvas[0].getContext('2d');
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'In Progress', 'Not Started'],
                    datasets: [{
                        data: canvas.data('data') || [0, 0, 0],
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

        initEngagementChart() {
            const canvas = $('#engagement-chart');
            if (!canvas.length) return;

            const ctx = canvas[0].getContext('2d');
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: canvas.data('labels') || [],
                    datasets: [{
                        label: 'Active Users',
                        data: canvas.data('data') || [],
                        backgroundColor: '#3498db'
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
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // ===== Settings Management =====
        handleSettingsSave(event) {
            event.preventDefault();
            
            const form = $(event.currentTarget);
            const submitButton = form.find('button[type="submit"]');
            
            this.showLoading(submitButton);
            
            const formData = new FormData(form[0]);
            formData.append('action', 'coachproai_save_settings');
            formData.append('nonce', this.nonce);
            
            $.ajax({
                url: this.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: (response) => {
                    this.hideLoading(submitButton);
                    
                    if (response.success) {
                        this.showNotice('Settings saved successfully!', 'success');
                    } else {
                        this.showNotice(response.data || 'Failed to save settings.', 'error');
                    }
                },
                error: () => {
                    this.hideLoading(submitButton);
                    this.showNotice('Network error. Please try again.', 'error');
                }
            });
        }

        // ===== Meta Box Management =====
        addMetaRow(event) {
            event.preventDefault();
            
            const container = $(event.currentTarget).data('container');
            const template = $(event.currentTarget).data('template');
            const target = $(container);
            
            if (!target.length || !template.length) return;
            
            const newRow = template.clone().removeClass('template').show();
            target.append(newRow);
            
            // Reinitialize components for the new row
            this.initComponents();
        }

        removeMetaRow(event) {
            event.preventDefault();
            
            const row = $(event.currentTarget).closest('.meta-row');
            row.fadeOut(300, function() {
                $(this).remove();
            });
        }

        // ===== AI Coach Management =====
        activateCoach(event) {
            event.preventDefault();
            
            const button = $(event.currentTarget);
            const coachId = button.data('coach-id');
            
            this.updateCoachStatus(coachId, 'active', button);
        }

        deactivateCoach(event) {
            event.preventDefault();
            
            const button = $(event.currentTarget);
            const coachId = button.data('coach-id');
            
            this.updateCoachStatus(coachId, 'inactive', button);
        }

        updateCoachStatus(coachId, status, button) {
            this.showLoading(button);
            
            $.ajax({
                url: this.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'coachproai_update_coach_status',
                    coach_id: coachId,
                    status: status,
                    nonce: this.nonce
                },
                success: (response) => {
                    this.hideLoading(button);
                    
                    if (response.success) {
                        if (status === 'active') {
                            button.removeClass('button-primary').addClass('button-secondary')
                                .text('Deactivate').attr('data-action', 'deactivate-coach');
                        } else {
                            button.removeClass('button-secondary').addClass('button-primary')
                                .text('Activate').attr('data-action', 'activate-coach');
                        }
                        this.showNotice('Coach status updated successfully!', 'success');
                    } else {
                        this.showNotice(response.data || 'Failed to update coach status.', 'error');
                    }
                },
                error: () => {
                    this.hideLoading(button);
                    this.showNotice('Network error. Please try again.', 'error');
                }
            });
        }

        // ===== Analytics Export =====
        exportAnalytics(event) {
            event.preventDefault();
            
            const button = $(event.currentTarget);
            const format = button.data('format') || 'json';
            const dateRange = button.data('date-range') || '30 days';
            
            this.showLoading(button);
            
            $.ajax({
                url: this.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'coachproai_export_analytics',
                    format: format,
                    date_range: dateRange,
                    nonce: this.nonce
                },
                success: (response) => {
                    this.hideLoading(button);
                    
                    if (response.success) {
                        if (format === 'csv') {
                            this.downloadCSV(response.data.data, response.data.filename);
                        } else {
                            this.downloadJSON(response.data, 'coachproai-analytics.json');
                        }
                        this.showNotice('Analytics exported successfully!', 'success');
                    } else {
                        this.showNotice(response.data || 'Failed to export analytics.', 'error');
                    }
                },
                error: () => {
                    this.hideLoading(button);
                    this.showNotice('Network error. Please try again.', 'error');
                }
            });
        }

        // ===== Dashboard Refresh =====
        refreshDashboard(event) {
            event.preventDefault();
            
            const button = $(event.currentTarget);
            const icon = button.find('.dashicons');
            
            this.showLoading(button);
            icon.addClass('rotating');
            
            $.ajax({
                url: this.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'coachproai_get_dashboard_stats',
                    nonce: this.nonce
                },
                success: (response) => {
                    this.hideLoading(button);
                    icon.removeClass('rotating');
                    
                    if (response.success) {
                        this.updateDashboardStats(response.data);
                        this.showNotice('Dashboard updated!', 'success');
                    } else {
                        this.showNotice('Failed to refresh dashboard.', 'error');
                    }
                },
                error: () => {
                    this.hideLoading(button);
                    icon.removeClass('rotating');
                    this.showNotice('Network error. Please try again.', 'error');
                }
            });
        }

        updateDashboardStats(stats) {
            // Update stat cards
            $('.coachproai-stat-card').each(function() {
                const card = $(this);
                const statType = card.data('stat');
                const value = stats[statType];
                
                if (value !== undefined) {
                    card.find('.coachproai-stat-value').text(value);
                    
                    // Update change indicator
                    const change = card.find('.coachproai-stat-change');
                    if (change.length && stats[`${statType}_change`] !== undefined) {
                        const changeValue = stats[`${statType}_change`];
                        change.text(changeValue > 0 ? `+${changeValue}%` : `${changeValue}%`)
                              .removeClass('positive negative')
                              .addClass(changeValue > 0 ? 'positive' : 'negative');
                    }
                }
            });

            // Update charts
            this.updateCharts(stats);
        }

        updateCharts(stats) {
            // Update enrollment chart
            if (stats.enrollment_data) {
                const chart = Chart.getChart('enrollment-chart');
                if (chart) {
                    chart.data.labels = stats.enrollment_data.labels;
                    chart.data.datasets[0].data = stats.enrollment_data.values;
                    chart.update();
                }
            }

            // Update progress chart
            if (stats.progress_data) {
                const chart = Chart.getChart('progress-chart');
                if (chart) {
                    chart.data.datasets[0].data = stats.progress_data;
                    chart.update();
                }
            }
        }

        // ===== Program Builder =====
        addLesson(event) {
            event.preventDefault();
            
            const module = $(event.currentTarget).closest('.module');
            const lessonsContainer = module.find('.lessons-container');
            
            // Add new lesson row
            const lessonHtml = `
                <div class="lesson-row">
                    <input type="text" name="lessons[]" placeholder="Lesson Title" class="lesson-title">
                    <textarea name="lesson_content[]" placeholder="Lesson Content" class="lesson-content"></textarea>
                    <input type="number" name="lesson_duration[]" placeholder="Duration (minutes)" class="lesson-duration">
                    <button type="button" class="button button-small" data-action="remove-lesson">Remove</button>
                </div>
            `;
            
            lessonsContainer.append(lessonHtml);
        }

        addModule(event) {
            event.preventDefault();
            
            const container = $('.program-modules');
            const moduleCount = container.find('.module').length;
            
            const moduleHtml = `
                <div class="module">
                    <h4>Module ${moduleCount + 1}</h4>
                    <input type="text" name="modules[]" placeholder="Module Title" class="module-title">
                    <textarea name="module_description[]" placeholder="Module Description" class="module-description"></textarea>
                    <div class="lessons-container">
                        <h5>Lessons</h5>
                        <button type="button" class="button button-secondary" data-action="add-lesson">Add Lesson</button>
                    </div>
                    <button type="button" class="button button-link-delete" data-action="remove-module">Remove Module</button>
                </div>
            `;
            
            container.append(moduleHtml);
        }

        reorderItems(event) {
            event.preventDefault();
            
            const container = $(event.currentTarget).data('container');
            const items = $(container).sortable('toArray');
            
            // Send reorder data to server
            $.ajax({
                url: this.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'coachproai_reorder_items',
                    items: items,
                    container: container,
                    nonce: this.nonce
                },
                success: (response) => {
                    if (response.success) {
                        this.showNotice('Items reordered successfully!', 'success');
                    } else {
                        this.showNotice('Failed to reorder items.', 'error');
                    }
                }
            });
        }

        // ===== Utility Functions =====
        showLoading(element) {
            element.addClass('loading').prop('disabled', true);
        }

        hideLoading(element) {
            element.removeClass('loading').prop('disabled', false);
        }

        showNotice(message, type = 'info') {
            const noticeHtml = `
                <div class="coachproai-notice ${type}">
                    <div class="coachproai-notice-title">${type.charAt(0).toUpperCase() + type.slice(1)}</div>
                    <div class="coachproai-notice-message">${message}</div>
                </div>
            `;
            
            $('.coachproai-admin-wrap').prepend(noticeHtml);
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                $('.coachproai-notice').fadeOut(() => $(this).remove());
            }, 5000);
        }

        downloadCSV(data, filename) {
            const blob = new Blob([data], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            a.click();
            window.URL.revokeObjectURL(url);
        }

        downloadJSON(data, filename) {
            const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            a.click();
            window.URL.revokeObjectURL(url);
        }
    }

    // Initialize admin when document is ready
    $(document).ready(function() {
        window.CoachProAI.admin = new CoachProAIAdmin();
        
        // Add rotating class to CSS
        if (!$('#coachproai-admin-styles').length) {
            $('<style id="coachproai-admin-styles">')
                .text('.rotating { animation: rotate 1s linear infinite; } @keyframes rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }')
                .appendTo('head');
        }
    });

})(jQuery);