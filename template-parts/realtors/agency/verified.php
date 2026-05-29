<?php $is_verified = get_post_meta( get_the_ID(), 'fave_agency_verified', true );?>

<?php if( $is_verified ) { ?>
<span class="badge btn-secondary agent-verified-badge">
    <i class="houzez-icon icon-check-circle-1 me-1"></i> <?php esc_html_e('Verified', 'houzez'); ?>
</span> 
<?php } ?>