<?php
get_header('theme-builder'); ?>
<div class="houzez-themebuilder-wrapper houzez-tb-single-listing houzez-themebuilder-<?php the_ID(); ?>">
    <?php \Elementor\Plugin::$instance->modules_manager->get_modules( 'page-templates' )->print_content(); ?>
</div>
<?php
get_footer('theme-builder');
