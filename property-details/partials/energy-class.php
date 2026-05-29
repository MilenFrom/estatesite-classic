<?php
global $energy_class, $ele_settings;

/**
 * Energy class data for property listings
 * Displays energy efficiency information in a structured format
 */

// Check energy mode
$energy_mode = houzez_option('energy_class_mode', 'standard');
$ghg_emissions_class = houzez_get_listing_data('ghg_emissions_class');

// Get energy data from listing
$energy_data = [
    'energy_class' => [
        'value' => $energy_class,
        'title' => isset($ele_settings['energetic_class_title']) && !empty($ele_settings['energetic_class_title']) 
                ? $ele_settings['energetic_class_title'] 
                : houzez_option('spl_energetic_cls', 'Energetic class')
    ],
    'energy_global_index' => [
        'value' => houzez_get_listing_data('energy_global_index'),
        'title' => isset($ele_settings['global_energy_index']) && !empty($ele_settings['global_energy_index']) 
                ? $ele_settings['global_energy_index'] 
                : houzez_option('spl_energy_index', 'Global energy performance index')
    ],
    'renewable_energy_index' => [
        'value' => houzez_get_listing_data('renewable_energy_global_index'),
        'title' => isset($ele_settings['renewable_energy_index']) && !empty($ele_settings['renewable_energy_index']) 
                ? $ele_settings['renewable_energy_index'] 
                : houzez_option('spl_energy_renew_index', 'Renewable energy performance index')
    ],
    'energy_performance' => [
        'value' => houzez_get_listing_data('energy_performance'),
        'title' => isset($ele_settings['energy_performance']) && !empty($ele_settings['energy_performance']) 
                ? $ele_settings['energy_performance'] 
                : houzez_option('spl_energy_build_performance', 'Energy performance of the building')
    ],
    'epc_current_rating' => [
        'value' => houzez_get_listing_data('epc_current_rating'),
        'title' => isset($ele_settings['epc_current_rating']) && !empty($ele_settings['epc_current_rating']) 
                ? $ele_settings['epc_current_rating'] 
                : houzez_option('spl_energy_ecp_rating', 'EPC Current Rating')
    ],
    'epc_potential_rating' => [
        'value' => houzez_get_listing_data('epc_potential_rating'),
        'title' => isset($ele_settings['epc_potential_rating']) && !empty($ele_settings['epc_potential_rating']) 
                ? $ele_settings['epc_potential_rating'] 
                : houzez_option('spl_energy_ecp_p', 'EPC Potential Rating')
    ]
];

// Add GHG emissions data for French/EU mode
if ($energy_mode === 'french_eu') {
    $energy_data['ghg_emissions_class'] = [
        'value' => $ghg_emissions_class,
        'title' => isset($ele_settings['ghg_emissions_title']) && !empty($ele_settings['ghg_emissions_title']) 
                ? $ele_settings['ghg_emissions_title'] 
                : houzez_option('spl_ghg_emissions', 'GHG Emissions Class')
    ];
    $energy_data['ghg_emissions_index'] = [
        'value' => houzez_get_listing_data('ghg_emissions_index'),
        'title' => isset($ele_settings['ghg_emissions_index_title']) && !empty($ele_settings['ghg_emissions_index_title']) 
                ? $ele_settings['ghg_emissions_index_title'] 
                : houzez_option('spl_ghg_emissions_index', 'GHG Emissions')
    ];
}

// Get energy class title for the indicator
$energy_class_title = isset($ele_settings['energy_class_title']) && !empty($ele_settings['energy_class_title']) 
                    ? $ele_settings['energy_class_title'] 
                    : houzez_option('spl_energy_cls', 'Energy class');

// Get GHG title for the indicator
$ghg_class_title = isset($ele_settings['ghg_class_title']) && !empty($ele_settings['ghg_class_title']) 
                    ? $ele_settings['ghg_class_title'] 
                    : houzez_option('spl_ghg_cls', 'GHG emissions');

