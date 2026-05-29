<?php
/**
 * Template Name: User Dashboard Create Listing
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 06/10/15
 * Time: 3:49 PM
 */
global $houzez_local, $properties_page, $hide_prop_fields, $is_multi_steps;

$current_user = wp_get_current_user();
$userID = get_current_user_id();
$packageUserId = $userID;

// Get agent agency ID for package verification
$agent_agency_id = houzez_get_agent_agency_id($userID);
if ($agent_agency_id) {
    $packageUserId = $agent_agency_id;
}

// Redirect non-authorized users
if (is_user_logged_in() && !houzez_check_role()) {
    wp_redirect(home_url());
    exit;
}

// Setup variables
$user_email = $current_user->user_email;
$admin_email = get_bloginfo('admin_email');
$invalid_nonce = false;
$submitted_successfully = false;
$updated_successfully = false;
$dashboard_listings = houzez_dashboard_listings();
$hide_prop_fields = houzez_option('hide_add_prop_fields');
$enable_paid_submission = houzez_option('enable_paid_submission');
$payment_page_link = houzez_get_template_link('template/template-payment.php');
$thankyou_page_link = houzez_get_template_link('template/template-thankyou.php');
$select_packages_link = houzez_get_template_link('template/template-packages.php');
$submit_property_link = houzez_get_template_link('template/user_dashboard_submit.php');
$is_user_verification_required = Houzez_Property_Submit::is_user_verification_required($userID);
$create_listing_login_required = houzez_option('create_listing_button');
$allowed_html = array();
$submit_form_type = houzez_option('submit_form_type');

// Set login requirement if verification is required
if ($is_user_verification_required) {
    $create_listing_login_required = 'yes';
}

// Form style configuration
if ($submit_form_type == 'one_step') {
    $submit_form_main_class = 'houzez-one-step-form';
    $is_multi_steps = 'form-section-wrap active';
} else {
    $submit_form_main_class = 'houzez-m-step-form';
    $is_multi_steps = 'form-section-wrap form-step';
}

