<?php
class Houzez_Library_Source extends Elementor\TemplateLibrary\Source_Base 
{
	public function get_id()
	{
		return 'houzez';
	}

	public function get_title()
	{
		return esc_html__( 'Houzez', 'houzez' );
	}

	public function register_data() {
	}

	public function get_items( $args = [] ) {
		return $this->get_local_templates();
	}

	public function get_item( $template_id ) {
		$templates = $this->get_items();
		// Templates are stored in an indexed array by ID, not in 'elements'
		return isset($templates[ $template_id ]) ? $templates[ $template_id ] : null;
	}

	/**
	 * Get templates from local database and format them properly for Elementor
	 */
	private function get_local_templates() {
		$stored_data = get_option('houzez_local_templates', []);
		
		// Convert the elements array to an indexed array by template ID
		$templates = [];
		if (isset($stored_data['elements']) && is_array($stored_data['elements'])) {
			foreach ($stored_data['elements'] as $template) {
				if (isset($template['id'])) {
					$templates[$template['id']] = $template;
				}
			}
		}
		
		return $templates;
	}

	/**
	 * Get templates in batches for progressive loading in popup
	 * This method supports the Houzez Library popup progressive loading
	 */
	public function get_templates_batch($batch_index = 0, $batch_size = 50) {
		$stored_data = get_option('houzez_local_templates', []);
		
		if (!isset($stored_data['elements']) || !is_array($stored_data['elements'])) {
			return array(
				'elements' => [],
				'batch_info' => array(
					'current_batch' => $batch_index,
					'batch_size' => $batch_size,
					'total_templates' => 0,
					'total_batches' => 0,
					'has_more' => false
				)
			);
		}
		
		$all_templates = $stored_data['elements'];
		$total_templates = count($all_templates);
		$total_batches = ceil($total_templates / $batch_size);
		
		// Get the specific batch
		$start_index = $batch_index * $batch_size;
		$batch_templates = array_slice($all_templates, $start_index, $batch_size);
		
		return array(
			'elements' => $batch_templates,
			'tags' => $stored_data['tags'] ?? [],
			'batch_info' => array(
				'current_batch' => $batch_index,
				'batch_size' => $batch_size,
				'total_templates' => $total_templates,
				'total_batches' => $total_batches,
				'has_more' => ($batch_index + 1) < $total_batches,
				'next_batch' => ($batch_index + 1) < $total_batches ? $batch_index + 1 : null,
				'templates_in_batch' => count($batch_templates),
				'loaded_so_far' => min($start_index + $batch_size, $total_templates)
			)
		);
	}

	/**
	 * Get all templates but return them organized for progressive loading
	 * This maintains backward compatibility while enabling progressive loading
	 */
	public function get_templates_progressive() {
		$stored_data = get_option('houzez_local_templates', []);
		
		if (!isset($stored_data['elements']) || !is_array($stored_data['elements'])) {
			// If no local templates, get ALL templates from remote API for progressive loading
			return $this->get_remote_templates_all();
		}
		
		// Return first batch immediately, with info about remaining batches
		return $this->get_templates_batch(0, 50);
	}

	/**
	 * Get ALL templates from remote API for progressive loading (when no local templates)
	 */
	private function get_remote_templates_all() {
		$response = wp_remote_get(
			'https://studio.houzez.co/wp-json/favethemes-blocks/v1/templates?all=true',
			array(
				'headers' => array(
					'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
					'Accept'          => 'application/json, text/javascript, */*; q=0.01',
					'Accept-Language' => 'en-US,en;q=0.9',
					'Referer'         => 'https://houzez.co',
					'x-api-key'       => 'er454erte35dgfd4564dgfd45646dfgd4564dfg',
				),
				'sslverify' => true,
				'timeout' => 60,
			)
		);

		if (is_wp_error($response) || !is_array($response)) {
			return array(
				'elements' => [],
				'batch_info' => array(
					'current_batch' => 0,
					'batch_size' => 50,
					'total_templates' => 0,
					'total_batches' => 0,
					'has_more' => false,
					'source' => 'remote_error'
				)
			);
		}

		$data = json_decode(wp_remote_retrieve_body($response), true);
		
		if (json_last_error() !== JSON_ERROR_NONE || !isset($data['elements']) || !is_array($data['elements'])) {
			return array(
				'elements' => [],
				'batch_info' => array(
					'current_batch' => 0,
					'batch_size' => 50,
					'total_templates' => 0,
					'total_batches' => 0,
					'has_more' => false,
					'source' => 'remote_invalid'
				)
			);
		}

		// Process all templates for progressive display
		$all_templates = $data['elements'];
		$batch_size = 50;
		$total_templates = count($all_templates);
		$total_batches = ceil($total_templates / $batch_size);
		
		// Return first batch with info about all templates
		$first_batch = array_slice($all_templates, 0, $batch_size);

		return array(
			'elements' => $first_batch,
			'tags' => $data['tags'] ?? [],
			'batch_info' => array(
				'current_batch' => 0,
				'batch_size' => $batch_size,
				'total_templates' => $total_templates,
				'total_batches' => $total_batches,
				'has_more' => $total_batches > 1,
				'next_batch' => $total_batches > 1 ? 1 : null,
				'templates_in_batch' => count($first_batch),
				'loaded_so_far' => count($first_batch),
				'source' => 'remote_all',
				'all_templates' => $all_templates // Store all templates for progressive loading
			)
		);
	}

