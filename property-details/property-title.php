<?php global $top_area; ?>
<div class="page-title-wrap d-none d-md-block" role="banner">
    <div class="container">
        <nav class="d-flex align-items-center justify-content-between" role="navigation">
            <?php get_template_part('template-parts/page/breadcrumb'); ?>
            <?php get_template_part('property-details/partials/tools'); ?>
        </nav>
        <div class="property-header-wrap d-flex align-items-start justify-content-between mt-3" role="main">
            <div class="property-title-wrap d-flex flex-column gap-2">
                <?php get_template_part('property-details/partials/title'); ?>
                <?php get_template_part('property-details/partials/item-address'); ?>
                <?php get_template_part('property-details/partials/item-labels'); ?>
            </div>
            <?php get_template_part('property-details/partials/item-price'); ?>
        </div>
    </div>
</div>