/**
 * Created by waqasriaz on 22/06/16.
 */
jQuery(document).ready(function ($) {
    'use strict';

    var ajaxurl = houzez_admin_vars.ajaxurl;
    var paid_text = houzez_admin_vars.paid_status;
    var nonce = houzez_admin_vars.nonce;
    var install_now = houzez_admin_vars.install_now;
    var installing = houzez_admin_vars.installing;
    var installed = houzez_admin_vars.installed;
    var activate_now = houzez_admin_vars.activate_now;
    var activating = houzez_admin_vars.activating;
    var activated = houzez_admin_vars.activated;
    var active = houzez_admin_vars.active;
    var failed = houzez_admin_vars.failed;
    var update_now = houzez_admin_vars.update_now || 'Update Now';
    var updating = houzez_admin_vars.updating || 'Updating...';
    var updated = houzez_admin_vars.updated || 'Updated';

    /*--------------------------------------------------------------
     * Install plugin
     * ------------------------------------------------------------*/
    function houzez_install_plugin($current_btn) {
        var $button = $current_btn;
        var plugin_slug = $current_btn.data('slug');
        var plugin_source = $current_btn.data('source');
        var plugin_file = $current_btn.data('file');
        var plugin_name = $current_btn.data('name');

        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajaxurl,
            data: {
                action: 'houzez_plugin_installation',
                plugin_slug: plugin_slug,
                plugin_source: plugin_source,
                plugin_file: plugin_file,
                _ajax_nonce: nonce,
            },
            beforeSend: function () {
                $button.addClass('updating-message');
                $button.text(installing);
            },
            complete: function () {
                $button.removeClass('updating-message');
            },
            success: function (res) {
                if (res.success) {
                    $button.addClass('updated-message');
                    $button.text(installed);

                    setTimeout(function () {
                        // Update plugin box UI instead of just changing button
                        updatePluginBoxAfterInstall(
                            $button,
                            plugin_slug,
                            plugin_name,
                            plugin_file
                        );
                        // Stats will be updated by updatePluginBoxAfterInstall function
                    }, 900);
                } else {
                    $button.text(failed);
                    setTimeout(function () {
                        $button.text(install_now);
                    }, 900);
                }
            },
            error: function (errorThrown) {},
        });
    }

    /*--------------------------------------------------------------
     * Activate plugin
     * ------------------------------------------------------------*/
    function houzez_activate($current_btn) {
        var $button = $current_btn;
        var plugin_file = $current_btn.data('file');
        var plugin_name = $current_btn.data('name');
        var plugin_slug = $current_btn.data('slug');

        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajaxurl,
            data: {
                action: 'houzez_plugin_activate',
                plugin_file: plugin_file,
                _ajax_nonce: nonce,
            },
            beforeSend: function () {
                $button.addClass('updating-message');
                $button.text(activating);
            },
            complete: function () {
                $button.removeClass('updating-message');
            },
            success: function (res) {
                if (res.success) {
                    $button.addClass('updated-message');
                    $button.text(activated);

                    setTimeout(function () {
                        // Update plugin box UI instead of just changing button
                        updatePluginBoxAfterActivation(
                            $button,
                            plugin_slug,
                            plugin_name,
                            plugin_file
                        );
                        // Stats will be updated by updatePluginBoxAfterActivation function
                    }, 900);
                } else {
                    $button.text(failed);
                    setTimeout(function () {
                        $button.text(activate_now);
                    }, 900);
                }
            },
            error: function (errorThrown) {},
        });
    }

    /*--------------------------------------------------------------
     * Update plugin
     * ------------------------------------------------------------*/
    function houzez_update_plugin($current_btn) {
        var $button = $current_btn;
        var plugin_file = $current_btn.data('file');
        var plugin_name = $current_btn.data('name');
        var plugin_slug = $current_btn.data('slug');

        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajaxurl,
            data: {
                action: 'houzez_plugin_update',
                plugin_file: plugin_file,
                plugin_name: plugin_name,
                plugin_slug: plugin_slug,
                _ajax_nonce: nonce,
            },
            beforeSend: function () {
                $button.addClass('updating-message');
                $button.text(updating);
            },
            complete: function () {
                $button.removeClass('updating-message');
            },
            success: function (res) {
                if (res.success) {
                    $button.addClass('updated-message');
                    $button.text(updated);

                    setTimeout(function () {
                        // Update plugin box UI instead of reloading
                        updatePluginBoxAfterUpdate(
                            $button,
                            plugin_slug,
                            plugin_name,
                            plugin_file
                        );
                        // Stats will be updated by updatePluginBoxAfterUpdate function
                    }, 1000);
                } else {
                    $button.text(failed);
                    setTimeout(function () {
                        $button.text(update_now);
                    }, 900);
                }
            },
            error: function (errorThrown) {
                $button.text(failed);
                setTimeout(function () {
                    $button.text(update_now);
                }, 900);
            },
        });
    }

    /*--------------------------------------------------------------
     * Deactivate plugin
     * ------------------------------------------------------------*/
    function houzez_deactivate_plugin($current_btn) {
        var $button = $current_btn;
        var plugin_file = $current_btn.data('file');
        var plugin_name = $current_btn.data('name');
        var plugin_slug = $current_btn.data('slug');

        if (
            !confirm('Are you sure you want to deactivate ' + plugin_name + '?')
        ) {
            return;
        }

        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajaxurl,
            data: {
                action: 'houzez_plugin_deactivate',
                plugin_file: plugin_file,
                plugin_name: plugin_name,
                _ajax_nonce: nonce,
            },
            beforeSend: function () {
                $button.addClass('updating-message');
                $button.text('Deactivating...');
            },
            complete: function () {
                $button.removeClass('updating-message');
            },
            success: function (res) {
                if (res.success) {
                    $button.addClass('updated-message');
                    $button.text('Deactivated');

                    setTimeout(function () {
                        // Update plugin box UI instead of reloading
                        updatePluginBoxAfterDeactivation(
                            $button,
                            plugin_slug,
                            plugin_name,
                            plugin_file
                        );
                        // Stats will be updated by updatePluginBoxAfterDeactivation function
                    }, 1000);
                } else {
                    $button.text(failed);
                    setTimeout(function () {
                        $button.text('Deactivate');
                    }, 900);
                }
            },
            error: function (errorThrown) {
                $button.text(failed);
                setTimeout(function () {
                    $button.text('Deactivate');
                }, 900);
            },
        });
    }

    /*--------------------------------------------------------------
     * Uninstall plugin
     * ------------------------------------------------------------*/
    function houzez_uninstall_plugin($current_btn) {
        var $button = $current_btn;
        var plugin_file = $current_btn.data('file');
        var plugin_name = $current_btn.data('name');
        var plugin_slug = $current_btn.data('slug');

        if (
            !confirm(
                'Are you sure you want to permanently uninstall ' +
                    plugin_name +
                    '? This action cannot be undone.'
            )
        ) {
            return;
        }

        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajaxurl,
            data: {
                action: 'houzez_plugin_uninstall',
                plugin_file: plugin_file,
                plugin_name: plugin_name,
                _ajax_nonce: nonce,
            },
            beforeSend: function () {
                $button.addClass('updating-message');
                $button.text('Uninstalling...');
            },
            complete: function () {
                $button.removeClass('updating-message');
            },
            success: function (res) {
                if (res.success) {
                    $button.addClass('updated-message');
                    $button.text('Uninstalled');

                    setTimeout(function () {
                        // Update plugin box UI instead of reloading
                        updatePluginBoxAfterUninstall(
                            $button,
                            plugin_slug,
                            plugin_name,
                            plugin_file
                        );
                        // Stats will be updated by updatePluginBoxAfterUninstall function
                    }, 1000);
                } else {
                    $button.text(failed);
                    setTimeout(function () {
                        $button.text('Uninstall');
                    }, 900);
                    if (res.data && res.data.errorMessage) {
                        alert('Error: ' + res.data.errorMessage);
                    }
                }
            },
            error: function (errorThrown) {
                $button.text(failed);
                setTimeout(function () {
                    $button.text('Uninstall');
                }, 900);
            },
        });
    }

    /*--------------------------------------------------------------
     * Enhanced Progress Tracking System
     * ------------------------------------------------------------*/
    var progressTracker = {
        total: 0,
        current: 0,
        plugins: [],
        currentPlugin: '',
        currentAction: '',

        init: function (plugins, actionType) {
            this.total = plugins.length;
            this.current = 0;
            this.plugins = plugins;
            this.currentPlugin = '';
            this.currentAction = this.getActionText(actionType);

            // Reset UI
            this.updateProgress();
            this.clearLog();
            this.updateCurrentAction('Preparing...', '');
        },

        getActionText: function (actionType) {
            switch (actionType) {
                case 'install-required':
                    return 'Installing & Activating';
                case 'update-all':
                    return 'Updating';
                case 'activate-required':
                    return 'Activating';
                default:
                    return 'Processing';
            }
        },

        updateProgress: function () {
            var percentage =
                this.total > 0
                    ? Math.round((this.current / this.total) * 100)
                    : 0;

            $('.progress-count').text(this.current + '/' + this.total);
            $('.progress-percentage').text(percentage + '%');
            $('.houzez-progress-fill').css('width', percentage + '%');
        },

        updateCurrentAction: function (action, pluginName) {
            var icon = this.getActionIcon(action);
            $('.action-icon').text(icon);
            $('.action-text').text(action);
            $('.plugin-name').text(pluginName);
        },

        getActionIcon: function (action) {
            if (action.includes('Installing')) return '📥';
            if (action.includes('Activating')) return '🔄';
            if (action.includes('Updating')) return '⬆️';
            if (action.includes('Complete')) return '✅';
            if (action.includes('Error')) return '❌';
            return '⏳';
        },

        addLogEntry: function (message, type, pluginName) {
            var time = new Date().toLocaleTimeString();
            var icon = this.getLogIcon(type);

            var logEntry = $(
                '<div class="log-entry ' +
                    type +
                    '">' +
                    '<span class="log-icon">' +
                    icon +
                    '</span>' +
                    '<span class="log-message">' +
                    message +
                    '</span>' +
                    '<span class="log-time">' +
                    time +
                    '</span>' +
                    '</div>'
            );

            $('.log-content').append(logEntry);
            $('.log-content').scrollTop($('.log-content')[0].scrollHeight);
        },

        getLogIcon: function (type) {
            switch (type) {
                case 'success':
                    return '✅';
                case 'error':
                    return '❌';
                case 'warning':
                    return '⚠️';
                case 'info':
                    return 'ℹ️';
                default:
                    return '•';
            }
        },

        clearLog: function () {
            $('.log-content').empty();
        },

        nextPlugin: function (pluginName) {
            this.current++;
            this.currentPlugin = pluginName;
            this.updateProgress();
        },

        complete: function () {
            this.updateCurrentAction('Complete!', '');
            this.addLogEntry(
                'All operations completed successfully',
                'success'
            );

            // Reset any button states
            $('.houzez-fix-required, .houzez-bulk-install, .houzez-bulk-update')
                .removeClass('updating-message')
                .text(function () {
                    if ($(this).hasClass('houzez-fix-required'))
                        return 'Fix Now';
                    if ($(this).hasClass('houzez-bulk-install'))
                        return 'Install Required Plugins';
                    if ($(this).hasClass('houzez-bulk-update'))
                        return 'Update All';
                    return $(this).text();
                });

            setTimeout(function () {
                $('#houzez-plugin-loading').hide();
                location.reload();
            }, 1500);
        },

        error: function (message) {
            this.updateCurrentAction('Error occurred', '');
            this.addLogEntry('Error: ' + message, 'error');

            // Reset any button states
            $('.houzez-fix-required, .houzez-bulk-install, .houzez-bulk-update')
                .removeClass('updating-message')
                .text(function () {
                    if ($(this).hasClass('houzez-fix-required'))
                        return 'Fix Now';
                    if ($(this).hasClass('houzez-bulk-install'))
                        return 'Install Required Plugins';
                    if ($(this).hasClass('houzez-bulk-update'))
                        return 'Update All';
                    return $(this).text();
                });

            setTimeout(function () {
                $('#houzez-plugin-loading').hide();
            }, 3000);
        },
    };

    /*--------------------------------------------------------------
     * Enhanced bulk plugin actions with progress tracking
     * ------------------------------------------------------------*/
    function houzez_bulk_plugin_action(action, plugins) {
        // Show loading overlay
        $('#houzez-plugin-loading').show();

        // Initialize progress tracker
        progressTracker.init(plugins, action);
        progressTracker.addLogEntry(
            'Starting ' +
                progressTracker.currentAction.toLowerCase() +
                ' process...',
            'info'
        );

        // Get plugin names for better UX
        var pluginNames = {};
        $('.admin-houzez-box-plugins').each(function () {
            var slug = $(this).data('plugin-slug');
            var name = $(this).find('h3').first().text().trim();
            if (slug && name) {
                pluginNames[slug] = name;
            }
        });

        var data = {
            action: 'houzez_bulk_plugin_action',
            bulk_action: action,
            plugins: plugins,
            _ajax_nonce: nonce,
        };

        // Process plugins sequentially for better progress tracking
        processPluginsSequentially(plugins, pluginNames, action);
    }

    function processPluginsSequentially(plugins, pluginNames, actionType) {
        if (plugins.length === 0) {
            progressTracker.complete();
            return;
        }

        var currentPlugin = plugins[0];
        var remainingPlugins = plugins.slice(1);
        var pluginName = pluginNames[currentPlugin] || currentPlugin;

        // Update progress
        progressTracker.nextPlugin(pluginName);

        // Update current action based on type
        var actionText = '';
        switch (actionType) {
            case 'install-required':
                actionText = 'Installing & Activating';
                break;
            case 'update-all':
                actionText = 'Updating';
                break;
            case 'activate-required':
                actionText = 'Activating';
                break;
            default:
                actionText = 'Processing';
        }

        progressTracker.updateCurrentAction(actionText, pluginName);
        progressTracker.addLogEntry(
            'Starting ' + actionText.toLowerCase() + ' ' + pluginName,
            'info'
        );

        // Process single plugin
        var data = {
            action: 'houzez_bulk_plugin_action',
            bulk_action: actionType,
            plugins: [currentPlugin],
            _ajax_nonce: nonce,
        };

        $.post(ajaxurl, data, function (response) {
            if (response.success) {
                var message =
                    response.data || pluginName + ' processed successfully';

                // Check if the plugin was skipped
                if (
                    message.includes('skipped') ||
                    message.includes('already')
                ) {
                    progressTracker.addLogEntry(message, 'info');
                } else {
                    progressTracker.addLogEntry(message, 'success');
                }

                // Process next plugin
                setTimeout(function () {
                    processPluginsSequentially(
                        remainingPlugins,
                        pluginNames,
                        actionType
                    );
                }, 300); // Reduced delay for better UX
            } else {
                progressTracker.addLogEntry(
                    'Failed to process ' +
                        pluginName +
                        ': ' +
                        (response.data || 'Unknown error'),
                    'error'
                );

                // Continue with remaining plugins even if one fails
                setTimeout(function () {
                    processPluginsSequentially(
                        remainingPlugins,
                        pluginNames,
                        actionType
                    );
                }, 500);
            }
        }).fail(function () {
            progressTracker.addLogEntry(
                'Network error while processing ' + pluginName,
                'error'
            );

            // Continue with remaining plugins
            setTimeout(function () {
                processPluginsSequentially(
                    remainingPlugins,
                    pluginNames,
                    actionType
                );
            }, 500);
        });
    }

    // Use event delegation to handle dynamically added buttons
    $(document).on('click', '.houzez-plugin-js', function (e) {
        e.preventDefault();
        var $clicked_btn = $(this);

        if ($clicked_btn.hasClass('houzez-install-btn')) {
            houzez_install_plugin($clicked_btn);
        } else if ($clicked_btn.hasClass('houzez-activate-btn')) {
            houzez_activate($clicked_btn);
        } else if ($clicked_btn.hasClass('houzez-update-btn')) {
            houzez_update_plugin($clicked_btn);
        } else if ($clicked_btn.hasClass('houzez-deactivate-btn')) {
            houzez_deactivate_plugin($clicked_btn);
        } else if ($clicked_btn.hasClass('houzez-uninstall-btn')) {
            houzez_uninstall_plugin($clicked_btn);
        }
    });

    /*--------------------------------------------------------------
     * Bulk actions
     * ------------------------------------------------------------*/
    $('.houzez-bulk-install, .houzez-bulk-update').on('click', function () {
        var action = $(this).data('action');
        var plugins = [];

        if (action === 'install-required') {
            $('.admin-houzez-box-plugins').each(function () {
                var $box = $(this);
                if (
                    ($box.data('plugin-required') === true ||
                        $box.data('plugin-required') === 'true') &&
                    $box.data('plugin-status') !== 'active'
                ) {
                    plugins.push($box.data('plugin-slug'));
                }
            });
        } else if (action === 'update-all') {
            $('.admin-houzez-box-plugins').each(function () {
                var $box = $(this);
                if ($box.find('.houzez-update-btn').length) {
                    plugins.push($box.data('plugin-slug'));
                }
            });
        }

        if (plugins.length > 0) {
            houzez_bulk_plugin_action(action, plugins);
        } else {
            var message = '';
            if (action === 'install-required') {
                message =
                    'All required plugins are already installed and activated.';
            } else if (action === 'update-all') {
                message = 'All plugins are already up to date.';
            } else {
                message = 'No plugins found for this action.';
            }
            alert(message);
        }
    });

    // Refresh status
    $('.houzez-refresh-status').on('click', function () {
        // Add refresh parameter to clear caches
        var currentUrl = window.location.href;
        var separator = currentUrl.indexOf('?') !== -1 ? '&' : '?';
        window.location.href = currentUrl + separator + 'refresh=1';
    });

    // Fix required plugins
    $('.houzez-fix-required').on('click', function () {
        var $button = $(this);
        var originalText = $button.text();

        // Collect all required plugins that are not active
        var pluginsToFix = [];

        $('.admin-houzez-box-plugins').each(function () {
            var $box = $(this);
            var isRequired = $box.data('plugin-required');
            var status = $box.data('plugin-status');
            var slug = $box.data('plugin-slug');

            if (
                (isRequired === true || isRequired === 'true') &&
                status !== 'active'
            ) {
                pluginsToFix.push(slug);
            }
        });

        if (pluginsToFix.length === 0) {
            alert('All required plugins are already installed and activated.');
            return;
        }

        var confirmMessage =
            'This will install and activate ' +
            pluginsToFix.length +
            ' required plugin(s). Continue?';

        if (!confirm(confirmMessage)) {
            return;
        }

        $button.addClass('updating-message').text('Fixing...');

        // Use the enhanced bulk action with progress tracking
        houzez_bulk_plugin_action('install-required', pluginsToFix);
    });

    /*--------------------------------------------------------------
     * Plugin filtering
     * ------------------------------------------------------------*/
    $('.filter-btn').on('click', function (e) {
        e.preventDefault();

        var $this = $(this);
        var filterValue = $this.data('filter');
        var $plugins = $('.admin-houzez-box-plugins');

        // Update active state
        $('.filter-btn').removeClass('active');
        $this.addClass('active');

        // Show all plugins first
        $plugins.show();

        // Apply filter
        switch (filterValue) {
            case 'required':
                $plugins.filter('[data-plugin-required="false"]').hide();
                break;
            case 'recommended':
                $plugins.filter('[data-plugin-required="true"]').hide();
                break;
            case 'active':
                $plugins
                    .filter(
                        '[data-plugin-status!="active"][data-plugin-status!="update-available"]'
                    )
                    .hide();
                break;
            case 'inactive':
                $plugins.filter('[data-plugin-status!="inactive"]').hide();
                break;
            case 'updates':
                $plugins.filter('[data-plugin-has-update="false"]').hide();
                break;
            case 'all':
            default:
                // Show all plugins
                break;
        }

        // Show/hide no plugins message
        var $visiblePlugins = $('.admin-houzez-box-plugins:visible');
        var $noPluginsMessage = $('#houzez-no-plugins-found');

        if ($visiblePlugins.length === 0) {
            $noPluginsMessage.show();
        } else {
            $noPluginsMessage.hide();
        }
    });

    function updateFilterCounts() {
        var $plugins = $('.admin-houzez-box-plugins');
        var totalCount = $plugins.length;

        // Count different types
        var requiredCount = $plugins.filter(
            '[data-plugin-required="true"]'
        ).length;
        var recommendedCount = $plugins.filter(
            '[data-plugin-required="false"]'
        ).length;
        var activeCount = $plugins.filter(
            '[data-plugin-status="active"], [data-plugin-status="update-available"]'
        ).length;
        var inactiveCount = $plugins.filter(
            '[data-plugin-status="inactive"]'
        ).length;
        var updatesCount = $plugins.filter(
            '[data-plugin-has-update="true"]'
        ).length;

        // Update count displays
        $('#count-all').text(totalCount);
        $('#count-required').text(requiredCount);
        $('#count-recommended').text(recommendedCount);
        $('#count-active').text(activeCount);
        $('#count-inactive').text(inactiveCount);
        $('#count-updates').text(updatesCount);
    }

    // Initialize filter counts on page load
    $(document).ready(function () {
        updateFilterCounts();
    });

    /*--------------------------------------------------------------
     * UI Update Functions (to avoid page reloads)
     * ------------------------------------------------------------*/

    // Update plugin box after installation
    function updatePluginBoxAfterInstall(
        $button,
        plugin_slug,
        plugin_name,
        plugin_file
    ) {
        var $pluginBox = $button.closest('.admin-houzez-box-plugins');
        var isRequired = $pluginBox.attr('data-plugin-required') === 'true';
        var plugin_source = $pluginBox.data('plugin-source') || '';

        // Update status badge
        $pluginBox
            .find('.plugin-status-badge')
            .removeClass('not-installed')
            .addClass('inactive')
            .text('Inactive');

        // Update plugin box status class
        $pluginBox
            .removeClass('status-not-installed')
            .addClass('status-inactive')
            .attr('data-plugin-status', 'inactive');

        // Update action buttons
        var $actionsSection = $pluginBox.find('.plugin-actions-section');
        var hasUpdate = $pluginBox.attr('data-plugin-has-update') === 'true';

        var newButtons =
            '<a href="#" class="houzez-plugin-js houzez-activate-btn button button-primary" data-name="' +
            plugin_name +
            '" data-slug="' +
            plugin_slug +
            '" data-file="' +
            plugin_file +
            '">Activate</a>';

        if (hasUpdate) {
            newButtons +=
                ' <a href="#" class="houzez-plugin-js houzez-update-btn button" data-name="' +
                plugin_name +
                '" data-slug="' +
                plugin_slug +
                '" data-file="' +
                plugin_file +
                '">Update Now</a>';
        }

        if (!isRequired) {
            newButtons +=
                ' <a href="#" class="houzez-plugin-js houzez-uninstall-btn button button-link-delete" data-name="' +
                plugin_name +
                '" data-slug="' +
                plugin_slug +
                '" data-file="' +
                plugin_file +
                (plugin_source ? '" data-source="' + plugin_source : '') +
                '">Uninstall</a>';
        }

        $actionsSection.html(newButtons);

        // Update stats immediately after DOM changes
        setTimeout(function () {
            updatePluginStats();
            updateFilterCounts();
            updateRequiredPluginsStatus();
        }, 50);
    }

    // Update plugin box after activation
    function updatePluginBoxAfterActivation(
        $button,
        plugin_slug,
        plugin_name,
        plugin_file
    ) {
        var $pluginBox = $button.closest('.admin-houzez-box-plugins');

        // Update status badge
        $pluginBox
            .find('.plugin-status-badge')
            .removeClass('inactive')
            .addClass('active')
            .text('Active');

        // Update plugin box status class
        $pluginBox
            .removeClass('status-inactive')
            .addClass('status-active')
            .attr('data-plugin-status', 'active');

        // Update action buttons
        var $actionsSection = $pluginBox.find('.plugin-actions-section');
        var hasUpdate = $pluginBox.attr('data-plugin-has-update') === 'true';

        var newButtons = '<span class="button button-disabled">Active</span>';
        newButtons +=
            ' <a href="#" class="houzez-plugin-js houzez-deactivate-btn button" data-name="' +
            plugin_name +
            '" data-slug="' +
            plugin_slug +
            '" data-file="' +
            plugin_file +
            '">Deactivate</a>';

        if (hasUpdate) {
            newButtons +=
                ' <a href="#" class="houzez-plugin-js houzez-update-btn button" data-name="' +
                plugin_name +
                '" data-slug="' +
                plugin_slug +
                '" data-file="' +
                plugin_file +
                '">Update Now</a>';
        }

        $actionsSection.html(newButtons);

        // Update stats immediately after DOM changes
        setTimeout(function () {
            updatePluginStats();
            updateFilterCounts();
            updateRequiredPluginsStatus();
        }, 50);
    }

    // Update plugin box after deactivation
    function updatePluginBoxAfterDeactivation(
        $button,
        plugin_slug,
        plugin_name,
        plugin_file
    ) {
        var $pluginBox = $button.closest('.admin-houzez-box-plugins');
        var isRequired = $pluginBox.attr('data-plugin-required') === 'true';
        var plugin_source = $pluginBox.data('plugin-source') || '';

        // Update status badge
        $pluginBox
            .find('.plugin-status-badge')
            .removeClass('active')
            .addClass('inactive')
            .text('Inactive');

        // Update plugin box status class
        $pluginBox
            .removeClass('status-active')
            .addClass('status-inactive')
            .attr('data-plugin-status', 'inactive');

        // Update action buttons
        var $actionsSection = $pluginBox.find('.plugin-actions-section');
        var hasUpdate = $pluginBox.attr('data-plugin-has-update') === 'true';

        var newButtons =
            '<a href="#" class="houzez-plugin-js houzez-activate-btn button button-primary" data-name="' +
            plugin_name +
            '" data-slug="' +
            plugin_slug +
            '" data-file="' +
            plugin_file +
            '">Activate</a>';

        if (hasUpdate) {
            newButtons +=
                ' <a href="#" class="houzez-plugin-js houzez-update-btn button" data-name="' +
                plugin_name +
                '" data-slug="' +
                plugin_slug +
                '" data-file="' +
                plugin_file +
                '">Update Now</a>';
        }

        if (!isRequired) {
            newButtons +=
                ' <a href="#" class="houzez-plugin-js houzez-uninstall-btn button button-link-delete" data-name="' +
                plugin_name +
                '" data-slug="' +
                plugin_slug +
                '" data-file="' +
                plugin_file +
                (plugin_source ? '" data-source="' + plugin_source : '') +
                '">Uninstall</a>';
        }

        $actionsSection.html(newButtons);

        // Update stats immediately after DOM changes
        setTimeout(function () {
            updatePluginStats();
            updateFilterCounts();
            updateRequiredPluginsStatus();
        }, 50);
    }

    // Update plugin box after uninstall
    function updatePluginBoxAfterUninstall(
        $button,
        plugin_slug,
        plugin_name,
        plugin_file
    ) {
        var $pluginBox = $button.closest('.admin-houzez-box-plugins');

        // Get original plugin source from the plugin box data attribute
        var plugin_source = $pluginBox.data('plugin-source') || '';

        // Update status badge
        $pluginBox
            .find('.plugin-status-badge')
            .removeClass('active inactive')
            .addClass('not-installed')
            .text('Not Installed');

        // Update plugin box status class
        $pluginBox
            .removeClass('status-active status-inactive')
            .addClass('status-not-installed')
            .attr('data-plugin-status', 'not-installed')
            .attr('data-plugin-has-update', 'false');

        // Remove update badge if present
        $pluginBox.find('.update-badge').remove();

        // Update action buttons - only show install button with all necessary data attributes
        var $actionsSection = $pluginBox.find('.plugin-actions-section');
        var newButtons =
            '<a class="houzez-plugin-js houzez-install-btn button button-primary" data-name="' +
            plugin_name +
            '" data-slug="' +
            plugin_slug +
            '" data-file="' +
            plugin_file +
            '"' +
            (plugin_source ? ' data-source="' + plugin_source + '"' : '') +
            ' href="#">Install Now</a>';

        $actionsSection.html(newButtons);

        // Update stats immediately after DOM changes
        setTimeout(function () {
            updatePluginStats();
            updateFilterCounts();
            updateRequiredPluginsStatus();
        }, 50);
    }

    // Update plugin box after update
    function updatePluginBoxAfterUpdate(
        $button,
        plugin_slug,
        plugin_name,
        plugin_file
    ) {
        var $pluginBox = $button.closest('.admin-houzez-box-plugins');
        var plugin_source = $pluginBox.data('plugin-source') || '';

        // Remove update badge
        $pluginBox.find('.update-badge').remove();

        // Update plugin box has-update attribute
        $pluginBox.attr('data-plugin-has-update', 'false');

        // Update action buttons - remove update button
        var $actionsSection = $pluginBox.find('.plugin-actions-section');

        // Check if plugin was active before update by looking at current status
        // If there was an "Active" button or the status is active, assume it stays active after update
        var wasActive =
            $pluginBox.attr('data-plugin-status') === 'active' ||
            $actionsSection.find('.button-disabled:contains("Active")').length >
                0;

        var newButtons = '';
        if (wasActive) {
            // Plugin was active before update, keep it active
            newButtons = '<span class="button button-disabled">Active</span>';
            newButtons +=
                ' <a href="#" class="houzez-plugin-js houzez-deactivate-btn button" data-name="' +
                plugin_name +
                '" data-slug="' +
                plugin_slug +
                '" data-file="' +
                plugin_file +
                '">Deactivate</a>';

            // Update status to active since plugin remains active after update
            $pluginBox.attr('data-plugin-status', 'active');
        } else {
            // Plugin was inactive before update, show activate button
            newButtons =
                '<a href="#" class="houzez-plugin-js houzez-activate-btn button button-primary" data-name="' +
                plugin_name +
                '" data-slug="' +
                plugin_slug +
                '" data-file="' +
                plugin_file +
                '">Activate</a>';

            var isRequired = $pluginBox.attr('data-plugin-required') === 'true';
            if (!isRequired) {
                newButtons +=
                    ' <a href="#" class="houzez-plugin-js houzez-uninstall-btn button button-link-delete" data-name="' +
                    plugin_name +
                    '" data-slug="' +
                    plugin_slug +
                    '" data-file="' +
                    plugin_file +
                    (plugin_source ? '" data-source="' + plugin_source : '') +
                    '">Uninstall</a>';
            }

            // Update status to inactive
            $pluginBox.attr('data-plugin-status', 'inactive');
        }

        $actionsSection.html(newButtons);

        // Update stats immediately after DOM changes
        setTimeout(function () {
            updatePluginStats();
            updateFilterCounts();
            updateRequiredPluginsStatus();
        }, 50);
    }

    // Update plugin statistics
    function updatePluginStats() {
        var $plugins = $('.admin-houzez-box-plugins');
        var totalCount = $plugins.length;
        var activeCount = $plugins.filter(
            '[data-plugin-status="active"], [data-plugin-status="update-available"]'
        ).length;
        var updatesCount = $plugins.filter(
            '[data-plugin-has-update="true"]'
        ).length;
        var requiredCount = $plugins.filter(
            '[data-plugin-required="true"]'
        ).length;

        // Update stat displays
        $('.stat-item .stat-number').eq(0).text(totalCount);
        $('.stat-item .stat-number').eq(1).text(activeCount);
        $('.stat-item .stat-number').eq(2).text(updatesCount);
        $('.stat-item .stat-number').eq(3).text(requiredCount);
    }

    // Update required plugins status
    function updateRequiredPluginsStatus() {
        var $requiredPlugins = $(
            '.admin-houzez-box-plugins[data-plugin-required="true"]'
        );
        var totalRequired = $requiredPlugins.length;

        var activeRequired = $requiredPlugins.filter(
            '[data-plugin-status="active"], [data-plugin-status="update-available"]'
        ).length;
        var installedRequired = $requiredPlugins.filter(
            '[data-plugin-status="active"], [data-plugin-status="inactive"], [data-plugin-status="update-available"]'
        ).length;

        var $statusContainer = $('.required-status-container');
        var $progressFill = $statusContainer.find('.progress-fill');
        var $progressActive = $statusContainer.find('.progress-active');
        var $progressTotal = $statusContainer.find('.progress-total');
        var $statusDescription = $statusContainer.find('.status-description');
        var $statusIcon = $statusContainer.find('.status-icon .dashicons');
        var $fixButton = $statusContainer.find('.houzez-fix-required');

        // Update progress
        var progressPercent =
            totalRequired > 0 ? (activeRequired / totalRequired) * 100 : 100;
        $progressFill.css('width', progressPercent + '%');
        $progressActive.text(activeRequired);
        $progressTotal.text(totalRequired);

        // Update status
        $statusContainer.removeClass(
            'status-complete status-partial status-incomplete'
        );

        if (activeRequired === totalRequired) {
            $statusContainer.addClass('status-complete');
            $statusIcon
                .removeClass('dashicons-warning dashicons-dismiss')
                .addClass('dashicons-yes-alt');
            $statusDescription.text('All required plugins are active');
            $fixButton.hide();
        } else if (installedRequired === totalRequired) {
            $statusContainer.addClass('status-partial');
            $statusIcon
                .removeClass('dashicons-yes-alt dashicons-dismiss')
                .addClass('dashicons-warning');
            $statusDescription.text(
                'All required plugins are installed but some are inactive'
            );
            $fixButton.show();
        } else {
            $statusContainer.addClass('status-incomplete');
            $statusIcon
                .removeClass('dashicons-yes-alt dashicons-warning')
                .addClass('dashicons-dismiss');
            $statusDescription.text('Some required plugins are missing');
            $fixButton.show();
        }
    }

    /*--------------------------------------------------------------
     * Feedback
     * ------------------------------------------------------------*/
    $('#houzez-feedback-submit').on('click', function (e) {
        e.preventDefault();

        var $button = $(this);
        var button_text = $button.text();
        var form_messages = $('#form-messages');
        var email = $('#houzez_feedback_email').val();
        var email_subject = $('#houzez_feedback_subject').val();
        var message = $('#houzez_feedback_message').val();
        var feedback_nonce = $('#houzez_feedback_nonce').val();

        if (!email) {
            form_messages.html(
                '<span class="error">Please provide email address.</span>'
            );
        } else if (!email_subject) {
            form_messages.html(
                '<span class="error">Please select subject.</span>'
            );
        } else if (!message) {
            form_messages.html(
                '<span class="error">Please enter message.</span>'
            );
        } else {
            form_messages.html('');

            jQuery.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl,
                data: {
                    action: 'houzez_feedback',
                    email: email,
                    subject: email_subject,
                    message: message,
                    feedback_nonce: feedback_nonce,
                },
                beforeSend: function () {
                    $button.addClass('updating-message');
                },
                complete: function () {
                    $button.removeClass('updating-message');
                },
                success: function (response) {
                    if (response.success) {
                        document.getElementById('admin-houzez-form').reset();
                        form_messages.html(
                            '<span class="success">' + response.msg + '</span>'
                        );
                    } else {
                        form_messages.html(
                            '<span class="error">' + response.msg + '</span>'
                        );
                    }

                    setTimeout(function () {
                        form_messages.html('');
                    }, 2500);
                },
                error: function (errorThrown) {},
            });
        }
    });

    $(
        '#fave_listing_template .inside .rwmb-meta-box > div:gt(0):lt(2)'
    ).wrapAll('<div id="only_for_listings_templates">');

    $('#only_for_listings_templates > div:gt(0):lt(1)').wrapAll(
        '<div id="listing_tabs">'
    );

    $('#fave_listings_tabs').on('change', function () {
        checkTabs();
    });
    function checkTabs() {
        var tabs = jQuery('#fave_listings_tabs').val();

        if (tabs == 'enable') {
            jQuery('#listing_tabs').show();
        } else {
            jQuery('#listing_tabs').hide();
        }
    }

    jQuery(window).on('load', function () {
        checkTabs();
    });

    $('#activate_purchase_listing').on('click', function () {
        var itemID, invoiceID, purchaseType;

        itemID = $(this).attr('data-item');
        invoiceID = $(this).attr('data-invoice');
        purchaseType = $(this).attr('data-purchaseType');

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'houzez_activate_purchase_listing',
                item_id: itemID,
                invoice_id: invoiceID,
                purchase_type: purchaseType,
            },
            beforeSend: function () {
                $(this).find('.houzez-loader-js').addClass('loader-show');
            },
            complete: function () {
                $(this).find('.houzez-loader-js').removeClass('loader-show');
            },
            success: function (data) {
                jQuery('#activate_purchase_listing').remove();
                jQuery(
                    '#houzez_invoice_payment_status .fave_admin_label'
                ).removeClass('label-red');
                jQuery(
                    '#houzez_invoice_payment_status .fave_admin_label'
                ).addClass('label-green');
                jQuery('#houzez_invoice_payment_status .fave_admin_label')
                    .empty()
                    .html(paid_text);
            },
            error: function (errorThrown) {},
        });
    });

    $('#activate_package').on('click', function () {
        var itemID, invoiceID;

        itemID = $(this).attr('data-item');
        invoiceID = $(this).attr('data-invoice');

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'houzez_activate_pack_purchase',
                item_id: itemID,
                invoice_id: invoiceID,
            },
            beforeSend: function () {
                $(this).find('.houzez-loader-js').addClass('loader-show');
            },
            complete: function () {
                $(this).find('.houzez-loader-js').removeClass('loader-show');
            },
            success: function (data) {
                jQuery('#activate_package').remove();
                jQuery(
                    '#houzez_invoice_payment_status .fave_admin_label'
                ).removeClass('label-red');
                jQuery(
                    '#houzez_invoice_payment_status .fave_admin_label'
                ).addClass('label-green');
                jQuery('#houzez_invoice_payment_status .fave_admin_label')
                    .empty()
                    .html(paid_text);
            },
            error: function (errorThrown) {},
        });
    });

    // Event delegation for activate buttons in invoice data table
    $(document).on(
        'click',
        '.houzez_activate_listing[data-action="activate_package"]',
        function () {
            var $button = $(this);
            var itemID = $button.attr('data-item');
            var invoiceID = $button.attr('data-invoice');

            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'houzez_activate_pack_purchase',
                    item_id: itemID,
                    invoice_id: invoiceID,
                },
                beforeSend: function () {
                    $button.find('.houzez-loader-js').addClass('loader-show');
                    $button.prop('disabled', true);
                },
                complete: function () {
                    $button
                        .find('.houzez-loader-js')
                        .removeClass('loader-show');
                    $button.prop('disabled', false);
                },
                success: function (data) {
                    // Replace the entire actions cell content with PAID status
                    $button
                        .closest('td')
                        .html(
                            '<span class="invoice-status-label fave_admin_label float-none label-green" style="font-size: 10px; padding: 2px 8px; display: inline-block; text-transform: uppercase; font-weight: 600; border-radius: 3px; background-color: #00a32a; color: #fff;">PAID</span>'
                        );
                },
                error: function (errorThrown) {
                    console.log('Error activating package:', errorThrown);
                },
            });
        }
    );

    $(document).on(
        'click',
        '.houzez_activate_listing[data-action="activate_purchase"]',
        function () {
            var $button = $(this);
            var itemID = $button.attr('data-item');
            var invoiceID = $button.attr('data-invoice');
            var purchaseType = $button.attr('data-purchaseType');

            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'houzez_activate_purchase_listing',
                    item_id: itemID,
                    invoice_id: invoiceID,
                    purchase_type: purchaseType,
                },
                beforeSend: function () {
                    $button.find('.houzez-loader-js').addClass('loader-show');
                    $button.prop('disabled', true);
                },
                complete: function () {
                    $button
                        .find('.houzez-loader-js')
                        .removeClass('loader-show');
                    $button.prop('disabled', false);
                },
                success: function (data) {
                    // Replace the entire actions cell content with PAID status
                    $button
                        .closest('td')
                        .html(
                            '<span class="invoice-status-label fave_admin_label float-none label-green" style="font-size: 10px; padding: 2px 8px; display: inline-block; text-transform: uppercase; font-weight: 600; border-radius: 3px; background-color: #00a32a; color: #fff;">PAID</span>'
                        );
                },
                error: function (errorThrown) {
                    console.log('Error activating purchase:', errorThrown);
                },
            });
        }
    );

    $(document).ready(function ($) {
        $('#specific_sidebar').on('change', function () {
            checkSidebar();
        });

        function checkSidebar() {
            var specific_sidebar = jQuery('#specific_sidebar').val();

            if (specific_sidebar == 'yes') {
                jQuery('#houzez_selected_sidebar').stop(true, true).fadeIn(500);
            } else {
                jQuery('#houzez_selected_sidebar').hide();
            }
        }

        jQuery(window).load(function () {
            checkSidebar();
        });
    });
});