	/**
	 * Fallback: Get templates from remote API in batches (when no local templates)
	 */
	private function get_remote_templates_batch($batch_index = 0, $batch_size = 50) {
		// For remote batch loading, we use the paginated API endpoint
		$page = $batch_index + 1; // API pages start from 1
		
		$response = wp_remote_get(
			"https://studio.houzez.co/wp-json/favethemes-blocks/v1/templates?page={$page}&per_page={$batch_size}",
			array(
				'headers' => array(
					'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
					'Accept'          => 'application/json, text/javascript, */*; q=0.01',
					'Accept-Language' => 'en-US,en;q=0.9',
					'Referer'         => 'https://houzez.co',
					'x-api-key'       => 'er454erte35dgfd4564dgfd45646dfgd4564dfg',
				),
				'sslverify' => true,
				'timeout' => 60,
			)
		);

		if (is_wp_error($response) || !is_array($response)) {
			return array(
				'elements' => [],
				'batch_info' => array(
					'current_batch' => $batch_index,
					'batch_size' => $batch_size,
					'total_templates' => 0,
					'total_batches' => 0,
					'has_more' => false,
					'source' => 'remote_error'
				)
			);
		}

		$data = json_decode(wp_remote_retrieve_body($response), true);
		
		if (json_last_error() !== JSON_ERROR_NONE || !isset($data['elements'])) {
			return array(
				'elements' => [],
				'batch_info' => array(
					'current_batch' => $batch_index,
					'batch_size' => $batch_size,
					'total_templates' => 0,
					'total_batches' => 0,
					'has_more' => false,
					'source' => 'remote_invalid'
				)
			);
		}

		// Calculate batch info from pagination data
		$pagination = $data['pagination'] ?? [];
		$total_templates = $data['total_records'] ?? count($data['elements']);
		$total_batches = isset($pagination['total_pages']) ? $pagination['total_pages'] : 1;

		return array(
			'elements' => $data['elements'],
			'tags' => $data['tags'] ?? [],
			'batch_info' => array(
				'current_batch' => $batch_index,
				'batch_size' => $batch_size,
				'total_templates' => $total_templates,
				'total_batches' => $total_batches,
				'has_more' => isset($pagination['has_next']) ? $pagination['has_next'] : false,
				'next_batch' => isset($pagination['has_next']) && $pagination['has_next'] ? $batch_index + 1 : null,
				'templates_in_batch' => count($data['elements']),
				'loaded_so_far' => min(($batch_index + 1) * $batch_size, $total_templates),
				'source' => 'remote_paginated'
			)
		);
	}

	/**
	 * Get template content from JSON file or remote API
	 */
	private function get_template_content( $template_id )
	{
		// Priority 1: Try to load from JSON file (fastest - requires favethemes-api plugin)
		$json_template = $this->get_json_template_content($template_id);
		if ($json_template) {
			if (is_array($json_template)) {
				$json_template['cache_source'] = 'json_file';
				$json_template['api_cached'] = false;
			}
			return $json_template;
		}

		// Priority 2: Fallback to remote API
		$remote_template = $this->get_remote_template_content($template_id);
		if ($remote_template && is_array($remote_template)) {
			$remote_template['cache_source'] = 'remote_api';
		}
		return $remote_template;
	}

