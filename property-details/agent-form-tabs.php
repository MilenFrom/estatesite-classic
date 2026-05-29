<div class="property-form-tabs-wrap mb-4">
	<div class="property-form-tabs">
        <ul class="nav nav-tabs">
            <li class="nav-item flex-fill">
                <a class="nav-link text-center py-3 active" data-bs-toggle="tab" href="#tab_tour" role="tab">
                    <span class="tab-title"><?php esc_html_e('Schedule a tour', 'houzez'); ?></span>
                </a>
            </li>
            <li class="nav-item flex-fill">
                <a class="nav-link text-center py-3" data-bs-toggle="tab" href="#tab_agent_form" role="tab">
                    <span class="tab-title"><?php esc_html_e('Request Info', 'houzez'); ?></span>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="property-form-tabs-tab-pane tab-pane fade show active" id="tab_tour" role="tabpanel">
                <?php get_template_part('property-details/partials/schedule-tour-sidebar-form'); ?>
            </div>
            <div class="property-tabs-module-tab-pane tab-pane fade" id="tab_agent_form" role="tabpanel">
                <div class="widget-wrap p-4 widget-property-form">
                    <?php get_template_part('property-details/agent-form'); ?>
                </div>
            </div>
        </div>
    </div><!-- property-form-tabs -->
</div><!-- property-form-tabs-wrap -->