<?php
// Fetch the uploaded CSV files from the user meta
$current_user_id = get_current_user_id();
$uploaded_files = get_user_meta($current_user_id, 'houzez_crm_leads_uploaded_csvs', true);
$dashboard_crm = houzez_get_template_link_2('template/user_dashboard_crm.php');
?>
<header class="header-main-wrap dashboard-header-main-wrap">
    <div class="dashboard-header-wrap">
        <div class="d-flex align-items-center">
            <div class="dashboard-header-left flex-grow-1">
                <h2 class="mb-3"><?php echo esc_html__( 'Import', 'houzez' ); ?></h2>         
            </div><!-- dashboard-header-left -->
        </div><!-- d-flex -->
    </div><!-- dashboard-header-wrap -->
</header><!-- .header-main-wrap -->
<section class="dashboard-content-wrap">
    <div class="dashboard-content-inner-wrap">
        <div class="dashboard-content-block-wrap">
            
            <div class="row">
                <div class="col col-lg-8 col-sm-12">
                    <div class="dashboard-content-block">
                        <p class="mb-3"><?php esc_html_e('This page allows you to easily import CSV files into our system. Follow these simple steps to seamlessly transfer your data:', 'houzez'); ?></p>

                        <ol class="mb-0">
                            <li><?php echo wp_kses(__( '<strong>Prepare your CSV file:</strong> Ensure your file is correctly formatted with clear column headers and accurate data.', 'houzez' ), houzez_allowed_html()); ?></li>
                            <li><?php echo wp_kses(__( '<strong>Upload your CSV file:</strong> Click the "Choose File" button to select your CSV file from your device.', 'houzez' ), houzez_allowed_html()); ?></li>
                        </ol>
                    </div><!-- dashboard-content-block -->

                    <?php
                    $is_import = $_GET['import'] ?? '';

                    if( $is_import == 1 ) { 
                        $total_files = count($uploaded_files);
                        $selected = 1;
                        $current = 0;

                        $get_file = $_GET['file'] ?? '';

                        if( ! empty($get_file) ) {
                            $selected = $get_file;
                        }

                        echo '<div class="dashboard-content-block">';
                            // Check if there are any files
                            if (!empty($uploaded_files) && is_array($uploaded_files)) {

                                // Define a custom sort function
                                usort($uploaded_files, function($a, $b) {
                                    return strtotime($b['upload_date']) - strtotime($a['upload_date']);
                                });

                                echo '<form id="csv-file-select-form">';
                                echo '<div class="d-flex">';
                                echo '<div style="margin-right:10px; max-width: 100%; width:100%;">';
                                echo '<select id="uploaded-csv-files" title="'.esc_html__('Select', 'houzez').'" class="selectpicker form-control">';

                                foreach ($uploaded_files as $file) { $current++;

                                    if( ! empty($get_file) ) {
                                        $current = $file['name'];
                                    }

                                    echo '<option '.selected($selected, $current, false).' value="' . esc_attr($file['name']) . '">' . esc_html($file['name']) . '</option>';
                                }

                                echo '</select>';
                                echo '</div>';
                                echo '<button type="button" class="btn btn-primary" id="fetch-data-btn">'.esc_html__('Fetch Data', 'houzez').'</button>';
                                echo '</div>';
                                echo '</form>';
                            } else {
                                echo esc_html__('No files uploaded.', 'houzez');
                            }
                            ?>

                            <div id="mapping-container"></div>
                        </div>

                    <?php
                    } else {
                    ?>

                    <h3 class="mt-5"><?php esc_html_e( 'Previous Imported Files', 'houzez' ); ?></h3>
                    <table class="table dashboard-table dashboard-table-file-import table-lined responsive-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'File Name', 'houzez' ); ?></th>
                                <th><?php esc_html_e( 'Import Date', 'houzez' ); ?></th>
                                <th class="action-col"><?php esc_html_e( 'Actions', 'houzez' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            // Check if there are any files
                            if (!empty($uploaded_files) && is_array($uploaded_files)) { 

                                // Define a custom sort function
                                usort($uploaded_files, function($a, $b) {
                                    return strtotime($b['upload_date']) - strtotime($a['upload_date']);
                                });

                                foreach ($uploaded_files as $file):
                                    // Use helper to properly interpret time (stored in UTC)
                                    $datetime_unix = houzez_mysql_to_wp_timestamp( $file['upload_date'], 'utc' );
                                    $get_date = houzez_return_formatted_date($datetime_unix);
                                    $get_time = houzez_get_formatted_time($datetime_unix);

                                    $import_link = add_query_arg( array('hpage' => 'import-leads', 'file' => $file['name'], 'import' => 1 ), $dashboard_crm );

                                    $delete_nonce = wp_create_nonce('delete_leads_csv_file_nonce');
                                ?>
                                <tr>
                                    <td data-label="<?php esc_html_e( 'File Name', 'houzez' ); ?>"><?php echo esc_html($file['name']); ?></td>
                                    <td data-label="<?php esc_html_e( 'Import Date', 'houzez' ); ?>">
                                        <?php echo esc_attr($get_date); ?><br>
                                    <?php echo esc_html__('at', 'houzez'); ?> <?php echo esc_attr($get_time); ?></td>
                                    <td class="property-table-actions" data-label="<?php esc_html_e( 'Actions', 'houzez' ); ?>">
                                        <div class="dropdown property-action-menu">
                                            <button class="btn btn-primary-outlined dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false"><?php esc_html_e( 'Actions', 'houzez' ); ?></button>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" data-file="<?php echo esc_html($file['name']); ?>" href="<?php echo esc_url($import_link); ?>"><?php esc_html_e('Import', 'houzez'); ?></a>
                                                <a class="delete-lead-csv-js dropdown-item" data-nonce="<?php echo esc_attr($delete_nonce); ?>" data-file="<?php echo esc_html($file['name']); ?>" href="#"><?php esc_html_e('Delete', 'houzez'); ?></a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>

                            <?php } else {

                                echo '<tr>';
                                echo '<td colspan="3">'. esc_html__('No files uploaded.', 'houzez').'</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table><!-- dashboard-table -->
                    <?php } ?>
                </div>
                <div class="col col-lg-4 col-sm-12">
                    <div class="dashboard-content-block">
                        <form name="import-csv" action="" method="post" enctype="multipart/form-data">
                            <?php wp_nonce_field('houzez_crm_leads_upload_nonce', 'houzez_crm_leads_nonce_field'); ?>
                            <div class="mb-3">
                                <input type="file" class="form-control" name="csv_import" accept=".csv" id="csv-file-input">
                                <input type="hidden" name="action" value="houzez_crm_import_leads">
                            </div>
                            <button class="btn btn-primary btn-full-width" type="submit" id="upload-leads-csv"><span class="btn-loader houzez-loader-js"></span><?php echo esc_html__( 'Upload', 'houzez' )?></button>
                        </form>
                    </div><!-- dashboard-content-block -->
                </div>
            </div>

        </div><!-- dashboard-content-block-wrap -->
    </div><!-- dashboard-content-inner-wrap -->
</section><!-- dashboard-content-wrap -->
<section class="dashboard-side-wrap">
    <?php get_template_part('template-parts/dashboard/side-wrap'); ?>
</section>