<?php

use Elementor\Plugin as Elementor;

class Houzez_Library
{
	public function __construct()
	{
		$this->hooks();
		$this->register_templates_source();
	}

	public function hooks()
	{
		add_action('elementor/editor/after_enqueue_scripts', array($this, 'enqueue'));
		add_action('elementor/editor/after_enqueue_styles', array($this, 'editor_inline_styles'));
		add_action('elementor/editor/footer', array($this, 'render'));
		add_action('elementor/frontend/before_enqueue_styles', array($this, 'inline_styles'));
	}

	public function editor_inline_styles()
	{
		// Preview links point to studio.houzez.co/templates/<slug>/ which 404 on
		// our self-hosted catalog. Hide until we ship a local preview gallery.
		?>
		<style>
		#houzez-library-modal .elementor-template-library-template-preview { display: none !important; }
		</style>
		<?php
	}

	public function inline_styles()
	{
	?>
		<style>
		.houzez-library-modal-btn {margin-left: 5px;background: #35AAE1;vertical-align: top;font-size: 0 !important;}
		.houzez-library-modal-btn:before {content: '';width: 16px;height: 16px;background-image: url('<?php echo get_template_directory_uri() . '/img/studio-icon.png';?>');background-position: center;background-size: contain;background-repeat: no-repeat;}
		#houzez-library-modal .houzez-elementor-template-library-template-name {text-align: right;flex: 1 0 0%;}
		.houzez-notice {padding: 10px 15px;margin: 10px 0;border-radius: 4px;font-size: 14px;}
		.houzez-notice.houzez-success {background: #d4edda;color: #155724;border: 1px solid #c3e6cb;}
		.houzez-notice.houzez-error {background: #f8d7da;color: #721c24;border: 1px solid #f5c6cb;}
		.houzez-notice.houzez-info {background: #d1ecf1;color: #0c5460;border: 1px solid #bee5eb;}
		</style>
	<?php
	}

	public function register_templates_source()
	{
		Elementor::instance()->templates_manager->register_source('Houzez_Library_Source');
	}

	public function enqueue()
	{
		// Version bumped to 1.2.0-es so customer browser caches reload the
		// patched URLs (was 1.0.1 hardcoded to studio.houzez.co).
		wp_enqueue_script('houzez-blocks', get_template_directory_uri() . '/inc/blocks/assets/js/blocks-templates.js', array('jquery'), '1.2.0-es', true);

		// EstateSite-native source: when the EstateSite Templates class is
		// loaded (from estatesite-wpelementor), point the library JS at our
		// own REST endpoints instead of studio.houzez.co. This keeps the
		// modal fully self-hosted — no client-side calls to 3rd-party servers.
		$json_base_url      = '';
		$use_json_files     = false;
		$json_version       = (string) time();
		$all_templates_url  = '';
		$template_url_template = '';
		$proxy_url          = '';
		$nonce_x            = '';

		if ( class_exists( '\\EstateSite\\Elementor\\Templates' ) ) {
			$all_templates_url     = \EstateSite\Elementor\Templates::manifest_url();
			$json_base_url         = \EstateSite\Elementor\Templates::library_url() . '/by-slug/';
			$template_url_template = \EstateSite\Elementor\Templates::library_url() . '/by-slug/{slug}';
			$proxy_url             = \EstateSite\Elementor\Templates::proxy_endpoint_url();
			$nonce_x               = wp_create_nonce( 'wp_rest' );
			$use_json_files        = true;
		}
		// Fallback for sites still running favethemes-api (Houzez compat).
		elseif (function_exists('favethemes_get_json_directory_url')) {
			$json_base_url = favethemes_get_json_directory_url();

			if (function_exists('favethemes_get_json_stats')) {
				$json_stats = favethemes_get_json_stats();
				$use_json_files = $json_stats['all_templates_exists'];

				if (isset($json_stats['last_generated']) && $json_stats['last_generated']) {
					$json_version = $json_stats['last_generated'];
				}
			}
			$all_templates_url = $use_json_files ? $json_base_url . 'all-templates.json' : '';
		}

		// Add localization for AJAX
		wp_localize_script('houzez-blocks', 'houzez_library_ajax', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('houzez_library_nonce'),
			'is_activated' => houzez_is_license_activated(),
			'license_url' => admin_url('admin.php?page=favethemes-license'),
			'proxy_url' => $proxy_url,
			'nonce_x'   => $nonce_x,
			'json_files' => array(
				'enabled' => $use_json_files,
				'base_url' => $json_base_url,
				'all_templates_url' => $all_templates_url,
				// EstateSite-specific: full URL template for per-template fetches
				// (the upstream Houzez JS uses base_url + slug + '.json' which we
				// don't match; the patched JS will use this instead).
				'template_url_template' => $template_url_template,
				'version' => $json_version,
			),
		));
	}

	/**
	 * Admin page for template management
	 */
	public static function admin_page() {
		$last_sync = Houzez_Library_Source::get_last_sync_time();
		$templates = get_option('houzez_local_templates', []);
		$template_count = isset($templates['elements']) ? count($templates['elements']) : 0;
		?>
		<div class="wrap houzez-template-library">
			<div class="houzez-header">
				<div class="houzez-header-content">
					<div class="houzez-logo">
						<!-- <img src="<?php echo get_template_directory_uri() . '/img/logo.png'; ?>" alt="Houzez" style="height: 40px;"> -->
						<h1><?php _e('Template Library Management', 'houzez'); ?></h1>
					</div>
					<div class="houzez-header-actions">
						<button type="button" id="sync-templates-btn" class="houzez-btn houzez-btn-primary">
							<i class="dashicons dashicons-update"></i>
							<?php _e('Sync Templates', 'houzez'); ?>
						</button>
						<button type="button" id="clear-templates-btn" class="houzez-btn houzez-btn-secondary">
							<i class="dashicons dashicons-trash"></i>
							<?php _e('Clear Cache', 'houzez'); ?>
						</button>
					</div>
				</div>
			</div>

			<div class="houzez-dashboard">
				<!-- Quick Stats -->
				<div class="houzez-stats-grid">
					<div class="houzez-stat-card">
						<div class="houzez-stat-icon">
							<i class="dashicons dashicons-admin-page"></i>
						</div>
						<div class="houzez-stat-content">
							<h3><?php echo $template_count; ?></h3>
							<p><?php _e('Templates Available', 'houzez'); ?></p>
						</div>
					</div>

					<div class="houzez-stat-card">
						<div class="houzez-stat-icon">
							<i class="dashicons dashicons-clock"></i>
						</div>
						<div class="houzez-stat-content">
							<h3>
								<?php if ($last_sync): ?>
									<?php echo human_time_diff($last_sync, current_time('timestamp')); ?> ago
								<?php else: ?>
									<?php _e('Never', 'houzez'); ?>
								<?php endif; ?>
							</h3>
							<p><?php _e('Last Sync', 'houzez'); ?></p>
						</div>
					</div>

					<div class="houzez-stat-card">
						<div class="houzez-stat-icon">
							<i class="dashicons dashicons-performance"></i>
						</div>
						<div class="houzez-stat-content">
							<h3><?php echo $template_count > 0 ? 'Local' : 'Remote'; ?></h3>
							<p><?php _e('Storage Mode', 'houzez'); ?></p>
						</div>
					</div>
				</div>

				<!-- Main Actions -->
				<div class="houzez-main-card">
					<div class="houzez-card-header">
						<h2>
							<i class="dashicons dashicons-admin-tools"></i>
							<?php _e('Template Management', 'houzez'); ?>
						</h2>
						<div class="houzez-status-badge <?php echo $template_count > 0 ? 'houzez-status-success' : 'houzez-status-warning'; ?>">
							<?php echo $template_count > 0 ? __('Active', 'houzez') : __('Inactive', 'houzez'); ?>
						</div>
					</div>
					<div class="houzez-card-body">
						<p class="houzez-description">
							<?php _e('Templates are stored locally for instant access in Elementor. Sync manually when needed.', 'houzez'); ?>
						</p>
						
						<div class="houzez-actions">
							<div class="houzez-action">
								<div class="houzez-action-icon">
									<i class="dashicons dashicons-download"></i>
								</div>
								<div class="houzez-action-content">
									<h4><?php _e('Sync Templates', 'houzez'); ?></h4>
									<p><?php _e('Download latest templates from studio.houzez.co', 'houzez'); ?></p>
									<button type="button" id="sync-action-btn" class="houzez-btn houzez-btn-outline">
										<?php _e('Start Sync', 'houzez'); ?>
									</button>
								</div>
							</div>

							<div class="houzez-action">
								<div class="houzez-action-icon houzez-icon-danger">
									<i class="dashicons dashicons-database-remove"></i>
								</div>
								<div class="houzez-action-content">
									<h4><?php _e('Clear Templates', 'houzez'); ?></h4>
									<p><?php _e('Remove all locally stored templates', 'houzez'); ?></p>
									<button type="button" id="clear-action-btn" class="houzez-btn houzez-btn-outline houzez-btn-danger">
										<?php _e('Clear All', 'houzez'); ?>
									</button>
								</div>
							</div>
						</div>

						<!-- Performance Info -->
						<div class="houzez-performance-info">
							<div class="houzez-perf-item">
								<strong><?php _e('Performance:', 'houzez'); ?></strong>
								<?php if ($template_count > 0): ?>
									<span class="houzez-success"><?php _e('Optimized - Instant loading', 'houzez'); ?></span>
								<?php else: ?>
									<span class="houzez-warning"><?php _e('Not optimized - Remote loading', 'houzez'); ?></span>
								<?php endif; ?>
							</div>
							<div class="houzez-perf-item">
								<strong><?php _e('API Calls:', 'houzez'); ?></strong>
								<span><?php echo $template_count > 0 ? '0 per request' : '315+ per request'; ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Progress Modal -->
			<div id="houzez-progress-modal" class="houzez-modal" style="display: none;">
				<div class="houzez-modal-content">
					<div class="houzez-modal-header">
						<h3 id="houzez-modal-title"><?php _e('Processing...', 'houzez'); ?></h3>
					</div>
					<div class="houzez-modal-body">
						<div class="houzez-progress-bar small">
							<div class="houzez-progress-fill default animated" style="width: 0%;"></div>
						</div>
						<div class="houzez-progress-text">
							<span id="houzez-progress-message"><?php _e('Initializing...', 'houzez'); ?></span>
							<span id="houzez-progress-percentage">0%</span>
						</div>
						<div id="houzez-progress-details" class="houzez-progress-details"></div>
					</div>
				</div>
			</div>

			<!-- Notifications -->
			<div id="houzez-notifications" class="houzez-notifications"></div>
		</div>

		<script>
		jQuery(document).ready(function($) {
			// Notification system
			function showNotification(message, type = 'info') {
				const notification = $(`
					<div class="houzez-notification ${type}">
						${message}
					</div>
				`);
				
				$('#houzez-notifications').append(notification);
				
				setTimeout(() => {
					notification.fadeOut(() => notification.remove());
				}, 5000);
			}

			// Progress modal functions
			function showProgressModal(title = 'Processing...') {
				$('#houzez-modal-title').text(title);
				$('#houzez-progress-modal').fadeIn();
				updateProgress(0, 'Initializing...');
			}

			function hideProgressModal() {
				$('#houzez-progress-modal').fadeOut();
			}

			function updateProgress(percentage, message, details = '') {
				$('.houzez-progress-fill').css('width', percentage + '%');
				$('#houzez-progress-percentage').text(Math.round(percentage) + '%');
				$('#houzez-progress-message').text(message);
				$('#houzez-progress-details').html(details);
			}

			// Sync templates functionality
			function handleSync() {
				showProgressModal('Syncing Templates');
				
				// Check template count to decide sync method
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'houzez_get_sync_status',
						nonce: '<?php echo wp_create_nonce('houzez_library_nonce'); ?>'
					},
					success: function(response) {
						if (response.success && response.data.template_count > 100) {
							startChunkedSync();
						} else {
							startRegularSync();
						}
					},
					error: function() {
						startRegularSync();
					}
				});
			}

			function startRegularSync() {
				updateProgress(10, 'Connecting to studio.houzez.co...');
				
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'houzez_sync_templates',
						nonce: '<?php echo wp_create_nonce('houzez_library_nonce'); ?>'
					},
					success: function(response) {
						if (response.success) {
							let details = '';
							
							if (response.data.hybrid) {
								// Hybrid sync results
								details = `<strong>Hybrid Sync Results:</strong><br>`;
								details += `Bulk Download: ${response.data.downloaded_bulk || 0} templates<br>`;
								details += `Individual Download: ${response.data.downloaded_individual || 0} templates<br>`;
								details += `Total Downloaded: ${response.data.downloaded || 0} templates<br>`;
								details += `Failed: ${response.data.failed || 0}<br>`;
								details += `API calls: ${response.data.api_calls || 0}`;
								
								if (response.data.cached) {
									details += `<br><span style="color: #059669; font-weight: bold;">✓ Bulk used server cache</span>`;
								}
								
								updateProgress(100, 'Hybrid sync completed!', details);
							} else {
								// Regular sync results
								details = `Downloaded: ${response.data.downloaded || 0} templates`;
								if (response.data.api_calls) {
									details += `<br>API calls: ${response.data.api_calls}`;
								}
								if (response.data.cached) {
									details += `<br><span style="color: #059669; font-weight: bold;">✓ Using server cache (lightning fast!)</span>`;
								}
								if (response.data.api_calls === 2) {
									details += `<br><span style="color: #059669;">✓ Bulk sync successful (2 calls vs 333!)</span>`;
								}
								
								updateProgress(100, 'Sync completed!', details);
							}
							
							setTimeout(() => {
								hideProgressModal();
								let message = 'Templates synced successfully!';
								if (response.data.hybrid) {
									message += ` (${response.data.downloaded} total templates)`;
								} else if (response.data.cached) {
									message += ' (Used server cache)';
								}
								showNotification(message, 'success');
								setTimeout(() => location.reload(), 1500);
							}, 2000);
						} else {
							if (response.data && response.data.use_chunked) {
								updateProgress(20, 'Large template set detected. Switching to chunked sync...');
								setTimeout(startChunkedSync, 1000);
							} else {
								hideProgressModal();
								showNotification(response.data.message || 'Sync failed. Please try again.', 'error');
							}
						}
					},
					error: function() {
						hideProgressModal();
						showNotification('Sync failed. Please check your connection.', 'error');
					}
				});
			}

