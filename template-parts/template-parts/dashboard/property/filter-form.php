<?php
global $dashboard_properties;
?>
<div class="mb-3">
  <label class="form-label"><?php echo esc_html__('Price Range', 'houzez'); ?></label>
  <div class="d-flex align-items-center gap-2">
    <input type="number" name="min-price" value="<?php echo isset($_GET['min-price']) ? esc_attr($_GET['min-price']) : ''; ?>" placeholder="<?php echo esc_html__('Min', 'houzez'); ?>" class="form-control">
    <span>-</span>
    <input type="number" name="max-price" value="<?php echo isset($_GET['max-price']) ? esc_attr($_GET['max-price']) : ''; ?>" placeholder="<?php echo esc_html__('Max', 'houzez'); ?>" class="form-control">
  </div>
</div>

<div class="mb-3">
  <label class="form-label"><?php echo esc_html__('Property ID', 'houzez'); ?></label>
  <div class="position-relative">
    <input type="text" name="property_id" value="<?php echo isset($_GET['property_id']) ? esc_attr($_GET['property_id']) : ''; ?>" placeholder="<?php echo esc_html__('Enter Property ID', 'houzez'); ?>" class="form-control">
  </div>
</div>

<div class="mb-3">
  <label class="form-label"><?php echo esc_html__('Property Type', 'houzez'); ?></label>
  <div class="position-relative">
    <select name="type[]" class="form-select">
      <option value=""><?php echo esc_html__('All Types', 'houzez'); ?></option>
      <?php
      $property_type = isset($_GET['type']) ? $_GET['type'] : '';
      houzez_get_search_taxonomies('property_type', $property_type, $args = array() );
      ?>
    </select>
  </div>
</div>

<div class="mb-3">
  <label class="form-label"><?php echo esc_html__('Status', 'houzez'); ?></label>
  <div class="position-relative">
    <select name="status[]" class="form-select">
      <option value=""><?php echo esc_html__('All Status', 'houzez'); ?></option>
      <?php
      $status = isset($_GET['status']) ? $_GET['status'] : '';
      houzez_get_search_taxonomies('property_status', $status, $args = array() );
      ?>
    </select>
  </div>
</div>

<div class="mb-3">
  <label class="form-label"><?php echo esc_html__('Featured', 'houzez'); ?></label>
  <div class="position-relative">
    <select name="featured" class="form-select">
      <option value=""><?php echo esc_html__('All Properties', 'houzez'); ?></option>
      <option value="1" <?php echo (isset($_GET['featured']) && $_GET['featured'] == '1') ? 'selected' : ''; ?>><?php echo esc_html__('Featured', 'houzez'); ?></option>
      <option value="0" <?php echo (isset($_GET['featured']) && $_GET['featured'] == '0') ? 'selected' : ''; ?>><?php echo esc_html__('Not Featured', 'houzez'); ?></option>
    </select>
  </div>
</div>

<div class="d-flex gap-2">
  <button type="submit" class="btn btn-primary w-100"><?php echo esc_html__('Apply Filters', 'houzez'); ?></button>
  <button type="button" onclick="window.location.href='<?php echo esc_url($dashboard_properties); ?>'" class="btn btn-primary-outlined"><?php echo esc_html__('Reset', 'houzez'); ?></button>
</div>