// Process form submission
if (isset($_POST['action'])) {
    $submission_action = sanitize_text_field($_POST['action']);
    $is_draft = isset($_POST['houzez_draft']) ? sanitize_text_field($_POST['houzez_draft']) : '';

    $new_property = array(
        'post_type' => 'property'
    );

    // Per listing payment
    if ($enable_paid_submission == 'per_listing') {
        // Guest user flow
        if (!is_user_logged_in()) {
            $email = sanitize_email($_POST['user_email']);
            $errors = array();

            // Validate email
            if (email_exists($email)) {
                $errors[] = $houzez_local['email_already_registerd'];
            }

            if (!is_email($email)) {
                $errors[] = $houzez_local['invalid_email'];
            }

            if (empty($errors)) {
                // Create username from email
                $username_parts = explode('@', $email);
                $username = $username_parts[0];

                if (username_exists($username)) {
                    $username = $username . rand(5, 999);
                }

                $random_password = wp_generate_password(12, false);
                $user_id = wp_create_user($username, $random_password, $email);

                if (!is_wp_error($user_id)) {
                    $user = get_user_by('id', $user_id);

                    houzez_update_profile($user_id);
                    houzez_wp_new_user_notification($user_id, $random_password);
                    
                    // Register as agent if enabled
                    $user_as_agent = houzez_option('user_as_agent');
                    if ($user_as_agent == 'yes') {
                        houzez_register_as_agent($username, $email, $user_id);
                    }

                    if (!is_wp_error($user)) {
                        wp_clear_auth_cookie();
                        wp_set_current_user($user->ID, $user->user_login);
                        wp_set_auth_cookie($user->ID);
                        do_action('wp_login', $user->user_login);

                        $property_id = apply_filters('houzez_submit_listing', $new_property);

                        // WooCommerce integration
                        if (houzez_is_woocommerce()) {
                            if (($submission_action != 'update_property') || ($is_draft == 'draft')) {
                                do_action('houzez_per_listing_woo_payment', $property_id);
                            } else {
                                if (!empty($submit_property_link)) {
                                    $submit_property_link = add_query_arg('edit_property', $property_id, $submit_property_link);
                                    $separator = (parse_url($submit_property_link, PHP_URL_QUERY) == NULL) ? '?' : '&';
                                    $parameter = 'updated=1';
                                    wp_redirect($submit_property_link . $separator . $parameter);
                                    exit;
                                }
                            }
                        } else {
                            // Payment redirection
                            if (!empty($payment_page_link) && $submission_action != 'update_property') {
                                $separator = (parse_url($payment_page_link, PHP_URL_QUERY) == NULL) ? '?' : '&';
                                $parameter = 'prop-id=' . $property_id;
                                wp_redirect($payment_page_link . $separator . $parameter);
                                exit;
                            } elseif (!empty($payment_page_link) && isset($_POST['houzez_draft'])) {
                                $separator = (parse_url($payment_page_link, PHP_URL_QUERY) == NULL) ? '?' : '&';
                                $parameter = 'prop-id=' . $property_id;
                                wp_redirect($payment_page_link . $separator . $parameter);
                                exit;
                            } else {
                                if (!empty($dashboard_listings)) {
                                    $separator = (parse_url($dashboard_listings, PHP_URL_QUERY) == NULL) ? '?' : '&';
                                    $parameter = ($updated_successfully) ? '' : '';
                                    wp_redirect($dashboard_listings . $separator . $parameter);
                                    exit;
                                }
                            }
                        }
                    }
                }
            }
        } else {
            // Logged in user flow
            $property_id = apply_filters('houzez_submit_listing', $new_property);

            // WooCommerce integration
            if (houzez_is_woocommerce()) {
                if (($submission_action != 'update_property') || ($is_draft == 'draft')) {
                    do_action('houzez_per_listing_woo_payment', $property_id);
                } else {
                    if (!empty($submit_property_link)) {
                        $submit_property_link = add_query_arg('edit_property', $property_id, $submit_property_link);
                        $separator = (parse_url($submit_property_link, PHP_URL_QUERY) == NULL) ? '?' : '&';
                        $parameter = 'updated=1';
                        wp_redirect($submit_property_link . $separator . $parameter);
                        exit;
                    }
                }
            } else {
                // Payment redirection
                if (!empty($payment_page_link) && $submission_action != 'update_property') {
                    $separator = (parse_url($payment_page_link, PHP_URL_QUERY) == NULL) ? '?' : '&';
                    $parameter = 'prop-id=' . $property_id;
                    wp_redirect($payment_page_link . $separator . $parameter);
                    exit;
                } elseif (!empty($payment_page_link) && isset($_POST['houzez_draft'])) {
                    $separator = (parse_url($payment_page_link, PHP_URL_QUERY) == NULL) ? '?' : '&';
                    $parameter = 'prop-id=' . $property_id;
                    wp_redirect($payment_page_link . $separator . $parameter);
                    exit;
                } else {
                    if (!empty($submit_property_link)) {
                        $submit_property_link = add_query_arg('edit_property', $property_id, $submit_property_link);
                        $separator = (parse_url($submit_property_link, PHP_URL_QUERY) == NULL) ? '?' : '&';
                        $parameter = 'updated=1';
                        wp_redirect($submit_property_link . $separator . $parameter);
                        exit;
                    }
                }
            }
        }

        // Send admin notification for listing updates if enabled
        if ($submission_action == 'update_property' && houzez_option('edit_listings_admin_approved') == 'yes') {
            $args = array(
                'listing_title' => get_the_title($property_id),
                'listing_id'    => $property_id,
                'listing_url'   => get_permalink($property_id)
            );
            houzez_email_type($admin_email, 'admin_update_listing', $args);
        }
    } 
    // Membership payment
    elseif ($enable_paid_submission == 'membership') {
        // Guest user flow
        if (!is_user_logged_in()) {
            $email = sanitize_email($_POST['user_email']);
            $errors = array();

            // Validate email
            if (email_exists($email)) {
                $errors[] = $houzez_local['email_already_registerd'];
            }

            if (!is_email($email)) {
                $errors[] = $houzez_local['invalid_email'];
            }

            if (empty($errors)) {
                // Create username from email
                $username_parts = explode('@', $email);
                $username = $username_parts[0];

                if (username_exists($username)) {
                    $username = $username . rand(5, 999);
                }

                $random_password = wp_generate_password(12, false);
                $user_id = wp_create_user($username, $random_password, $email);

                if (!is_wp_error($user_id)) {
                    $user = get_user_by('id', $user_id);

                    houzez_update_profile($user_id);
                    houzez_wp_new_user_notification($user_id, $random_password);
                    
                    // Register as agent if enabled
                    $user_as_agent = houzez_option('user_as_agent');
                    if ($user_as_agent == 'yes') {
                        houzez_register_as_agent($username, $email, $user_id);
                    }

                    if (!is_wp_error($user)) {
                        wp_clear_auth_cookie();
                        wp_set_current_user($user->ID, $user->user_login);
                        wp_set_auth_cookie($user->ID);
                        do_action('wp_login', $user->user_login);

                        $property_id = apply_filters('houzez_submit_listing', $new_property);

                        $args = array(
                            'listing_title' => get_the_title($property_id),
                            'listing_id'    => $property_id,
                            'listing_url'   => get_permalink($property_id),
                        );

                        // Send email notifications
                        if ($submission_action != 'update_property') {
                            houzez_email_type($user_email, 'free_submission_listing', $args);
                            houzez_email_type($admin_email, 'admin_free_submission_listing', $args);
                        } elseif ($submission_action == 'update_property' && houzez_option('edit_listings_admin_approved') == 'yes') {
                            houzez_email_type($admin_email, 'admin_update_listing', $args);
                        }

                        // Redirect to package selection
                        $separator = (parse_url($select_packages_link, PHP_URL_QUERY) == NULL) ? '?' : '&';
                        $parameter = ''; // 'prop-id=' . $property_id removed as not needed per original
                        wp_redirect($select_packages_link . $separator . $parameter);
                        exit;
                    }
                }
            }
        } else {
            // Logged in user flow
            $property_id = apply_filters('houzez_submit_listing', $new_property);
            
            $args = array(
                'listing_title' => get_the_title($property_id),
                'listing_id'    => $property_id,
                'listing_url'   => get_permalink($property_id)
            );

            // Send email notifications
            if ($submission_action != 'update_property') {
                houzez_email_type($user_email, 'free_submission_listing', $args);
                houzez_email_type($admin_email, 'admin_free_submission_listing', $args);
            } elseif ($submission_action == 'update_property' && houzez_option('edit_listings_admin_approved') == 'yes') {
                houzez_email_type($admin_email, 'admin_update_listing', $args);
            }

            // Check if user has active membership
            if (houzez_user_has_membership($packageUserId)) {
                if (!empty($submit_property_link)) {
                    $submit_property_link = add_query_arg('edit_property', $property_id, $submit_property_link);
                    $separator = (parse_url($submit_property_link, PHP_URL_QUERY) == NULL) ? '?' : '&';

                    $parameter = 'success=1';
                    if ($submission_action == 'update_property') {
                        $parameter = 'updated=1';
                    }
                    
                    wp_redirect($submit_property_link . $separator . $parameter);
                    exit;
                }
            } else {
                // Redirect to package selection
                $separator = (parse_url($select_packages_link, PHP_URL_QUERY) == NULL) ? '?' : '&';
                $parameter = ''; // 'prop-id=' . $property_id removed as not needed per original
                wp_redirect($select_packages_link . $separator . $parameter);
                exit;
            }
        }
    } 
    // Free submission
    else {
        // Guest user flow
        if (!is_user_logged_in()) {
            $email = sanitize_email($_POST['user_email']);
            $errors = array();

            // Validate email
            if (email_exists($email)) {
                $errors[] = $houzez_local['email_already_registerd'] ?? 'Email already registered';
            }

            if (!is_email($email)) {
                $errors[] = $houzez_local['invalid_email'] ?? 'Invalid email';
            }

            if (empty($errors)) {
                // Create username from email
                $username_parts = explode('@', $email);
                $username = $username_parts[0];

                if (username_exists($username)) {
                    $username = $username . rand(5, 999);
                }

                $random_password = wp_generate_password(12, false);
                $user_id = wp_create_user($username, $random_password, $email);

                if (!is_wp_error($user_id)) {
                    $user = get_user_by('id', $user_id);

                    houzez_update_profile($user_id);
                    houzez_wp_new_user_notification($user_id, $random_password);
                    
                    // Register as agent if enabled
                    $user_as_agent = houzez_option('user_as_agent');
                    if ($user_as_agent == 'yes') {
                        houzez_register_as_agent($username, $email, $user_id);
                    }

                    if (!is_wp_error($user)) {
                        wp_clear_auth_cookie();
                        wp_set_current_user($user->ID, $user->user_login);
                        wp_set_auth_cookie($user->ID);
                        do_action('wp_login', $user->user_login);

                        $property_id = apply_filters('houzez_submit_listing', $new_property);

                        $args = array(
                            'listing_title' => get_the_title($property_id),
                            'listing_id'    => $property_id,
                            'listing_url'   => get_permalink($property_id)
                        );

                        // Send email notifications
                        if ($submission_action != 'update_property' || ($submission_action == 'update_property' && $is_draft == 'draft')) {
                            houzez_email_type($user_email, 'free_submission_listing', $args);
                            houzez_email_type($admin_email, 'admin_free_submission_listing', $args);
                        } elseif ($submission_action == 'update_property' && houzez_option('edit_listings_admin_approved') == 'yes') {
                            houzez_email_type($admin_email, 'admin_update_listing', $args);
                        }

                        // Redirect after submission
                        if (!empty($thankyou_page_link)) {
                            wp_redirect($thankyou_page_link);
                            exit;
                        } else {
                            if (!empty($dashboard_listings)) {
                                $separator = (parse_url($dashboard_listings, PHP_URL_QUERY) == NULL) ? '?' : '&';
                                $parameter = ($updated_successfully) ? '' : '';
                                wp_redirect($dashboard_listings . $separator . $parameter);
                                exit;
                            }
                        }
                    }
                }
            }
        } else {
            // Logged in user flow
            $property_id = apply_filters('houzez_submit_listing', $new_property);

            $args = array(
                'listing_title' => get_the_title($property_id),
                'listing_id'    => $property_id,
                'listing_url'   => get_permalink($property_id)
            );

            // Send email notifications
            if ($submission_action != 'update_property' || ($submission_action == 'update_property' && $is_draft == 'draft')) {
                houzez_email_type($user_email, 'free_submission_listing', $args);
                houzez_email_type($admin_email, 'admin_free_submission_listing', $args);
            } elseif ($submission_action == 'update_property' && houzez_option('edit_listings_admin_approved') == 'yes') {
                houzez_email_type($admin_email, 'admin_update_listing', $args);
            }

            // Redirect to the property edit page
            if (!empty($submit_property_link)) {
                $submit_property_link = add_query_arg('edit_property', $property_id, $submit_property_link);
                $separator = (parse_url($submit_property_link, PHP_URL_QUERY) == NULL) ? '?' : '&';

                $parameter = 'success=1';
                if ($submission_action == 'update_property') {
                    $parameter = 'updated=1';
                }
                
                wp_redirect($submit_property_link . $separator . $parameter);
                exit;
            }
        }
    }
}

