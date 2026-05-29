<?php 
$header_width = houzez_option('header_1_width');
if(houzez_is_half_map()) {
    $header_width = 'container-fluid';
}
?>
<div id="header-section" class="header-desktop header-v1" data-sticky="<?php houzez_header_sticky() ?>">
    <div class="<?php echo esc_attr($header_width); ?>">
        <div class="header-inner-wrap">
            <div class="navbar d-flex flex-row align-items-center h-100">
                <?php get_template_part('template-parts/header/partials/logo'); ?>

                <nav class="main-nav navbar-expand-lg flex-grow-1 on-hover-menu with-angle-icon h-100" role="navigation">
                    <?php get_template_part('template-parts/header/partials/nav'); ?>
                </nav><!-- main-nav -->

                <?php get_template_part('template-parts/header/user-nav'); ?>
            </div><!-- navbar -->
        </div><!-- header-inner-wrap -->
    </div><!-- .container -->    
</div><!-- .header-v1 -->