<?php
global $houzez_search_data;
$searches = Houzez_Leads::get_lead_saved_searches();
$total_records = $searches['data']['total_records'] ?? 0;

if(!empty($searches['data']['results'])) { ?>
<div class="inquiry-data">
    <div class="houzez-data-table">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
            <thead>
                <tr>
                    <th><?php esc_html_e('Search Parameters', 'houzez'); ?></th>
                    <th></th>
                    <th><?php esc_html_e('Date', 'houzez'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($searches['data']['results'] as $houzez_search_data) {
                    
                    get_template_part( 'template-parts/dashboard/board/leads/saved-search-item' );
                }?>
            </tbody>
            </table>
        </div>
    </div>
    <?php get_template_part('template-parts/dashboard/board/pagination', '', array('total_records' => $total_records)); ?>
</div>
<?php } else { ?>
    <div class="inquiry-data pb-1">
    <div class="alert alert-info mb-4" role="alert">
        <i class="houzez-icon icon-info-circle me-2"></i>
        <?php esc_html_e('No record found.', 'houzez'); ?>
    </div>
    </div>
<?php } ?>

