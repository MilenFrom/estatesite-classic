<?php
global $verification_data, $document_types;
?>
<div class="additional-info-request-section">
    <div class="alert alert-warning mb-4" role="alert">
        <h5 class="alert-heading fw-bold"><i class="houzez-icon icon-info-circle me-1"></i> <?php esc_html_e('Additional Information Required', 'houzez'); ?></h5>
        <p><?php esc_html_e('We\'ve reviewed your verification request but need additional information to complete the process.', 'houzez'); ?></p>
        <p class="mb-0"><?php esc_html_e('Please provide the requested information below to continue with your verification.', 'houzez'); ?></p>
    </div>
    
    <!-- Admin message -->
    <?php if (isset($verification_data['additional_info_request']) && !empty($verification_data['additional_info_request'])): ?>
        <div class="admin-message bg-light p-4 mb-4 rounded border">
            <h6 class="fw-bold mb-3"><i class="houzez-icon icon-messages-bubble me-1"></i> <?php esc_html_e('Message from Admin:', 'houzez'); ?></h6>
            <p class="mb-0"><?php echo esc_html($verification_data['additional_info_request']); ?></p>
        </div>
    <?php endif; ?>
    
    <!-- Update submission form -->
    <form id="additional-info-form" class="verification-form" enctype="multipart/form-data">
        <?php wp_nonce_field('houzez_additional_info_nonce', 'additional_info_security'); ?>
        <input type="hidden" name="action" value="houzez_submit_additional_info">
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="document_type" class="form-label"><?php esc_html_e('Document Type', 'houzez'); ?> <span class="required">*</span></label>
                    <select id="document_type" name="document_type" class="selectpicker form-control bs-select-hidden" required>
                        <option value=""><?php esc_html_e('Select Document Type', 'houzez'); ?></option>
                        <?php foreach ($document_types as $value => $document): ?>
                            <option value="<?php echo esc_attr($value); ?>" 
                                data-requires-back="<?php echo esc_attr($document['requires_back'] ? 'true' : 'false'); ?>">
                                <?php echo esc_html($document['label']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="additional_document" class="form-label"><?php esc_html_e('Upload Document (Front Side)', 'houzez'); ?> <span class="required">*</span></label>
                    <input type="file" id="additional_document" name="additional_document" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                    <p class="file-help-text"><?php esc_html_e('Allowed file types: JPG, PNG, PDF. Maximum file size: 10MB', 'houzez'); ?></p>
                </div>
            </div>
        </div>
        
        <div class="row document-back-upload" style="display: none;">
            <div class="col-12">
                <div class="form-group mb-3">
                    <label for="additional_document_back" class="form-label"><?php esc_html_e('Upload Back Side', 'houzez'); ?> <span class="required">*</span></label>
                    <input type="file" id="additional_document_back" name="additional_document_back" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                    <p class="file-help-text"><?php esc_html_e('Upload the back side of your document. Allowed file types: JPG, PNG, PDF. Maximum file size: 10MB', 'houzez'); ?></p>
                </div>
            </div>
        </div>
        
        <div class="form-group mb-4">
            <label for="additional_notes" class="form-label"><?php esc_html_e('Additional Notes', 'houzez'); ?></label>
            <textarea id="additional_notes" name="additional_notes" class="form-control" rows="4" placeholder="<?php esc_attr_e('Provide any additional information that may help with your verification', 'houzez'); ?>"></textarea>
        </div>
        
        <div id="additional-info-messages"></div>
        
        <div class="form-group mt-4">
            <button type="submit" id="submit-additional-info-btn" class="btn btn-primary">
                <i class="houzez-icon icon-upload-button me-1"></i> <?php esc_html_e('Submit Additional Information', 'houzez'); ?>
            </button>
        </div>
    </form>
    
    <div class="additional-info-timeline mt-5">
        <h6 class="font-weight-bold mb-3"><?php esc_html_e('Verification Timeline', 'houzez'); ?></h6>
        <ul class="timeline list-unstyled">
            <li class="timeline-item">
                <div class="timeline-marker bg-warning"></div>
                <div class="timeline-content">
                    <h6 class="timeline-title"><?php esc_html_e('Additional Information Requested', 'houzez'); ?></h6>
                    <p class="text-muted mb-0">
                        <?php echo isset($verification_data['processed_on']) ? esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($verification_data['processed_on']))) : ''; ?>
                    </p>
                </div>
            </li>
            <li class="timeline-item">
                <div class="timeline-marker bg-light"></div>
                <div class="timeline-content">
                    <h6 class="timeline-title"><?php esc_html_e('Verification Submitted', 'houzez'); ?></h6>
                    <p class="text-muted mb-0">
                        <?php echo isset($verification_data['submitted_on']) ? esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($verification_data['submitted_on']))) : ''; ?>
                    </p>
                </div>
            </li>
        </ul>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Handle document type change to show/hide back side upload
    $('#document_type').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const requiresBack = selectedOption.data('requires-back') === true || selectedOption.attr('data-requires-back') === 'true';
        
        if (requiresBack) {
            $('.document-back-upload').slideDown(200);
            $('#additional_document_back').prop('required', true);
        } else {
            $('.document-back-upload').slideUp(200);
            $('#additional_document_back').prop('required', false);
        }
    });

    // Trigger change event on page load to handle pre-selected values
    $('#document_type').trigger('change');
});
</script>