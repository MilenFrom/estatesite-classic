<?php
// Sanitize and validate the 'lead-id' input
$belong_to = isset($_GET['lead-id']) ? intval($_GET['lead-id']) : 0; // Using intval() to convert to integer and ensure it's safe
$notes = Houzez_CRM_Notes::get_notes($belong_to, 'lead');
?>
<div class="inquiry-data">
    
    <div class="d-flex flex-column gap-3 pb-4">
        <label><?php esc_html_e('Add a note', 'houzez'); ?></label>
        <textarea class="form-control" id="note" rows="3" placeholder="<?php esc_html_e('Write your note here...', 'houzez'); ?>" style="height: 100px;"></textarea>
        <input type="hidden" id="belong_to" value="<?php echo intval($belong_to); ?>">
        <input type="hidden" id="note_type" value="lead">
        <input type="hidden" id="note_security" value="<?php echo wp_create_nonce('note_add_nonce') ?>">
        <button id="enquiry_note" class="btn btn-primary align-self-start">
            <?php get_template_part('template-parts/loader'); ?>
            <?php esc_html_e('Save', 'houzez'); ?>
        </button>
    </div>
    
    <div id="notes-main-wrap">
    <?php
    if(!empty($notes)) {
        foreach ($notes as $data) {
            // Interpret database time as UTC
            $datetime = houzez_mysql_to_wp_timestamp( $data->time, 'utc' );
    ?>
        <div class="note-block border-top py-4 border-top">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <!-- <h6 class="mb-1"><?php esc_html_e('Note', 'houzez'); ?></h6> -->
                    <p class="text-muted mb-0 activity-time">
                        <?php printf( __( '%s ago', 'houzez' ), human_time_diff( $datetime, time() ) ); ?>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="#" class="text-muted delete_note" data-id="<?php echo intval($data->note_id); ?>">
                        <i class="houzez-icon icon-bin"></i>
                    </a>
                </div>
            </div>
            <p class="mb-0"><?php echo esc_attr($data->note); ?></p>
        </div>
    <?php
        }
    }
    ?>
    </div>
</div>