			function startChunkedSync() {
				let chunkIndex = 0;
				let totalChunks = 0;
				let totalApiCalls = 0;
				
				function processChunk() {
					$.ajax({
						url: ajaxurl,
						type: 'POST',
						data: {
							action: 'houzez_sync_chunked',
							nonce: '<?php echo wp_create_nonce('houzez_library_nonce'); ?>',
							chunk_size: 10,
							chunk_index: chunkIndex
						},
						success: function(response) {
							if (response.success) {
								const data = response.data;
								totalChunks = data.total_chunks;
								
								if (data.api_calls) {
									totalApiCalls += data.api_calls;
								}
								
								const progress = Math.round((chunkIndex + 1) / totalChunks * 100);
								const message = `Processing chunk ${chunkIndex + 1} of ${totalChunks}`;
								let details = `Progress: ${progress}%`;
								if (totalApiCalls > 0) {
									details += `<br>API calls: ${totalApiCalls}`;
								}
								
								updateProgress(progress, message, details);
								
								if (data.completed) {
									updateProgress(100, 'Chunked sync completed!', 
										`Total API calls: ${totalApiCalls}<br>All templates downloaded successfully`);
									
									setTimeout(() => {
										hideProgressModal();
										showNotification('Templates synced successfully!', 'success');
										setTimeout(() => location.reload(), 1500);
									}, 2000);
								} else {
									chunkIndex++;
									setTimeout(processChunk, 2000);
								}
							} else {
								hideProgressModal();
								showNotification('Chunk sync failed: ' + response.data.message, 'error');
							}
						},
						error: function() {
							hideProgressModal();
							showNotification('Chunk sync failed. Please try again.', 'error');
						}
					});
				}
				
				processChunk();
			}

