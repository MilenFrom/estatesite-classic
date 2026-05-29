<?php
global $properties_query;
$total_posts = $properties_query->found_posts;
$records_array = array(10,20,50,100);
?>
<div class="houzez-sorting d-flex align-items-center justify-content-between p-4">
  <?php
    $per_page = isset($_GET['per_page']) && in_array( intval($_GET['per_page']), $records_array ) ? intval($_GET['per_page']) : 10;
    $current_page = max(1, get_query_var('paged') ?: get_query_var('page'));
    $start = ($current_page - 1) * $per_page + 1;
    $end = min($total_posts, $current_page * $per_page);
  ?>
  <p class="m-0 small"><?php echo sprintf( esc_html__('Showing %1$s-%2$s of %3$s items', 'houzez'), $start, $end, $total_posts ); ?></p>
  <div class="relative">
    <select class="form-control" onchange="var p=new URLSearchParams(location.search);p.set('per_page',this.value);p.delete('paged');location.search=p.toString();">
      <?php foreach ($records_array as $pp): ?>
        <option value="<?php echo esc_attr($pp); ?>" <?php selected($per_page, $pp); ?>><?php echo esc_html(sprintf(__('%s per page','houzez'), $pp)); ?></option>
      <?php endforeach; ?>
    </select>
    <span class="sort-arrow"><i class="houzez-icon icon-arrow-down-1"></i></span>
  </div>
  <?php houzez_pagination_dashboard( $properties_query->max_num_pages ); ?>
</div> 