<?php
global $insights_stats, $houzez_local;

$views = $insights_stats['views'];

$lastday = $views['lastday'];
$lasttwo = $views['lasttwo'];
$lasttwo = $lasttwo - $lastday;

$lastweek = $views['lastweek'];
$last2week = $views['last2week'];
$last2week = $last2week - $lastweek;

$lastmonth = $views['lastmonth'];
$last2month = $views['last2month'];
$last2month = $last2month - $lastweek;

?>
<div class="block-wrap">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title mb-0"><?php esc_html_e('Views', 'houzez'); ?></h5>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="stats-box bg-light rounded p-3">
                    <p class="small"><?php esc_html_e('Last 24 Hours', 'houzez'); ?></p>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3><?php echo number_format_i18n($lastday); ?></h3>
                        <?php houzez_views_percentage($lasttwo, $lastday); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-box bg-light rounded p-3">
                    <p class="small"><?php esc_html_e('Last 7 Days', 'houzez'); ?></p>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3><?php echo number_format_i18n($views['lastweek']); ?></h3>
                        <?php houzez_views_percentage($last2week, $lastweek); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-box bg-light rounded p-3">
                    <p class="small"><?php esc_html_e('Last 30 Days', 'houzez'); ?></p>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3><?php echo number_format_i18n($views['lastmonth']); ?></h3>
                        <?php houzez_views_percentage($last2month, $lastmonth); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
