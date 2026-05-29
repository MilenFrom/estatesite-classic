<?php
$dashboard_logo = houzez_option( 'dashboard_logo', false, 'url' ); ?>
<div class="dashboard-sidebar"> 
	<div class="sidebar-logo">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo">
			<img src="<?php echo esc_url($dashboard_logo); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
		</a>
		<a href="javascript:void(0)" class="crose-btn d-xl-none d-flex"><i class="houzez-icon icon-close"></i></a>
	</div>
	<?php get_template_part('template-parts/dashboard/dashboard-menu'); ?>
</div>