// Load the appropriate header
if (is_user_logged_in()) {
    get_header('dashboard');
} else {
    get_header();
}

$houzez_loggedin = is_user_logged_in();

// Determine if sidebar should be shown
$show_sidebar = false;
$col_class = "col-12";
if (houzez_edit_property() || houzez_can_manage() || houzez_is_editor()) { 
    $col_class = "col-12 col-lg-10";
    $show_sidebar = true;
}

// User is logged in
if ($houzez_loggedin) { 
?>

<!-- Load the dashboard sidebar -->
<?php get_template_part('template-parts/dashboard/sidebar'); ?>

<div class="dashboard-right">
    <!-- Dashboard Topbar --> 
    <?php get_template_part('template-parts/dashboard/topbar'); ?>

    <div class="dashboard-content">
        
        <?php
        // Show verification message if required
        if ($is_user_verification_required) {
            $verification_message = houzez_option('verification_message', esc_html__('Your account must be verified before you can add new properties. Please complete the verification process.', 'houzez'));
            $profile_link = houzez_get_template_link_2('template/user_dashboard_profile.php');
            $verification_link = add_query_arg('hpage', 'verification', $profile_link);
            
            echo '<div class="block-wrap">
            <div class="block-content-wrap">';
            
            echo '<div class="alert alert-warning" role="alert">' . esc_html($verification_message) . '</div>';
            echo '<a class="btn btn-primary verification-link" href="' . esc_url($verification_link) . '">' . esc_html__('Go to Verification', 'houzez') . '</a>';
            
            echo '</div>
            </div>';
            return;
        }
        ?>

        <div class="heading d-flex align-items-center justify-content-between">
            <div class="heading-text">
                <h2><?php echo houzez_option('dsh_create_listing', 'Create a Listing'); ?></h2>
            </div>

            <?php 
            // Show different buttons based on whether editing an existing property
            if (houzez_edit_property()) { 
                $view_link = isset($_GET['edit_property']) ? get_permalink(intval($_GET['edit_property'])) : '';
            ?>
                <div class="d-flex gap-2">
                    <a href="<?php echo esc_url($view_link); ?>" target="_blank" class="btn btn-primary-outlined btn-sm-custom"><?php echo houzez_option('fal_view_property', esc_html__('View Property', 'houzez')); ?></a>
                    <button id="save_as_draft" class="btn btn-primary btn-sm-custom" data-action="save_as_draft">
                        <?php get_template_part('template-parts/loader'); ?>
                        <?php echo houzez_option('fal_save_draft', 'Save'); ?>
                    </button>
                </div>
                <style>
                    @media (max-width: 767px) {
                        .btn-sm-custom {
                            padding: 5px 8px;
                            font-size: 0.775rem;
                            line-height: 20px;
                        }
                    }
                </style>
            <?php } else { ?>
                <button id="save_as_draft" class="btn btn-primary-outlined" data-action="save_as_draft">
                    <?php get_template_part('template-parts/loader'); ?>
                    <?php echo houzez_option('fal_save_draft', 'Save as Draft'); ?>
                </button>
            <?php } ?>
        </div>

        <div class="dashboard-content-block-wrap pt-4">
            <div id="messages"></div>
            <div class="row mb-4">
                <div class="<?php echo esc_attr($col_class); ?>">
                    <?php
                    if (is_plugin_active('houzez-theme-functionality/houzez-theme-functionality.php')) {
                        if (houzez_edit_property()) {
                            get_template_part('template-parts/dashboard/submit/edit-property-form');
                        } else {
                            get_template_part('template-parts/dashboard/submit/submit-property-form');
                        }
                    } else {
                        echo $houzez_local['houzez_plugin_required'] ?? 'Houzez Theme Functionality plugin is required';
                    }
                    ?>
                </div>

                <?php if ($show_sidebar) { ?>  
                <div class="col-2 d-none d-lg-block">
                    <?php 
                    if (houzez_edit_property()) {
                        get_template_part('template-parts/dashboard/submit/partials/menu-edit-property');
                    } else {
                        get_template_part('template-parts/dashboard/submit/partials/author');
                    }
                    ?>
                </div>
                <?php } ?>
            </div> <!-- row -->
        </div> <!-- dashboard-content-block-wrap -->
    </div> <!-- dashboard-content -->
</div> <!-- dashboard-right -->

<?php
} else { // User is not logged in
?>

<section class="dashboard-content-block-wrap pt-4">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <?php 
                if ($create_listing_login_required == 'yes') {
                    get_template_part('template-parts/dashboard/submit/partials/login-required');
                } else {
                    get_template_part('template-parts/dashboard/submit/submit-property-form');
                }
                ?>
            </div>
        </div><!-- row -->
    </div><!-- container -->
</section><!-- frontend-submission-page -->

<?php
} // End logged-in else

// Load map scripts based on the selected map system
if (houzez_get_map_system() == 'google') {
    if (houzez_option('googlemap_api_key') != "") {
        wp_enqueue_script('houzez-submit-google-map', get_theme_file_uri('/js/submit-property-google-map.js'), array('jquery'), HOUZEZ_THEME_VERSION, true);
    }
} elseif (houzez_get_map_system() == 'mapbox') {
    wp_enqueue_script('houzez-submit-mapbox', get_theme_file_uri('/js/submit-property-mapbox.js'), array('jquery', 'mapbox-gl', 'mapbox-gl-language'), HOUZEZ_THEME_VERSION, true);
} else {
    wp_enqueue_script('houzez-submit-osm', get_theme_file_uri('/js/submit-property-osm.js'), array('jquery'), HOUZEZ_THEME_VERSION, true);
}

// Load the appropriate footer
if (is_user_logged_in()) {
    get_footer('dashboard');
} else {
    get_footer();
}
?>