			// Clear templates functionality
			function handleClear() {
				if (!confirm('Are you sure you want to clear all local templates? This will impact performance until templates are synced again.')) {
					return;
				}
				
				showProgressModal('Clearing Templates');
				updateProgress(50, 'Removing local templates...');
				
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'houzez_clear_templates',
						nonce: '<?php echo wp_create_nonce('houzez_library_nonce'); ?>'
					},
					success: function(response) {
						if (response.success) {
							updateProgress(100, 'Templates cleared successfully!');
							setTimeout(() => {
								hideProgressModal();
								showNotification('Local templates cleared successfully.', 'success');
								setTimeout(() => location.reload(), 1500);
							}, 1000);
						} else {
							hideProgressModal();
							showNotification(response.data.message || 'Failed to clear templates.', 'error');
						}
					},
					error: function() {
						hideProgressModal();
						showNotification('Clear operation failed. Please try again.', 'error');
					}
				});
			}

			// Event handlers
			$('#sync-templates-btn, #sync-action-btn').on('click', handleSync);
			$('#clear-templates-btn, #clear-action-btn').on('click', handleClear);

			// Close modal on outside click
			$('#houzez-progress-modal').on('click', function(e) {
				if (e.target === this) {
					hideProgressModal();
				}
			});

			// Add loading states to buttons
			$(document).on('ajaxStart', function() {
				$('.houzez-btn').prop('disabled', true);
			}).on('ajaxStop', function() {
				$('.houzez-btn').prop('disabled', false);
			});

			// Animate stats on page load
			$('.houzez-stat-card').each(function(index) {
				$(this).css('opacity', '0').delay(index * 100).animate({
					opacity: 1
				}, 500);
			});

			// Animate cards on page load
			$('.houzez-card').each(function(index) {
				$(this).css('opacity', '0').delay((index + 4) * 100).animate({
					opacity: 1
				}, 500);
			});
		});
		</script>
		<?php
	}

	public function render()
	{
	?>
		<script type="text/html" id="tmpl-elementor-houzez-library-modal-header">
			<div class="elementor-templates-modal__header">
				<div class="elementor-templates-modal__header__logo-area">
					<div class="elementor-templates-modal__header__logo">
						<span class="elementor-templates-modal__header__logo__title">
							Houzez Library
						</span>
					</div>
				</div>

				<div class="elementor-templates-modal__header__menu-area">
					<div id="elementor-houzez-library-header-menu">
						<div id="houzez-tab-block" class="elementor-component-tab elementor-template-library-menu-item elementor-active" data-tab="block">Blocks</div>
						<div id="houzez-tab-template" class="elementor-component-tab elementor-template-library-menu-item" data-tab="template">Pages</div>
					</div>
				</div>

				<div class="elementor-templates-modal__header__items-area">
					<div class="elementor-templates-modal__header__close elementor-templates-modal__header__close--normal elementor-templates-modal__header__item">
						<i class="eicon-close" aria-hidden="true" title="<?php echo esc_html__('Close', 'houzez'); ?>"></i>

						<span class="elementor-screen-only">
							<?php echo esc_html__('Close', 'houzez'); ?>
						</span>
					</div>
				</div>
			</div>
		</script>

		<script type="text/html" id="tmpl-elementor-houzez-library-modal-order">
			<div id="elementor-template-library-filter">
				<select id="elementor-template-library-filter-subtype" class="elementor-template-library-filter-select" data-elementor-filter="subtype">
					<option value="all"><?php echo esc_html__('All', 'houzez'); ?></option>
					<# data.tags.forEach(function(item, i) { #>
						<option value="{{{item.slug}}}">{{{item.title}}}</option>
						<# }); #>
				</select>
			</div>
		</script>

		<script type="text/template" id="tmpl-elementor-houzez-library-header-menu">
			<# jQuery.each( tabs, ( tab, args ) => { #>	
				<div class="elementor-component-tab elementor-template-library-menu-item" data-tab="{{{ tab }}}">{{{ args.title }}}</div>
			<# } ); #>
		</script>

		<script type="text/html" id="tmpl-elementor-houzez-library-modal">
			<div id="elementor-template-library-templates" data-template-source="remote">
				<div id="elementor-template-library-toolbar">
					<div id="elementor-template-library-filter-toolbar-remote" class="elementor-template-library-filter-toolbar"></div>

					<div id="elementor-template-library-filter-text-wrapper">
						<label for="elementor-template-library-filter-text" class="elementor-screen-only"><?php echo esc_html__('Search Templates:', 'houzez'); ?></label>
						<input id="elementor-template-library-filter-text" placeholder="<?php echo esc_attr__('Search', 'houzez'); ?>">
						<i class="eicon-search"></i>
					</div>
				</div>

				<div id="elementor-template-library-templates-container"></div>

				<div id="elementor-template-library-footer-banner">
					<img class="elementor-nerd-box-icon" src="<?php echo get_bloginfo('url'); ?>/wp-content/plugins/elementor/assets/images/information.svg">
					<div class="elementor-excerpt">Templates loaded from local storage. Use sync button to update.</div>
				</div>
			</div>

			<div class="elementor-loader-wrapper" style="display: none">
				<div class="elementor-loader">
					<div class="elementor-loader-boxes">
						<div class="elementor-loader-box"></div>
						<div class="elementor-loader-box"></div>
						<div class="elementor-loader-box"></div>
						<div class="elementor-loader-box"></div>
					</div>
				</div>
				<div class="elementor-loading-title"><?php echo esc_html__('Loading', 'houzez'); ?></div>
			</div>
		</script>

		<script type="text/html" id="tmpl-elementor-houzez-library-modal-item">
			<# data.elements.forEach(function(item, i) { #>
				
				<div class="elementor-template-library-template elementor-template-library-template-remote elementor-template-library-template-{{{item.type === 'template' ? 'page' : 'block'}}}" data-slug="{{{item.slug}}}" data-tag="{{{item.category}}}" data-type="{{{item.type}}}" data-name="{{{item.title}}}">
						
					<div class="elementor-template-library-template-body">
						<# if (item.type === 'block') { #>
							<img src="{{{item.image}}}">
						<# } else { #>
						<div class="elementor-template-library-template-screenshot" style="background-image: url({{{item.image}}})"></div>
						<# } #>

						<a class="elementor-template-library-template-preview" href="{{{item.link}}}" target="_blank">
							<i class="eicon-zoom-in-bold" aria-hidden="true"></i>
						</a>
					</div>

					<div class="elementor-template-library-template-footer">
						<a class="elementor-template-library-template-action elementor-template-library-template-insert elementor-button" data-id="{{{item.id}}}">
							<i class="eicon-file-download" aria-hidden="true"></i>
							<span class="elementor-button-title">Insert</span>
						</a>
						<div class="houzez-elementor-template-library-template-name">{{{item.title}}}</div>
					</div>
				</div>
				<# }); #>
		</script>

		<script type="text/html" id="tmpl-elementor-houzez-library-license-required">
			<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:50px 40px;min-height:560px;background:#fff;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">

				<!-- Top visual: 3 template preview cards with blurred overlay -->
				<div style="position:relative;margin-bottom:36px;">
					<div style="display:flex;gap:14px;filter:blur(1px);opacity:0.5;">
						<div style="width:150px;height:100px;background:linear-gradient(135deg,#e8f0fe 0%,#d4e4fc 100%);border-radius:10px;border:1px solid #d1dce8;position:relative;overflow:hidden;">
							<div style="height:30px;background:linear-gradient(90deg,#1e3a5f,#2a5280);"></div>
							<div style="padding:10px 12px;display:flex;flex-direction:column;gap:5px;">
								<div style="height:5px;background:#c5d3e3;border-radius:3px;width:80%;"></div>
								<div style="height:5px;background:#c5d3e3;border-radius:3px;width:55%;"></div>
							</div>
						</div>
						<div style="width:150px;height:100px;background:linear-gradient(135deg,#e8f0fe 0%,#d4e4fc 100%);border-radius:10px;border:1px solid #d1dce8;position:relative;overflow:hidden;">
							<div style="height:30px;background:linear-gradient(90deg,#2a5280,#35AAE1);"></div>
							<div style="padding:10px 12px;display:flex;flex-direction:column;gap:5px;">
								<div style="height:5px;background:#c5d3e3;border-radius:3px;width:70%;"></div>
								<div style="height:5px;background:#c5d3e3;border-radius:3px;width:90%;"></div>
							</div>
						</div>
						<div style="width:150px;height:100px;background:linear-gradient(135deg,#e8f0fe 0%,#d4e4fc 100%);border-radius:10px;border:1px solid #d1dce8;position:relative;overflow:hidden;">
							<div style="height:30px;background:linear-gradient(90deg,#1a3550,#1e3a5f);"></div>
							<div style="padding:10px 12px;display:flex;flex-direction:column;gap:5px;">
								<div style="height:5px;background:#c5d3e3;border-radius:3px;width:60%;"></div>
								<div style="height:5px;background:#c5d3e3;border-radius:3px;width:75%;"></div>
							</div>
						</div>
					</div>
					<!-- Lock overlay on top of cards -->
					<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
						<div style="width:52px;height:52px;background:#1e3a5f;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(30,58,95,0.35);">
							<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
								<path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
							</svg>
						</div>
					</div>
				</div>

				<!-- Content -->
				<h2 style="margin:0 0 10px;font-size:22px;font-weight:700;color:#111827;text-align:center;letter-spacing:-0.2px;">Activate Your License to Unlock Studio</h2>
				<p style="margin:0 0 32px;font-size:14px;color:#6b7280;text-align:center;line-height:1.65;max-width:440px;">
					Get instant access to 300+ professionally designed templates and blocks, built exclusively for Houzez real estate websites.
				</p>

				<!-- Feature pills -->
				<div style="display:flex;gap:10px;margin-bottom:36px;flex-wrap:wrap;justify-content:center;">
					<div style="display:flex;align-items:center;gap:7px;padding:8px 16px;background:#f1f5f9;border-radius:100px;font-size:13px;color:#334155;font-weight:500;">
						<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#1e3a5f" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="3" width="7" height="7" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect></svg>
						300+ Templates
					</div>
					<div style="display:flex;align-items:center;gap:7px;padding:8px 16px;background:#f1f5f9;border-radius:100px;font-size:13px;color:#334155;font-weight:500;">
						<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#1e3a5f" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
						One-Click Import
					</div>
					<div style="display:flex;align-items:center;gap:7px;padding:8px 16px;background:#f1f5f9;border-radius:100px;font-size:13px;color:#334155;font-weight:500;">
						<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#1e3a5f" stroke-width="2"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"></path></svg>
						Regular Updates
					</div>
				</div>

				<!-- CTA -->
				<a href="{{{data.license_url}}}" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:13px 36px;background:#1e3a5f;color:#ffffff;text-decoration:none;border-radius:8px;font-size:14px;font-weight:600;transition:all 0.2s;box-shadow:0 1px 3px rgba(30,58,95,0.25),0 4px 12px rgba(30,58,95,0.12);" onmouseover="this.style.background='#2a5280';this.style.transform='translateY(-1px)';this.style.boxShadow='0 2px 6px rgba(30,58,95,0.3),0 8px 20px rgba(30,58,95,0.18)'" onmouseout="this.style.background='#1e3a5f';this.style.transform='translateY(0)';this.style.boxShadow='0 1px 3px rgba(30,58,95,0.25),0 4px 12px rgba(30,58,95,0.12)'">
					Activate License
				</a>

				<p style="margin:14px 0 0;font-size:12px;color:#9ca3af;">
					Already purchased? <a href="{{{data.license_url}}}" target="_blank" style="color:#1e3a5f;text-decoration:none;font-weight:500;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Go to License Settings</a>
				</p>
			</div>
		</script>
<?php
	}
}