// Get energy class array from options
$energy_array = houzez_option('energy_class_data', 'A+, A, B, C, D, E, F, G, H'); 
$energy_array = array_map('trim', explode(',', $energy_array));
$total_records = count($energy_array);

// Get GHG emissions array from options
$ghg_array = houzez_option('ghg_emissions_class_data', 'A, B, C, D, E, F, G'); 
$ghg_array = array_map('trim', explode(',', $ghg_array));
$total_ghg_records = count($ghg_array);
?>

<?php 
// Check if there's any energy data to display
$has_energy_data = false;
foreach ($energy_data as $data) {
    if (!empty($data['value'])) {
        $has_energy_data = true;
        break;
    }
}
?>

<?php if ($has_energy_data): ?>
<!-- Energy information list -->
<ul class="class-energy-list list-lined list-unstyled" role="list">
    <?php foreach ($energy_data as $key => $data): ?>
        <?php if (!empty($data['value'])): ?>
            <li class="d-flex justify-content-between">
                <div class="ist-lined-item w-100 py-2 justify-content-between d-flex">
                    <strong><?php echo esc_attr($data['title']); ?>:</strong>
                    <span><?php echo esc_attr($data['value']); ?></span>
                </div>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if ($energy_mode === 'french_eu' && (!empty($energy_class) || !empty($ghg_emissions_class))) : ?>
