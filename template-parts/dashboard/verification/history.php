<?php 
global $document_types;
// Get user ID and verification data
$user_id = get_current_user_id();
if (isset($_GET['edit_user']) && !empty($_GET['edit_user']) && current_user_can('edit_users')) {
    $user_id = intval($_GET['edit_user']);
}

// Get verification history with readable notes
$verification_history = $GLOBALS['houzez_user_verification']->get_verification_history($user_id);

if (!is_array($verification_history)) {
    $verification_history = array();
}
?>
<div class="block-wrap mt-0 mb-4">
    <div class="block-title-wrap d-flex justify-content-between align-items-center">
        <h2><?php esc_html_e('Verification History', 'houzez'); ?></h2>
    </div>
    <div class="block-content-wrap">
        <?php if (!empty($verification_history)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th scope="col"><?php esc_html_e('Date', 'houzez'); ?></th>
                        <th scope="col"><?php esc_html_e('Status', 'houzez'); ?></th>
                        <th scope="col"><?php esc_html_e('Document Type', 'houzez'); ?></th>
                        <th scope="col"><?php esc_html_e('Notes', 'houzez'); ?></th>
                        <th scope="col"><?php esc_html_e('Details', 'houzez'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Sort history by date (newest first)
                    usort($verification_history, function($a, $b) {
                        return strtotime($b['date']) - strtotime($a['date']);
                    });
                    
                    foreach ($verification_history as $history_item): 
                        $history_status = isset($history_item['status']) ? $history_item['status'] : '';
                        $status_badge_class = '';
                        
                        switch ($history_status) {
                            case 'pending':
                                $status_badge_class = 'bg-warning';
                                $status_display = esc_html__('Pending', 'houzez');
                                break;
                            case 'approved':
                                $status_badge_class = 'bg-success';
                                $status_display = esc_html__('Approved', 'houzez');
                                break;
                            case 'rejected':
                                $status_badge_class = 'bg-danger';
                                $status_display = esc_html__('Rejected', 'houzez');
                                break;
                            case 'additional_info_required':
                                $status_badge_class = 'bg-warning';
                                $status_display = esc_html__('Additional Info Required', 'houzez');
                                break;
                            default:
                                $status_badge_class = 'bg-secondary';
                                $status_display = $history_status ? esc_html($history_status) : esc_html__('Unknown', 'houzez');
                        }

                        // Get document type display name
                        $doc_type = isset($history_item['document_type']) && !empty($history_item['document_type']) 
                            ? $history_item['document_type'] 
                            : '';
                        
                        // If the document type is not in the history item, try to get it from verification data
                        if (empty($doc_type) && !empty($verification_data)) {
                            // Check if this is likely additional info submission
                            if ($history_status === 'pending' && 
                                isset($verification_data['additional_document_type']) && 
                                !empty($verification_data['additional_document_type']) &&
                                isset($verification_data['additional_info_submitted_on'])) {
                                
                                // If the dates approximately match (same day), use additional document type
                                $history_date = isset($history_item['date']) ? date('Y-m-d', strtotime($history_item['date'])) : '';
                                $add_info_date = date('Y-m-d', strtotime($verification_data['additional_info_submitted_on']));
                                
                                if ($history_date === $add_info_date) {
                                    $doc_type = $verification_data['additional_document_type'];
                                }
                            } else {
                                $doc_type = isset($verification_data['document_type']) ? $verification_data['document_type'] : '';
                            }
                        }

                        $doc_type_display = $document_types[$doc_type]['label'] ?? '';

                        if (empty($doc_type)) {
                            $doc_type_display = '';
                        }
                    ?>
                    <tr>
                        <td><?php echo isset($history_item['date']) ? esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($history_item['date']))) : ''; ?></td>
                        <td>
                            <span class="badge <?php echo esc_attr($status_badge_class); ?> text-white">
                                <?php echo esc_html($status_display); ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                            if (!empty($doc_type_display)) {
                                echo esc_html($doc_type_display);
                            } else {
                                if ($history_status === 'additional_info_required') {
                                    echo '<span class="text-muted">—</span>';
                                } elseif ($history_status === '') {
                                    echo '<span class="text-muted">—</span>';
                                } else {
                                    echo '<span class="text-muted">' . esc_html__('Not specified', 'houzez') . '</span>';
                                }
                            }
                            ?>
                        </td>
                        <td><?php echo isset($history_item['readable_note']) ? esc_html($history_item['readable_note']) : (isset($history_item['notes']) ? esc_html($history_item['notes']) : ''); ?></td>
                        <td><?php echo isset($history_item['context']) ? esc_html($history_item['context']) : ''; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            <?php esc_html_e('No verification history available.', 'houzez'); ?>
        </div>
        <?php endif; ?>
    </div>
</div>