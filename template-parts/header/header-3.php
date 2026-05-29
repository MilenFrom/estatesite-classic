<?php $sticky_header = houzez_option('main-menu-sticky', 0); ?>
<div class="header-desktop header-v3">
    <div class="header-top">
        <div class="container-fluid">
            <div class="header-inner-wrap">
                <div class="navbar d-flex align-items-center">
                    <?php get_template_part('template-parts/header/partials/logo'); ?>
                    <?php get_template_part('template-parts/header/partials/phone-number'); ?>
                </div><!-- navbar -->
            </div>
        </div>
    </div><!-- .header-top -->
    <div id="header-section" class="header-bottom" data-sticky="<?php houzez_header_sticky() ?>">
        <div class="container-fluid">
            <div class="header-inner-wrap">
                <div class="navbar d-flex flex-row align-items-center h-100">
                    <nav class="main-nav navbar-expand-lg flex-grow-1 on-hover-menu with-angle-icon h-100" role="navigation" aria-label="Main Navigation">
                        <?php get_template_part('template-parts/header/partials/nav'); ?>
                    </nav><!-- main-nav -->
                    <?php get_template_part('template-parts/header/user-nav'); ?>
                </div><!-- navbar -->
            </div>
        </div>
    </div><!-- .header-bottom -->
</div><!-- .header-v3 -->

<?php if( houzez_option('hd3_callus', 0) != 0 ) { ?>
<div class="header-v3 header-v3-mobile">
    <?php get_template_part('template-parts/header/partials/phone-number'); ?>
</div>
<?php } ?>