	/**
	 * Get template content from JSON file (requires favethemes-api plugin)
	 */
	private function get_json_template_content($template_id) {
		// Check if favethemes-api plugin functions are available
		if (!function_exists('favethemes_get_json_directory') || !function_exists('favethemes_get_json_stats')) {
			return false;
		}

		// Check if JSON files are enabled
		$json_stats = favethemes_get_json_stats();
		if (!$json_stats['all_templates_exists']) {
			return false;
		}

		// Get the template slug from all-templates.json
		$json_dir = favethemes_get_json_directory();
		$all_templates_file = $json_dir . 'all-templates.json';

		if (!file_exists($all_templates_file)) {
			return false;
		}

		// Load all templates to find the slug for this template ID
		$all_templates_content = file_get_contents($all_templates_file);
		$all_templates_data = json_decode($all_templates_content, true);

		if (json_last_error() !== JSON_ERROR_NONE || !isset($all_templates_data['elements'])) {
			return false;
		}

		// Find the template by ID to get its slug
		$template_slug = null;
		foreach ($all_templates_data['elements'] as $template) {
			if (isset($template['id']) && $template['id'] == $template_id) {
				$template_slug = $template['slug'];
				break;
			}
		}

		if (!$template_slug) {
			return false;
		}

		// Load the individual template JSON file
		$template_json_file = $json_dir . $template_slug . '.json';

		if (!file_exists($template_json_file)) {
			return false;
		}

		$template_content = file_get_contents($template_json_file);
		$template_data = json_decode($template_content, true);

		if (json_last_error() !== JSON_ERROR_NONE || !isset($template_data['content'])) {
			return false;
		}

		return $template_data;
	}

	/**
	 * Get template content from remote API (fallback)
	 */
	private function get_remote_template_content( $template_id ) 
	{
		$response = wp_remote_get(
			'https://studio.houzez.co/wp-json/favethemes-blocks/v1/templates?id=' . $template_id,
			array(
				'headers' => array(
					'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
					'Accept'          => 'application/json, text/javascript, */*; q=0.01',
					'Accept-Language' => 'en-US,en;q=0.9',
					'Referer'         => 'https://houzez.co',
					'x-api-key'       => 'er454erte35dgfd4564dgfd45646dfgd4564dfg',
				),
				'sslverify' => true,
				'timeout' => 60,
			)
		);

		if ( is_wp_error( $response ) || ! is_array( $response )) {
			return $response;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			die( __( 'Error decoding JSON response', 'houzez' ) );
		}

		// The API response structure varies between blocks and pages
		// Pages typically don't include id/title in the response, only content
		// So we need to get this info from the template list
		if (!isset($data['id']) || !isset($data['title'])) {
			// Get template info from the list
			$templates = $this->get_items();
			if (isset($templates[$template_id])) {
				$template_info = $templates[$template_id];
				
				// Add missing fields from template list
				if (!isset($data['id'])) {
					$data['id'] = $template_id;
				}
				if (!isset($data['title'])) {
					$data['title'] = $template_info['title'] ?? '';
				}
				if (!isset($data['type'])) {
					$data['type'] = $template_info['type'] ?? '';
				}
			}
		}

		return $data;
	}

