<?php if( houzez_can_manage() || houzez_is_editor() ) { ?>
<div class="mb-3">
    <label class="form-label"><?php echo esc_html__( 'Author', 'houzez' )?></label>
    <select id="property-author-js" class="selectpicker form-control" data-live-search="true" data-size="5">
        <?php houzez_property_authors_list(); ?>
    </select>
</div>
<?php } ?>