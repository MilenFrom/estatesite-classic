<?php $sticky_header = houzez_option('main-menu-sticky', 0); ?>
<div class="header-desktop header-v2">
    <div class="header-top">
        <div class="container">
            <div class="header-inner-wrap">
                <div class="navbar d-flex align-items-center">
                    <?php get_template_part('template-parts/header/partials/logo'); ?>
                    <?php get_template_part('template-parts/header/partials/contact-info'); ?>
                </div><!-- navbar -->
            </div>
        </div>
    </div><!-- .header-top -->
    <div id="header-section" class="header-bottom" data-sticky="<?php houzez_header_sticky() ?>">
        <div class="container">
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
</div><!-- .header-v2 -->

<div class="header-v2 header-v2-mobile">
    <?php get_template_part('template-parts/header/partials/contact-info'); ?>    
</div><!-- header-v2-mobile -->