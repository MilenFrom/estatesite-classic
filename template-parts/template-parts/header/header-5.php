<?php 
$header_width = houzez_option('header_5_layout');
?>
<div class="header-desktop header-v5">
    <div class="header-top">
        <div class="<?php echo esc_attr($header_width); ?>">
            <div class="header-inner-wrap">
            <div class="d-flex align-items-center justify-content-between h-100">
                    <?php 
                    if( houzez_option('social-header') != 1 ) {
                        echo '<div class="header-social-icons"></div>';
                    } ?>
                    <?php get_template_part('template-parts/header/partials/social-icons'); ?>
                    <?php get_template_part('template-parts/header/partials/logo'); ?>
                    <?php get_template_part('template-parts/header/user-nav'); ?>
                </div><!-- d-flex -->
            </div>
        </div>
    </div><!-- .header-top -->
    <div id="header-section" class="header-bottom" data-sticky="<?php houzez_header_sticky() ?>">
        <div class="container">
            <div class="header-inner-wrap">
                <div class="d-flex flex-fill navbar-expand-lg align-items-center justify-content-center">
                    <nav class="main-nav navbar-expand-lg on-hover-menu with-angle-icon h-100" role="navigation" aria-label="Main Navigation">
                        <?php get_template_part('template-parts/header/partials/nav'); ?>
                    </nav><!-- main-nav -->
                </div><!-- d-flex -->
            </div>
        </div>
    </div><!-- .header-bottom -->
</div><!-- .header-v5 -->