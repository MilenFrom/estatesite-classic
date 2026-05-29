/**
 * Houzez User Verification JavaScript
 *
 * Handles verification form submission and UI updates
 */
(function ($) {
    'use strict';

    // Initialize verification functionality
    function initVerification() {
        // Handle document type change to show/hide back side upload
        $('#document_type').on('change', function () {
            const selectedOption = $(this).find('option:selected');
            const requiresBack =
                selectedOption.data('requires-back') === true ||
                selectedOption.attr('data-requires-back') === 'true';

            if (requiresBack) {
                $('.document-back-upload').slideDown(200);
                $('#verification_document_back').prop('required', true);
            } else {
                $('.document-back-upload').slideUp(200);
                $('#verification_document_back').prop('required', false);
            }
        });

        // Trigger change event on page load to handle pre-selected values
        $('#document_type').trigger('change');

        // Handle verification form submission
        $('#houzez-verification-form').on('submit', function (e) {
            e.preventDefault();

            // Submit the form via AJAX
            submitVerificationRequest(this);
        });

        // Handle additional info form submission
        $('#additional-info-form').on('submit', function (e) {
            e.preventDefault();

            // Submit the form via AJAX
            submitAdditionalInfo(this);
        });
    }

    // Submit additional info
    function submitAdditionalInfo(form) {
        const formData = new FormData(form);
        const messagesContainer = $('#additional-info-messages');
        const submitBtn = $('#submit-additional-info-btn');

        messagesContainer.html('');

        $.ajax({
            url: houzez_verification.ajax_url || ajaxurl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                submitBtn
                    .prop('disabled', true)
                    .html(
                        '<i class="houzez-icon icon-spinner-circle me-1"></i> ' +
                            (houzez_verification.submitting || 'Submitting...')
                    );
            },
            success: function (response) {
                if (response.success) {
                    // Show success message
                    window.houzez.Core.util.showSuccess(
                        messagesContainer,
                        response.data.message
                    );

                    // Reset form
                    form.reset();

                    // Redirect after a delay if redirect URL is provided
                    if (response.data.redirect) {
                        setTimeout(function () {
                            window.location.href = response.data.redirect;
                        }, 2000);
                    }
                } else {
                    // Show error message
                    window.houzez.Core.util.showError(
                        messagesContainer,
                        response.data.message
                    );
                }
            },
            error: function () {
                window.houzez.Core.util.showError(
                    messagesContainer,
                    houzez_verification.error
                );
            },
            complete: function () {
                submitBtn
                    .prop('disabled', false)
                    .html(
                        '<i class="houzez-icon icon-upload-button me-1"></i> ' +
                            houzez_verification.submit_additional_info
                    );
            },
        });
    }

    // Submit verification request
    function submitVerificationRequest(form) {
        const formData = new FormData(form);
        formData.append('action', 'houzez_submit_verification');
        formData.append('security', houzez_verification.verify_nonce || '');
        const verificationMessages = $('#verification-messages');
        const selectedOption = $('#document_type').find('option:selected');
        const requiresBack =
            selectedOption.data('requires-back') === true ||
            selectedOption.attr('data-requires-back') === 'true';

        verificationMessages.html('');

        // Validate form
        let isValid = true;

        // Check front document
        if (!$('#verification_document')[0].files[0]) {
            window.houzez.Core.util.showError(
                verificationMessages,
                houzez_verification.document_required
            );
            isValid = false;
        }

        // Check back document if required
        if (requiresBack && !$('#verification_document_back')[0].files[0]) {
            window.houzez.Core.util.showError(
                verificationMessages,
                houzez_verification.back_side_required
            );
            isValid = false;
        }

        if (!isValid) {
            return;
        }

        // AJAX submission
        $.ajax({
            url: houzez_verification.ajax_url || ajaxurl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $('#submit-verification-btn')
                    .prop('disabled', true)
                    .text(houzez_verification.submitting);
                $('.verification-response').remove();
            },
            success: function (response) {
                if (response.success) {
                    // Show success message
                    window.houzez.Core.util.showSuccess(
                        verificationMessages,
                        response.data.message
                    );

                    // Reset form
                    $('#houzez-verification-form')[0].reset();
                    $('.file-upload-text').text(
                        houzez_verification.choose_file
                    );

                    // Redirect after delay if redirect URL is provided
                    if (response.data.redirect) {
                        setTimeout(function () {
                            window.location.href = response.data.redirect;
                        }, 2000);
                    } else {
                        // Fallback to default redirect
                        setTimeout(function () {
                            window.location.href =
                                window.location.href.split('?')[0] +
                                '?verification=true';
                        }, 2000);
                    }
                } else {
                    // Show error message
                    //verificationMessages.html(response.data.message);
                    window.houzez.Core.util.showError(
                        verificationMessages,
                        response.data.message
                    );
                }
            },
            error: function () {
                window.houzez.Core.util.showError(
                    verificationMessages,
                    houzez_verification.error
                );
            },
            complete: function () {
                $('#submit-verification-btn')
                    .prop('disabled', false)
                    .text(houzez_verification.submit);
            },
        });
    }

    // Initialize on document ready
    $(document).ready(function () {
        initVerification();
    });
})(jQuery);