<!-- Card Layout for French/EU Energy Classes -->
<div class="energy-class-cards-wrapper mt-4">
    <div class="row g-4">
        <?php if (!empty($energy_class)) : ?>
        <!-- DPE Card -->
        <div class="col-md-6">
            <div class="card energy-diagnostic-card h-100 border rounded shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fs-6 text-dark fw-bold mb-2"><?php echo houzez_option('spl_energy_dpe_title', 'Energy Performance Diagnostic (DPE)'); ?></h5>
                    <p class="text-muted small mb-3"><?php echo houzez_option('spl_energy_economical_housing', 'Economical housing'); ?></p>
                    
                    <div class="energy-scale-vertical position-relative p-0">
                        <?php 
                        // Get energy colors from theme options
                        $energy_colors = [];
                        $energy_classes = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
                        for ($i = 0; $i < count($energy_classes); $i++) {
                            $option_key = 'energy_' . ($i + 1) . '_color';
                            $default_colors = ['#33a357', '#79b752', '#c3d545', '#fff12c', '#edb731', '#d66f2c', '#cc232a'];
                            $energy_colors[$energy_classes[$i]] = houzez_option($option_key, $default_colors[$i]);
                        }
                        
                        if (!empty($energy_array)) {
                            $index = 0;
                            foreach ($energy_array as $energy) {
                                $energy = trim($energy);
                                $is_active = ($energy == $energy_class);
                                $color = isset($energy_colors[$energy]) ? $energy_colors[$energy] : '#ccc';

                                // Skip A+ and H for French DPE display, only show A-G
                                if ($energy === 'A+' || $energy === 'H') continue;
                                
                                $bar_class = $is_active ? 'energy-bar active-bar d-flex align-items-center rounded-end' : 'energy-bar d-flex align-items-center rounded-end';
                                echo '<div class="energy-bar-wrapper position-relative mb-1" style="height: 35px;" data-energy="' . esc_attr($energy) . '">';
                                echo '<div class="' . $bar_class . '" style="background-color: ' . $color . '; width: ' . (40 + ($index * 10)) . '%; height: 35px; transition: all 0.3s ease;">';
                                echo '<span class="fw-bold text-white ps-3 fs-6" style="text-shadow: 0 1px 2px rgba(0,0,0,0.2);">' . esc_html($energy) . '</span>';
                                
                                // Show badge with hover tooltip for active class
                                if ($is_active) {
                                    $energy_value = $energy_data['energy_global_index']['value'] ?? '150 kWh/m².an';
                                    $final_energy = $energy_data['renewable_energy_index']['value'] ?? '18 kWh/m².an';
                                    $emissions = $energy_data['ghg_emissions_index']['value'] ?? '18 kg CO₂/m².an';
                                    
                                    echo '<div class="energy-value-badge-wrapper d-inline-flex align-items-center ms-auto me-2 position-relative" style="z-index: 2;">';
                                    echo '<span class="badge bg-white text-dark energy-badge-hover" style="font-size: 12px; font-weight: 600; padding: 4px 8px; cursor: pointer; transition: all 0.3s ease;">' . esc_html($energy_value) . '</span>';
                                    
                                    // Tooltip on hover
                                    echo '<div class="energy-tooltip-hover">';
                                    echo '<div class="tooltip-content bg-white border border-2 border-dark rounded p-3 shadow position-relative" style="min-width: 200px; z-index: 10001;">';
                                    echo '<div class="tooltip-class fs-4 fw-bold text-dark text-center mb-2 pb-2 border-bottom">' . esc_html($energy) . '</div>';
                                    echo '<div class="tooltip-details" style="font-size: 13px;">';
                                    echo '<div class="tooltip-line mb-2">';
                                    echo '<span class="text-secondary" style="font-size: 11px; line-height: 1.3;">' . houzez_option('spl_energy_primary_consumption', 'Primary energy consumption') . '</span><br>';
                                    echo '<span class="text-dark fw-semibold" style="font-size: 14px;">' . esc_html($energy_value) . '</span>';
                                    echo '</div>';
                                    if (!empty($final_energy)) {
                                        echo '<div class="tooltip-line mt-2">';
                                        echo '<span class="text-secondary" style="font-size: 11px; line-height: 1.3;">' . houzez_option('spl_energy_final', 'Final energy') . '</span><br>';
                                        echo '<span class="text-dark fw-semibold" style="font-size: 14px;">' . esc_html($final_energy) . '</span>';
                                        echo '</div>';
                                    }
                                    if (!empty($emissions)) {
                                        echo '<div class="tooltip-line mt-2">';
                                        echo '<span class="text-secondary" style="font-size: 11px; line-height: 1.3;">' . houzez_option('spl_energy_emissions', 'Emissions') . '</span><br>';
                                        echo '<span class="text-dark fw-semibold" style="font-size: 14px;">' . esc_html($emissions) . '</span>';
                                        echo '</div>';
                                    }
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                                
                                echo '</div>';
                                echo '</div>';
                                $index++;
                            }
                        }
                        ?>
                    </div>

                    <p class="text-muted small mt-3 mb-0"><?php echo houzez_option('spl_energy_intensive_housing', 'Energy-intensive housing'); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($ghg_emissions_class)) : ?>
        <!-- GES Card -->
        <div class="col-md-6">
            <div class="card energy-diagnostic-card h-100 border rounded shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fs-6 text-dark fw-bold mb-2"><?php echo houzez_option('spl_ghg_title', 'Greenhouse Gas Emissions Index (GHG)'); ?></h5>
                    <p class="text-muted small mb-3"><?php echo houzez_option('spl_ghg_low_emissions', 'Low GHG emissions'); ?></p>
                    
                    <div class="energy-scale-vertical position-relative p-0">
                        <?php 
                        // Get GHG colors from theme options
                        $ghg_colors = [];
                        $ghg_classes = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
                        for ($i = 0; $i < count($ghg_classes); $i++) {
                            $option_key = 'ghg_' . ($i + 1) . '_color';
                            $default_colors = ['#5d9cd3', '#70b0d9', '#8cc4e3', '#98cdeb', '#5966ab', '#3e4795', '#2d2e7f'];
                            $ghg_colors[$ghg_classes[$i]] = houzez_option($option_key, $default_colors[$i]);
                        }
                        
                        if (!empty($ghg_array)) {
                            $index = 0;
                            foreach ($ghg_array as $ghg) {
                                $ghg = trim($ghg);
                                $is_active = ($ghg == $ghg_emissions_class);
                                $color = isset($ghg_colors[$ghg]) ? $ghg_colors[$ghg] : '#ccc';
                                
                                $bar_class = $is_active ? 'energy-bar ghg-bar active-bar d-flex align-items-center rounded-end' : 'energy-bar ghg-bar d-flex align-items-center rounded-end';
                                echo '<div class="energy-bar-wrapper position-relative mb-1" style="height: 35px;" data-ghg="' . esc_attr($ghg) . '">';
                                echo '<div class="' . $bar_class . '" style="background-color: ' . $color . '; width: ' . (40 + ($index * 10)) . '%; height: 35px; transition: all 0.3s ease;">';
                                echo '<span class="fw-bold text-white ps-3 fs-6" style="text-shadow: 0 1px 2px rgba(0,0,0,0.2);">' . esc_html($ghg) . '</span>';
                                
                                // Show badge with hover tooltip for active class
                                if ($is_active) {
                                    $ghg_value = $energy_data['ghg_emissions_index']['value'] ?? '18 kg CO₂/m².an';
                                    
                                    echo '<div class="energy-value-badge-wrapper d-inline-flex align-items-center ms-auto me-2 position-relative" style="z-index: 2;">';
                                    echo '<span class="badge bg-white text-dark energy-badge-hover" style="font-size: 12px; font-weight: 600; padding: 4px 8px; cursor: pointer; transition: all 0.3s ease;">' . esc_html($ghg_value) . '</span>';
                                    
                                    // Tooltip on hover
                                    echo '<div class="energy-tooltip-hover ghg-tooltip">';
                                    echo '<div class="tooltip-content bg-white border border-2 border-dark rounded p-3 shadow position-relative" style="min-width: 150px; z-index: 10001;">';
                                    echo '<div class="tooltip-class fs-4 fw-bold text-dark text-center mb-2 pb-2 border-bottom">' . esc_html($ghg) . '</div>';
                                    echo '<div class="tooltip-details" style="font-size: 13px;">';
                                    echo '<div class="tooltip-line mb-2">';
                                    echo '<span class="text-secondary" style="font-size: 11px; line-height: 1.3;">' . houzez_option('spl_energy_emissions', 'Emissions') . '</span><br>';
                                    echo '<span class="text-dark fw-semibold" style="font-size: 14px;">' . esc_html($ghg_value) . '</span>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                                
                                echo '</div>';
                                echo '</div>';
                                
                                $index++;
                            }
                        }
                        ?>
                    </div>

                    <p class="text-muted small mt-3 mb-0"><?php echo houzez_option('spl_ghg_high_emissions', 'High GHG emissions'); ?></p>
                    
                    <?php 
                    // Show diagnostic date only if available
                    $diagnostic_date = houzez_get_listing_data('diagnostic_date');
                    if (!empty($diagnostic_date)) : 
                    ?>
                    <div class="diagnostic-info bg-light p-2 rounded mt-3 pt-3 border-top">
                        <p class="small mb-1"><i class="fas fa-file-alt me-1"></i> <?php echo houzez_option('spl_energy_diagnostic_completed', 'Diagnostic completed'); ?></p>
                        <p class="small mb-0"><?php echo esc_html($diagnostic_date); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php else : ?>
<!-- Standard Single Column Layout -->
<?php if (!empty($energy_class)) : ?>
<!-- Energy class indicator -->
<ul class="class-energy d-flex justify-content-between list-unstyled energy-class-<?php echo esc_attr($total_records); ?>" role="list">
    <?php 
    if (!empty($energy_array)) {
        foreach ($energy_array as $energy) {
            $indicator_energy = '';
            $energy = trim($energy);
            
            // Add indicator if this is the current energy class
            if ($energy == $energy_class) {
                $indicator_energy = sprintf(
                    '<div class="indicator-energy" data-energyclass="%1$s">%2$s | %3$s %1$s</div>',
                    esc_attr($energy_class),
                    esc_attr($energy_data['energy_global_index']['value']),
                    esc_attr($energy_class_title)
                );
            }
            
            echo '<li class="class-energy-indicator flex-fill">' . 
                 $indicator_energy . 
                 '<span class="energy-' . esc_attr($energy) . '">' . esc_attr($energy) . '</span>' . 
                 '</li>';
        }
    }
    ?>
</ul>
<?php endif; ?>
<?php endif; ?>