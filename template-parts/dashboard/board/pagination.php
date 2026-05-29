<div class="houzez-sorting d-flex align-items-center justify-content-between p-4">
    <?php 
    $total_records = isset($args['total_records']) ? $args['total_records'] : 10;
    $records_array = array(5,10,20,50,100);
    
    $per_page = isset($_GET['records']) && in_array( intval($_GET['records']), $records_array ) ? intval($_GET['records']) : 10;
    $current_page = max(1, get_query_var('cpage') ?: get_query_var('cpage'));
    $start = ($current_page - 1) * $per_page + 1;
    $end = min($total_records, $current_page * $per_page);
    
    $total = isset($_GET['records']) ? $_GET['records'] : 10; ?>
    <p class="m-0 small"><?php echo sprintf( esc_html__('Showing %1$s-%2$s of %3$s items', 'houzez'), $start, $end, $total_records ); ?></p>
    <div class="relative">
        <select class="form-control" onchange="var p=new URLSearchParams(location.search);p.set('records',this.value);p.delete('cpage');location.search=p.toString();">
            <?php foreach ($records_array as $pp): ?>
                <option value="<?php echo esc_attr($pp); ?>" <?php selected($per_page, $pp); ?>><?php echo esc_html(sprintf(__('%s per page','houzez'), $pp)); ?></option>
            <?php endforeach; ?>
        </select>
        <span class="sort-arrow"><i class="houzez-icon icon-arrow-down-1"></i></span>
    </div>
    
    <?php
    $total_pages = ceil($total_records / $per_page);
    houzez_crm_pagination($total_pages, $current_page);
    ?>
</div> 