	/**
	 * Optimized sync using parallel requests in batches
	 */
	public static function sync_templates_optimized($batch_size = 10) {
		// Get template list first (use all=true to get all templates for sync)
		$response = wp_remote_get(
			'https://studio.houzez.co/wp-json/favethemes-blocks/v1/templates?all=true',
			array(
				'headers' => array(
					'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
					'Accept'          => 'application/json, text/javascript, */*; q=0.01',
					'Accept-Language' => 'en-US,en;q=0.9',
					'Referer'         => 'https://houzez.co',
					'x-api-key'       => 'er454erte35dgfd4564dgfd45646dfgd4564dfg',
				),
				'sslverify' => true,
				'timeout' => 60,
			)
		);

		if ( is_wp_error( $response ) || ! is_array( $response )) {
			return array('success' => false, 'message' => 'Failed to connect to remote server');
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( json_last_error() !== JSON_ERROR_NONE || !isset($data['elements']) || !is_array($data['elements'])) {
			return array('success' => false, 'message' => 'Invalid response format');
		}

		// Debug logging for sync operations
		if (defined('WP_DEBUG') && WP_DEBUG) {
			$template_count = count($data['elements']);
			$sync_friendly = isset($data['sync_friendly']) ? 'Yes' : 'No';
			error_log("Houzez Sync: Received {$template_count} templates from API (Sync-friendly: {$sync_friendly})");
		}

		// Store template list exactly as received
		update_option('houzez_local_templates', $data);

		$total_templates = count($data['elements']);
		$downloaded = 0;
		$failed = 0;
		$api_calls = 1; // Initial call for template list

		// Process templates in batches using WordPress HTTP API with parallel requests
		$batches = array_chunk($data['elements'], $batch_size);
		
		foreach ($batches as $batch_index => $batch) {
			// Prepare multiple requests for this batch
			$requests = array();
			
			foreach ($batch as $template) {
				if (isset($template['id'])) {
					$requests[$template['id']] = array(
						'url' => 'https://studio.houzez.co/wp-json/favethemes-blocks/v1/templates?id=' . $template['id'],
						'args' => array(
							'headers' => array(
								'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
								'Accept'          => 'application/json, text/javascript, */*; q=0.01',
								'Accept-Language' => 'en-US,en;q=0.9',
								'Referer'         => 'https://houzez.co',
								'x-api-key'       => 'er454erte35dgfd4564dgfd45646dfgd4564dfg',
							),
							'sslverify' => true,
							'timeout' => 30,
						),
						'template_metadata' => $template
					);
				}
			}
			
			// Process requests for this batch
			foreach ($requests as $template_id => $request_data) {
				$api_calls++;
				
				$template_response = wp_remote_get($request_data['url'], $request_data['args']);
				
				if (!is_wp_error($template_response) && is_array($template_response)) {
					$template_data = json_decode(wp_remote_retrieve_body($template_response), true);
					
					if (json_last_error() === JSON_ERROR_NONE && $template_data && isset($template_data['content'])) {
						// Use metadata from template list to ensure consistency
						$template_metadata = $request_data['template_metadata'];
						
						// Always use data from template list for consistency
						$template_data['id'] = $template_metadata['id'];
						$template_data['title'] = $template_metadata['title'] ?? '';
						$template_data['type'] = $template_metadata['type'] ?? '';

						$downloaded++;
					} else {
						$failed++;
					}
				} else {
					$failed++;
				}
				
				// Small delay between requests to avoid overwhelming server
				usleep(50000); // 0.05 second
			}
			
			// Progress update
			$progress = round((($batch_index + 1) / count($batches)) * 100);
			update_option('houzez_sync_progress', array(
				'current_batch' => $batch_index + 1,
				'total_batches' => count($batches),
				'downloaded' => $downloaded,
				'failed' => $failed,
				'total' => $total_templates,
				'progress' => $progress
			));
			
			// Pause between batches
			if ($batch_index < count($batches) - 1) {
				sleep(1); // 1 second between batches
			}
		}

		// Clear progress
		delete_option('houzez_sync_progress');

		// Update sync timestamp
		update_option('houzez_templates_last_sync', current_time('timestamp'));

		return array(
			'success' => true, 
			'message' => sprintf('Optimized sync completed. Downloaded: %d, Failed: %d, API calls: %d', $downloaded, $failed, $api_calls),
			'downloaded' => $downloaded,
			'failed' => $failed,
			'total' => $total_templates,
			'api_calls' => $api_calls
		);
	}

	/**
	 * Try bulk sync approach with proper order mapping
	 */
	private static function try_bulk_sync() {
		// Step 1: Get the template list for correct ordering (use all=true for sync)
		$list_response = wp_remote_get(
			'https://studio.houzez.co/wp-json/favethemes-blocks/v1/templates?all=true',
			array(
				'headers' => array(
					'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
					'Accept'          => 'application/json, text/javascript, */*; q=0.01',
					'Accept-Language' => 'en-US,en;q=0.9',
					'Referer'         => 'https://houzez.co',
					'x-api-key'       => 'er454erte35dgfd4564dgfd45646dfgd4564dfg',
				),
				'sslverify' => true,
				'timeout' => 60,
			)
		);

		if (is_wp_error($list_response) || !is_array($list_response)) {
			error_log('Houzez: Failed to get template list for ordering');
			return array('success' => false, 'message' => 'Failed to get template list');
		}

		$list_data = json_decode(wp_remote_retrieve_body($list_response), true);
		if (json_last_error() !== JSON_ERROR_NONE || !isset($list_data['elements'])) {
			return array('success' => false, 'message' => 'Invalid template list response');
		}

		// Step 2: Get bulk data with all content (uses cache if available)
		$bulk_response = wp_remote_get(
			'https://studio.houzez.co/wp-json/favethemes-blocks/v1/templates?bulk=true',
			array(
				'headers' => array(
					'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
					'Accept'          => 'application/json, text/javascript, */*; q=0.01',
					'Accept-Language' => 'en-US,en;q=0.9',
					'Referer'         => 'https://houzez.co',
					'x-api-key'       => 'er454erte35dgfd4564dgfd45646dfgd4564dfg',
				),
				'sslverify' => true,
				'timeout' => 120,
			)
		);

		if (is_wp_error($bulk_response) || !is_array($bulk_response)) {
			error_log('Houzez: Bulk endpoint not available, falling back to individual calls');
			return array('success' => false, 'message' => 'Bulk endpoint not available');
		}

		$bulk_data = json_decode(wp_remote_retrieve_body($bulk_response), true);
		if (json_last_error() !== JSON_ERROR_NONE || !isset($bulk_data['elements'])) {
			error_log('Houzez: Invalid bulk response, falling back to individual calls');
			return array('success' => false, 'message' => 'Invalid bulk response');
		}

		// Step 3: Create a map of bulk data by ID for fast lookup
		$bulk_map = array();
		foreach ($bulk_data['elements'] as $template) {
			if (isset($template['id'])) {
				$bulk_map[$template['id']] = $template;
			}
		}

		// Step 4: Store template list in the EXACT order from the list endpoint
		// This ensures the library displays templates in the correct order
		update_option('houzez_local_templates', $list_data);

		$total_templates = count($list_data['elements']);
		$downloaded = 0;
		$failed = 0;
		$missing_in_bulk = 0;

		// Step 5: Process each template from the list and map to bulk data
		foreach ($list_data['elements'] as $list_template) {
			if (!isset($list_template['id'])) {
				$failed++;
				continue;
			}

			$template_id = $list_template['id'];

			// Find this template in bulk data
			if (isset($bulk_map[$template_id])) {
				$bulk_template = $bulk_map[$template_id];

				// Check if template has content
				if (isset($bulk_template['content']) && is_array($bulk_template['content'])) {
					$downloaded++;
				} else {
					// Template in list but no content in bulk
					error_log('Houzez: Template ' . $template_id . ' has no content in bulk response');
					$missing_in_bulk++;
				}
			} else {
				// Template in list but not in bulk response
				error_log('Houzez: Template ' . $template_id . ' not found in bulk response');
				$missing_in_bulk++;
			}
		}

		// Update sync timestamp
		update_option('houzez_templates_last_sync', current_time('timestamp'));

		// Build result message
		$cached_info = '';
		if (isset($bulk_data['cached']) && $bulk_data['cached']) {
			$cached_info = ' (using server cache - FAST!)';
		}

		return array(
			'success' => true, 
			'message' => sprintf('Bulk sync with mapping completed%s. Downloaded: %d, Failed: %d, Missing: %d', 
				$cached_info, $downloaded, $failed, $missing_in_bulk),
			'downloaded' => $downloaded,
			'failed' => $failed,
			'missing' => $missing_in_bulk,
			'total' => $total_templates,
			'api_calls' => 2, // 1 for list + 1 for bulk
			'cached' => isset($bulk_data['cached']) ? $bulk_data['cached'] : false,
			'completed' => true
		);
	}

	/**
	 * Hybrid sync - try bulk first, fall back to optimized individual sync
	 */
	public static function sync_templates_hybrid() {
		$bulk_result = self::try_bulk_sync();

		if (!$bulk_result['success']) {
			return self::sync_templates_optimized(10);
		}

		return $bulk_result;
	}

	/**
	 * Sync templates from remote API using hybrid approach
	 */
	public static function sync_templates_from_remote() {
		// Increase execution time and memory for sync
		@ini_set('max_execution_time', 600); // 10 minutes for hybrid sync
		@ini_set('memory_limit', '512M');
		
		// Use hybrid sync for best of both worlds
		return self::sync_templates_hybrid();
	}

	/**
	 * Legacy individual sync method - kept for compatibility
	 */
	public static function sync_templates_individual_calls() {
		// Use the optimized sync with batch size 1 for true individual calls
		return self::sync_templates_optimized(1);
	}

	/**
	 * Sync templates in background using chunked approach
	 */
	public static function sync_templates_chunked($chunk_size = 10, $chunk_index = 0) {
		// First try bulk sync if this is the first chunk
		if ($chunk_index === 0) {
			$bulk_result = self::try_bulk_sync();
			if ($bulk_result['success']) {
				return $bulk_result;
			}
			// If bulk fails, continue with chunked approach
		}
		
		// Get template list first
		$templates = get_option('houzez_local_templates', []);
		
		// For the first chunk or if no templates, always fetch fresh list
		if ($chunk_index === 0 || empty($templates) || !isset($templates['elements'])) {
			// Get fresh template list from API (use all=true for sync)
			$list_response = wp_remote_get(
				'https://studio.houzez.co/wp-json/favethemes-blocks/v1/templates?all=true',
				array(
					'headers' => array(
						'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
						'Accept'          => 'application/json, text/javascript, */*; q=0.01',
						'Accept-Language' => 'en-US,en;q=0.9',
						'Referer'         => 'https://houzez.co',
						'x-api-key'       => 'er454erte35dgfd4564dgfd45646dfgd4564dfg',
					),
					'sslverify' => true,
					'timeout' => 60,
				)
			);

			if (is_wp_error($list_response) || !is_array($list_response)) {
				return array('success' => false, 'message' => 'Failed to get template list');
			}

			$templates = json_decode(wp_remote_retrieve_body($list_response), true);
			if (json_last_error() !== JSON_ERROR_NONE || !isset($templates['elements'])) {
				return array('success' => false, 'message' => 'Invalid template list response');
			}

			// Store fresh template list - do not modify
			update_option('houzez_local_templates', $templates);
		}

		$all_templates = $templates['elements'];
		$total_templates = count($all_templates);
		$chunks = array_chunk($all_templates, $chunk_size);
		$total_chunks = count($chunks);

		// Check if this chunk exists
		if ($chunk_index >= $total_chunks) {
			// All chunks processed
			delete_option('houzez_sync_progress');
			update_option('houzez_templates_last_sync', current_time('timestamp'));
			
			return array(
				'success' => true,
				'message' => 'All chunks processed',
				'completed' => true
			);
		}

		$current_chunk = $chunks[$chunk_index];
		$downloaded = 0;
		$failed = 0;
		$api_calls = 0;

		// Process current chunk
		foreach ($current_chunk as $template) {
			if (isset($template['id'])) {
				$template_response = wp_remote_get(
					'https://studio.houzez.co/wp-json/favethemes-blocks/v1/templates?id=' . $template['id'],
					array(
						'headers' => array(
							'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
							'Accept'          => 'application/json, text/javascript, */*; q=0.01',
							'Accept-Language' => 'en-US,en;q=0.9',
							'Referer'         => 'https://houzez.co',
							'x-api-key'       => 'er454erte35dgfd4564dgdf45646dfgd4564dfg',
						),
						'sslverify' => true,
						'timeout' => 60,
					)
				);

				$api_calls++;

				if (!is_wp_error($template_response) && is_array($template_response)) {
					$template_data = json_decode(wp_remote_retrieve_body($template_response), true);
					if (json_last_error() === JSON_ERROR_NONE && $template_data && isset($template_data['content'])) {
						// Validate template ID if present in response
						if (isset($template_data['id']) && $template_data['id'] != $template['id']) {
							error_log(sprintf(
								'Houzez Template Sync Error: Requested template ID %s but received ID %s (title: %s)',
								$template['id'],
								$template_data['id'],
								$template_data['title'] ?? 'Unknown'
							));
							$failed++;
						} else {
							$downloaded++;
						}
					} else {
						$failed++;
					}
				} else {
					$failed++;
				}

				usleep(100000); // 0.1 second delay
			}
		}

		// Update progress
		$progress = get_option('houzez_sync_progress', array());
		$progress['current_chunk'] = $chunk_index + 1;
		$progress['total_chunks'] = $total_chunks;
		$progress['downloaded'] = ($progress['downloaded'] ?? 0) + $downloaded;
		$progress['failed'] = ($progress['failed'] ?? 0) + $failed;
		$progress['api_calls'] = ($progress['api_calls'] ?? 0) + $api_calls;
		$progress['total'] = $total_templates;
		update_option('houzez_sync_progress', $progress);

		// Schedule next chunk
		if ($chunk_index + 1 < $total_chunks) {
			wp_schedule_single_event(time() + 10, 'houzez_sync_chunk', array($chunk_size, $chunk_index + 1));
		}

		return array(
			'success' => true,
			'message' => sprintf('Chunk %d/%d completed. Downloaded: %d, Failed: %d, API calls: %d', 
				$chunk_index + 1, $total_chunks, $downloaded, $failed, $api_calls),
			'downloaded' => $downloaded,
			'failed' => $failed,
			'api_calls' => $api_calls,
			'chunk_index' => $chunk_index,
			'total_chunks' => $total_chunks,
			'completed' => ($chunk_index + 1 >= $total_chunks)
		);
	}

	/**
	 * Get sync progress
	 */
	public static function get_sync_progress() {
		return get_option('houzez_sync_progress', null);
	}

	/**
	 * Get last sync time
	 */
	public static function get_last_sync_time() {
		return get_option('houzez_templates_last_sync', 0);
	}

	/**
	 * Clear local templates
	 */
	public static function clear_local_templates() {
		global $wpdb;
		
		// Delete all template options
		$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'houzez_template_%'");
		delete_option('houzez_local_templates');
		delete_option('houzez_templates_last_sync');
		
		return true;
	}

	public function get_data( array $args, $context = 'display' ) {
		if ( 'update' === $context ) {
			$data = $args['data'];
		} else {
			$data = $this->get_template_content( $args['template_id'] );
		}

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		// Store cache information before processing
		$cache_info = array(
			'cache_source' => isset($data['cache_source']) ? $data['cache_source'] : 'unknown',
			'api_cached' => isset($data['api_cached']) ? $data['api_cached'] : null,
			'template_id' => $args['template_id']
		);

		// Check if content exists and is valid
		if (!isset($data['content']) || !is_array($data['content'])) {
			return new WP_Error('invalid_template_data', __('Template content is missing or invalid. Please clear local templates and sync again.', 'houzez'));
		}

		$data['content'] = $this->replace_elements_ids( $data['content'] );
		$data['content'] = $this->process_export_import_content( $data['content'], 'on_import' );

		$post_id  = $args['editor_post_id'];
		$document = Elementor\Plugin::$instance->documents->get( $post_id );
		if ( $document ) {
			$data['content'] = $document->get_elements_raw_data( $data['content'], true );
		}

		if ( 'update' === $context ) {
			update_post_meta( $post_id, '_elementor_data', $data['content'] );
		}

		// Add cache information to the final response
		$data['houzez_cache_info'] = $cache_info;

		return $data;
	}

	public function save_item( $template_data ) {
		return new WP_Error( 'invalid_request', 'Cannot save template to a remote source' );
	}

	public function update_item( $new_data ) {
		return new WP_Error( 'invalid_request', 'Cannot update template to a remote source' );
	}

	public function delete_template( $template_id ) {
		return new WP_Error( 'invalid_request', 'Cannot delete template from a remote source' );
	}

	public function export_template( $template_id ) {
		return new WP_Error( 'invalid_request', 'Cannot export template from a remote source' );
	}

	/**
	 * Force chunked sync for large template sets
	 */
	public static function force_chunked_sync($chunk_size = 10, $chunk_index = 0) {
		// Get template list first
		$templates = get_option('houzez_local_templates', []);
		
		if (empty($templates) || !isset($templates['elements'])) {
			// If no template list, get it first (use all=true for sync)
			$list_response = wp_remote_get(
				'https://studio.houzez.co/wp-json/favethemes-blocks/v1/templates?all=true',
				array(
					'headers' => array(
						'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
						'Accept'          => 'application/json, text/javascript, */*; q=0.01',
						'Accept-Language' => 'en-US,en;q=0.9',
						'Referer'         => 'https://houzez.co',
						'x-api-key'       => 'er454erte35dgfd4564dgdf45646dfgd4564dfg',
					),
					'sslverify' => true,
					'timeout' => 60,
				)
			);

			if (is_wp_error($list_response) || !is_array($list_response)) {
				return array('success' => false, 'message' => 'Failed to get template list');
			}

			$templates = json_decode(wp_remote_retrieve_body($list_response), true);
			if (json_last_error() !== JSON_ERROR_NONE || !isset($templates['elements'])) {
				return array('success' => false, 'message' => 'Invalid template list response');
			}


			update_option('houzez_local_templates', $templates);
		}

		return self::sync_templates_chunked($chunk_size, $chunk_index);
	}
}
