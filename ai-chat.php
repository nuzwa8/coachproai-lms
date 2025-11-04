<?php
/**
 * CoachProAI AI Chat Template
 *
 * @package CoachProAI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shortcodes = \CoachProAI\Shortcodes::instance();
$coaches = $shortcodes->get_available_coaches();
$selected_coach = ! empty( $atts['coach_id'] ) ? $atts['coach_id'] : '';
?>

<div class="coachproai-ai-chat-container" style="width: <?php echo esc_attr( $atts['width'] ); ?>; height: <?php echo esc_attr( $atts['height'] ); ?>">
	<div class="coachproai-chat-header">
		<h3><?php _e( 'AI Coaching Chat', 'coachproai-lms' ); ?></h3>
		<div class="coachproai-coach-selector">
			<?php echo $shortcodes->get_coach_dropdown( $selected_coach ); ?>
		</div>
	</div>
	
	<div class="coachproai-chat-messages">
		<div class="coachproai-welcome-message">
			<div class="coachproai-message ai">
				<div class="coachproai-message-content">
					<?php _e( 'Welcome to AI Coaching! Select a coach and start your session.', 'coachproai-lms' ); ?>
				</div>
			</div>
		</div>
	</div>
	
	<div class="coachproai-chat-input">
		<textarea 
			data-field="chat-message" 
			placeholder="<?php esc_attr_e( 'Type your message...', 'coachproai-lms' ); ?>" 
			disabled
		></textarea>
		<button data-action="send-message" disabled>
			<?php _e( 'Send', 'coachproai-lms' ); ?>
		</button>
	</div>
	
	<div class="coachproai-chat-actions">
		<button data-action="start-ai-chat" class="coachproai-btn coachproai-btn-primary">
			<?php _e( 'Start Coaching Session', 'coachproai-lms' ); ?>
		</button>
		<button data-action="end-session" class="coachproai-btn coachproai-btn-secondary" style="display: none;">
			<?php _e( 'End Session', 'coachproai-lms' ); ?>
		</button>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const chatContainer = document.querySelector('.coachproai-ai-chat-container');
	const coachSelector = chatContainer.querySelector('.coachproai-coach-select');
	const startButton = chatContainer.querySelector('[data-action="start-ai-chat"]');
	const endButton = chatContainer.querySelector('[data-action="end-session"]');
	const messageInput = chatContainer.querySelector('[data-field="chat-message"]');
	const sendButton = chatContainer.querySelector('[data-action="send-message"]');
	
	// Enable/disable controls based on coach selection
	coachSelector.addEventListener('change', function() {
		const isSelected = this.value !== '';
		startButton.disabled = !isSelected;
	});
	
	// Session management
	let currentSessionId = null;
	
	startButton.addEventListener('click', function() {
		const coachId = coachSelector.value;
		if (!coachId) {
			alert('Please select a coach first.');
			return;
		}
		
		startChatSession(coachId);
	});
	
	endButton.addEventListener('click', function() {
		endChatSession();
	});
	
	function startChatSession(coachId) {
		const sessionType = 'general'; // This could be dynamic based on context
		
		startButton.disabled = true;
		startButton.textContent = 'Starting...';
		
		// AJAX call to start session
		fetch(ajaxurl, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			body: new URLSearchParams({
				action: 'coachproai_start_ai_session',
				coach_id: coachId,
				session_type: sessionType,
				nonce: coachproai_ajax.nonce
			})
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				currentSessionId = data.data.session_id;
				enableChatInterface();
				addMessage('ai', 'Welcome! I\'m here to help you with your coaching journey. How can I assist you today?');
			} else {
				alert('Failed to start session: ' + data.data);
			}
		})
		.catch(error => {
			console.error('Error:', error);
			alert('Network error. Please try again.');
		})
		.finally(() => {
			startButton.disabled = false;
			startButton.textContent = 'Start Coaching Session';
		});
	}
	
	function enableChatInterface() {
		messageInput.disabled = false;
		sendButton.disabled = false;
		endButton.style.display = 'inline-block';
		coachSelector.disabled = true;
		startButton.style.display = 'none';
	}
	
	function endChatSession() {
		currentSessionId = null;
		messageInput.disabled = true;
		sendButton.disabled = true;
		endButton.style.display = 'none';
		coachSelector.disabled = false;
		startButton.style.display = 'inline-block';
		
		// Clear messages
		const messagesContainer = chatContainer.querySelector('.coachproai-chat-messages');
		messagesContainer.innerHTML = `
			<div class="coachproai-welcome-message">
				<div class="coachproai-message ai">
					<div class="coachproai-message-content">
						Session ended. Select a coach to start a new session.
					</div>
				</div>
			</div>
		`;
	}
	
	// Send message functionality
	sendButton.addEventListener('click', sendMessage);
	messageInput.addEventListener('keypress', function(e) {
		if (e.key === 'Enter' && !e.shiftKey) {
			e.preventDefault();
			sendMessage();
		}
	});
	
	function sendMessage() {
		const message = messageInput.value.trim();
		if (!message || !currentSessionId) return;
		
		// Add user message
		addMessage('student', message);
		messageInput.value = '';
		
		// Show typing indicator
		showTypingIndicator();
		
		// Send to AI
		fetch(ajaxurl, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			body: new URLSearchParams({
				action: 'coachproai_send_message',
				session_id: currentSessionId,
				message: message,
				nonce: coachproai_ajax.nonce
			})
		})
		.then(response => response.json())
		.then(data => {
			hideTypingIndicator();
			if (data.success) {
				addMessage('ai', data.data.response);
			} else {
				addMessage('ai', 'Sorry, I encountered an error. Please try again.');
			}
		})
		.catch(error => {
			hideTypingIndicator();
			addMessage('ai', 'Network error. Please check your connection and try again.');
		});
	}
	
	function addMessage(sender, content) {
		const messagesContainer = chatContainer.querySelector('.coachproai-chat-messages');
		const timestamp = new Date().toLocaleTimeString();
		const messageHtml = `
			<div class="coachproai-message ${sender}">
				<div class="coachproai-message-content">${escapeHtml(content)}</div>
				<div class="coachproai-message-time">${timestamp}</div>
			</div>
		`;
		
		messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
		messagesContainer.scrollTop = messagesContainer.scrollHeight;
	}
	
	function showTypingIndicator() {
		const messagesContainer = chatContainer.querySelector('.coachproai-chat-messages');
		const indicatorHtml = `
			<div class="coachproai-message ai typing" id="typing-indicator">
				<div class="coachproai-message-content">
					<div class="coachproai-typing-dots">
						<span></span>
						<span></span>
						<span></span>
					</div>
				</div>
			</div>
		`;
		
		messagesContainer.insertAdjacentHTML('beforeend', indicatorHtml);
		messagesContainer.scrollTop = messagesContainer.scrollHeight;
	}
	
	function hideTypingIndicator() {
		const indicator = document.getElementById('typing-indicator');
		if (indicator) {
			indicator.remove();
		}
	}
	
	function escapeHtml(text) {
		const div = document.createElement('div');
		div.textContent = text;
		return div.innerHTML;
	}
});
</script>

<style>
.coachproai-typing-dots {
	display: flex;
	gap: 4px;
}

.coachproai-typing-dots span {
	width: 6px;
	height: 6px;
	background: #7f8c8d;
	border-radius: 50%;
	animation: typing 1.4s infinite;
}

.coachproai-typing-dots span:nth-child(2) {
	animation-delay: 0.2s;
}

.coachproai-typing-dots span:nth-child(3) {
	animation-delay: 0.4s;
}

@keyframes typing {
	0%, 60%, 100% {
		transform: translateY(0);
	}
	30% {
		transform: translateY(-10px);
	}
}

.coachproai-login-notice {
	background: #f8f9fa;
	padding: 20px;
	border-radius: 8px;
	text-align: center;
	border: 1px solid #dee2e6;
}

.coachproai-login-notice a {
	color: #3498db;
	text-decoration: none;
	font-weight: 500;
}

.coachproai-login-notice a:hover {
	text-decoration: underline;
}
</style>