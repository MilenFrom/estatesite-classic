<?php $advanced_btn_type = houzez_option('advanced_btn_type', 'icon');?>

<?php if( $advanced_btn_type == 'icon') { ?>
        <button class="btn advanced-search-btn w-100" 
                type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#advanced-search-filters"
                aria-expanded="false"
                aria-controls="advanced-search-filters"
                aria-label="<?php echo houzez_option('srh_btn_adv', 'Advanced');?>">
                <i class="houzez-icon icon-Filter-Faders" aria-hidden="true"></i>
        </button>
<?php } else { ?>
        <a class="btn advanced-search-btn w-100" data-bs-toggle="collapse" href="#advanced-search-filters">
        <i class="houzez-icon icon-cog me-1"></i> <?php echo houzez_option('srh_btn_adv', 'Advanced');?>
        </a>
<?php } ?>
