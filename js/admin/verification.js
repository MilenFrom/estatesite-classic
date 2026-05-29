/**
 * Houzez Admin Verification JavaScript
 *
 * Handles verification requests management in the admin area
 */
(function ($) {
    'use strict';

    // Initialize verification management functionality
    function initVerificationAdmin() {
        // Handle approval action with confirmation tooltip
        $('.approve-request').on('click', function () {
            const userId = $(this).data('user-id');
            const userName = $(this)
                .closest('tr')
                .find('.user-name')
                .text()
                .trim();

            // Confirmation with user info
            if (
                confirm(
                    houzez_admin_verification.confirm_approve.replace(
                        '{user}',
                        userName
                    )
                )
            ) {
                processVerificationRequest(userId, 'approve', $(this));
            }
        });

        // Handle rejection modal
        $('.reject-request').on('click', function () {
            const userId = $(this).data('user-id');
            const userName = $(this)
                .closest('tr')
                .find('.user-name')
                .text()
                .trim();

            // Set user ID in modal
            $('#rejection-user-id').val(userId);

            // Add user name in modal title
            const modalTitle = $('.houzez-modal-header h3').first();
            modalTitle.data('original-text', modalTitle.text());
            modalTitle.text(
                houzez_admin_verification.reject_request_for.replace(
                    '{user}',
                    userName
                )
            );

            // Show modal with animation
            $('#rejection-modal').fadeIn(200);
            $('#rejection-reason').focus();
        });

        // Handle additional info modal
        $('.request-info-btn').on('click', function () {
            const userId = $(this).data('user-id');
            const userName = $(this)
                .closest('tr')
                .find('.user-name')
                .text()
                .trim();

            // Set user ID in modal
            $('#additional-info-user-id').val(userId);

            // Add user name in modal title
            const modalTitle = $('.houzez-modal-header h3').eq(1);
            modalTitle.data('original-text', modalTitle.text());
            modalTitle.text(
                houzez_admin_verification.request_info_for.replace(
                    '{user}',
                    userName
                )
            );

            // Show modal with animation
            $('#additional-info-modal').fadeIn(200);
            $('#additional-info').focus();
        });

        // Close rejection modal
        $('.close, .cancel-rejection').on('click', function () {
            // Restore original modal title
            const modalTitle = $('.houzez-modal-header h3').first();
            if (modalTitle.data('original-text')) {
                modalTitle.text(modalTitle.data('original-text'));
            }

            // Hide modal with animation
            $('#rejection-modal').fadeOut(200);
        });

        // Close additional info modal
        $('.close, .cancel-additional-info').on('click', function () {
            // Restore original modal title
            const modalTitle = $('.houzez-modal-header h3').eq(1);
            if (modalTitle.data('original-text')) {
                modalTitle.text(modalTitle.data('original-text'));
            }

            // Hide modal with animation
            $('#additional-info-modal').fadeOut(200);
        });

        // Close modal when clicking outside
        $(window).on('click', function (event) {
            if ($(event.target).is('#rejection-modal')) {
                $('.cancel-rejection').trigger('click');
            }
            if ($(event.target).is('#additional-info-modal')) {
                $('.cancel-additional-info').trigger('click');
            }
        });

        // Handle rejection form submission
        $('#rejection-form').on('submit', function (e) {
            e.preventDefault();

            const formData = $(this).serialize();
            const userId = $('#rejection-user-id').val();
            const btn = $('#rejection-form')
                .closest('.houzez-modal-content')
                .find('button[type="submit"]');
            const reason = $('#rejection-reason').val();

            // Optional validation for rejection reason
            if (reason.trim() === '') {
                if (
                    !confirm(houzez_admin_verification.confirm_reject_no_reason)
                ) {
                    return;
                }
            }

            // AJAX submission
            $.ajax({
                url: houzez_admin_verification.ajax_url,
                type: 'POST',
                data: formData,
                beforeSend: function () {
                    showProcessingState(
                        btn,
                        houzez_admin_verification.processing
                    );
                },
                success: function (response) {
                    if (response.success) {
                        showProcessingState(
                            btn,
                            houzez_admin_verification.success_text,
                            'success'
                        );
                        setTimeout(function () {
                            location.reload();
                        }, 800);
                    } else {
                        showProcessingState(
                            btn,
                            response.data.message,
                            'error'
                        );
                        setTimeout(function () {
                            btn.prop('disabled', false)
                                .text(houzez_admin_verification.confirm_reject)
                                .removeClass(
                                    'button-state-processing button-state-success button-state-error'
                                );
                        }, 1500);
                    }
                },
                error: function () {
                    showProcessingState(
                        btn,
                        houzez_admin_verification.ajax_error,
                        'error'
                    );
                    setTimeout(function () {
                        btn.prop('disabled', false)
                            .text(houzez_admin_verification.confirm_reject)
                            .removeClass(
                                'button-state-processing button-state-success button-state-error'
                            );
                    }, 1500);
                },
            });
        });

        // Handle additional info form submission
        $('#additional-info-form').on('submit', function (e) {
            e.preventDefault();

            const formData = $(this).serialize();
            const userId = $('#additional-info-user-id').val();
            const btn = $('#additional-info-form')
                .closest('.houzez-modal-content')
                .find('button[type="submit"]');
            const additionalInfo = $('#additional-info').val();

            // Validation for additional info field
            if (additionalInfo.trim() === '') {
                $('#additional-info').addClass('error').focus();
                return;
            } else {
                $('#additional-info').removeClass('error');
            }

            // AJAX submission
            $.ajax({
                url: houzez_admin_verification.ajax_url,
                type: 'POST',
                data: formData,
                beforeSend: function () {
                    showProcessingState(
                        btn,
                        houzez_admin_verification.processing
                    );
                },
                success: function (response) {
                    if (response.success) {
                        showProcessingState(
                            btn,
                            houzez_admin_verification.success_text,
                            'success'
                        );
                        setTimeout(function () {
                            location.reload();
                        }, 800);
                    } else {
                        showProcessingState(
                            btn,
                            response.data.message,
                            'error'
                        );
                        setTimeout(function () {
                            btn.prop('disabled', false)
                                .text(
                                    houzez_admin_verification.request_info_text
                                )
                                .removeClass(
                                    'button-state-processing button-state-success button-state-error'
                                );
                        }, 1500);
                    }
                },
                error: function () {
                    showProcessingState(
                        btn,
                        houzez_admin_verification.ajax_error,
                        'error'
                    );
                    setTimeout(function () {
                        btn.prop('disabled', false)
                            .text(houzez_admin_verification.request_info_text)
                            .removeClass(
                                'button-state-processing button-state-success button-state-error'
                            );
                    }, 1500);
                },
            });
        });

        // Handle reset status or revoke approval
        $('.reset-status, .revoke-approval').on('click', function () {
            const userId = $(this).data('user-id');
            const userName = $(this)
                .closest('tr')
                .find('.user-name')
                .text()
                .trim();
            const action = $(this).hasClass('reset-status')
                ? 'reset'
                : 'revoke';
            let confirmText = '';

            if (action === 'reset') {
                confirmText = houzez_admin_verification.confirm_reset;
            } else {
                confirmText = houzez_admin_verification.confirm_revoke;
            }

            // Replace placeholder with user name
            confirmText = confirmText.replace('{user}', userName);

            if (confirm(confirmText)) {
                processVerificationRequest(userId, action, $(this));
            }
        });

        // Add pulse effect to hover on action buttons
        $('.request-actions button').hover(
            function () {
                $(this).addClass('pulse-animation');
            },
            function () {
                $(this).removeClass('pulse-animation');
            }
        );

        // Field validation styling
        $('#additional-info').on('input', function () {
            if ($(this).val().trim() !== '') {
                $(this).removeClass('error');
            }
        });

        // Add CSS for button states
        addButtonStateStyles();
    }

    // Process verification request via AJAX
    function processVerificationRequest(userId, action, button) {
        const data = {
            action: 'houzez_process_verification',
            user_id: userId,
            action_type: action,
            security: houzez_admin_verification.verify_nonce,
        };

        // Highlight the row that's being processed
        const row = button.closest('tr');
        row.addClass('processing-row');

        $.ajax({
            url: houzez_admin_verification.ajax_url,
            type: 'POST',
            data: data,
            beforeSend: function () {
                showProcessingState(
                    button,
                    houzez_admin_verification.processing
                );
            },
            success: function (response) {
                if (response.success) {
                    showProcessingState(
                        button,
                        houzez_admin_verification.success_text,
                        'success'
                    );

                    // Add a visual feedback before reload
                    row.addClass('update-success');

                    setTimeout(function () {
                        location.reload();
                    }, 800);
                } else {
                    row.removeClass('processing-row');
                    showProcessingState(button, response.data.message, 'error');

                    setTimeout(function () {
                        // Reset button text
                        resetButtonState(button, action);
                    }, 1500);
                }
            },
            error: function () {
                row.removeClass('processing-row');
                showProcessingState(
                    button,
                    houzez_admin_verification.ajax_error,
                    'error'
                );

                setTimeout(function () {
                    // Reset button text
                    resetButtonState(button, action);
                }, 1500);
            },
        });
    }

    // Helper function to show processing state with visual feedback
    function showProcessingState(button, text, state = 'processing') {
        button
            .prop('disabled', true)
            .text(text)
            .removeClass(
                'button-state-processing button-state-success button-state-error'
            )
            .addClass('button-state-' + state);

        if (state === 'processing') {
            button.append('<span class="processing-spinner"></span>');
        } else if (state === 'success') {
            button.prepend(
                '<span class="dashicons dashicons-yes-alt"></span> '
            );
        } else if (state === 'error') {
            button.prepend(
                '<span class="dashicons dashicons-warning"></span> '
            );
        }
    }

    // Reset button state based on action type
    function resetButtonState(button, action) {
        // Remove all state classes
        button
            .prop('disabled', false)
            .removeClass(
                'button-state-processing button-state-success button-state-error'
            );

        // Reset text and icon
        if (action === 'approve') {
            button.html(
                '<span class="dashicons dashicons-yes"></span> ' +
                    houzez_admin_verification.approve_text
            );
        } else if (action === 'reset') {
            button.html(
                '<span class="dashicons dashicons-image-rotate"></span> ' +
                    houzez_admin_verification.reset_text
            );
        } else if (action === 'revoke') {
            button.html(
                '<span class="dashicons dashicons-undo"></span> ' +
                    houzez_admin_verification.revoke_text
            );
        }
    }

    // Add CSS for button states
    function addButtonStateStyles() {
        const css = `
            @keyframes pulse-animation {
                0% {
                    transform: scale(1);
                }
                50% {
                    transform: scale(1.05);
                }
                100% {
                    transform: scale(1);
                }
            }
            
            .pulse-animation {
                animation: pulse-animation 0.5s ease;
            }
            
            .button-state-processing {
                opacity: 0.8;
                position: relative;
            }
            
            .processing-spinner {
                display: inline-block;
                width: 12px;
                height: 12px;
                border: 2px solid rgba(255,255,255,0.3);
                border-radius: 50%;
                border-top-color: #fff;
                animation: spin 1s linear infinite;
                margin-left: 5px;
                vertical-align: middle;
            }
            
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
            
            .button-state-success {
                background-color: #46b450 !important;
                border-color: #46b450 !important;
                color: #fff !important;
            }
            
            .button-state-error {
                background-color: #dc3232 !important;
                border-color: #dc3232 !important;
                color: #fff !important;
            }
            
            .processing-row {
                background-color: #f9f9f9 !important;
                transition: background-color 0.3s ease;
            }
            
            .update-success {
                background-color: #f0fff0 !important;
                transition: background-color 0.3s ease;
            }
            
            textarea.error {
                border-color: #dc3232;
                box-shadow: 0 0 0 1px #dc3232;
            }
            
            /* Animation for dashicons in buttons */
            .button .dashicons {
                animation: fadeIn 0.2s ease-in-out;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
        `;

        $('head').append(
            '<style id="houzez-verification-admin-styles">' + css + '</style>'
        );
    }

    // Initialize on document ready
    $(document).ready(function () {
        initVerificationAdmin();
    });
})(jQuery);
