<div class="btn-group" role="group">
	<button type="button" class="btn btn-light-grey-outlined" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false" aria-controls="status-dropdown">
		<?php echo houzez_option('srh_status', 'Status'); ?>
	</button>
	<div id="status-dropdown" class="dropdown-menu dropdown-menu-small dropdown-menu-right advanced-search-dropdown clearfix" role="menu">
		
		<?php
		if( taxonomy_exists('property_status') ) {

		    $prop_status = get_terms(
		        array(
		            "property_status"
		        ),
		        array(
		            'orderby' => 'name',
		            'order' => 'ASC',
		            'hide_empty' => false,
		            'parent' => 0,
		            'exclude' => houzez_option('search_exclude_status')
		        )
		    );
		
		    $checked_status = '';
		    $count = 0;
		    if ( !empty($prop_status) && ! is_wp_error($prop_status) ) {

		    	$searched_status_slugs = array();
		        if (isset($_GET['status'])) {
		            $searched_status_slugs = $_GET['status'];
		        }

		        foreach ($prop_status as $status):

		        	$checked_status = '';

		            if (in_array($status->slug, $searched_status_slugs)) {
		                $checked_status = 'checked';
		            }
		       
		            echo '<label class="control control--checkbox">';
		            echo '<input class="'.houzez_get_ajax_search().' status-js" name="status[]" type="checkbox" '.$checked_status.' value="' . esc_attr( $status->slug ) . '">';
		            echo '<span class="control__indicator"></span>';
					echo '<span class="control__label">'.esc_attr( $status->name ).'</span>';

		            $get_child = get_terms('property_status', array(
                        'hide_empty' => false,
                        'parent' => $status->term_id
                    ));

                    if (!empty($get_child)) {
                        foreach($get_child as $child) {

                        	$checked_status2 = '';
                        	if (in_array($child->slug, $searched_status_slugs)) {
				                $checked_status2 = 'checked';
				            }
				            
                            echo '<label class="control control--checkbox">';
                                echo '<input class="'.houzez_get_ajax_search().' status-js" name="status[]" type="checkbox" '.$checked_status2.' value="' . esc_attr( $child->slug ) . '">';
					            echo '<span class="control__indicator"></span>';
								echo '<span class="control__label">'.esc_attr( $child->name ).'</span>';
                            echo '</label>';

                        }
                    }

		            echo '</label>';
		            $count++;
		        endforeach;
		    }
		} ?>

		<div class="d-flex gap-2 mt-2 justify-content-start">
			<button class="btn btn-apply btn-primary" type="button"><?php echo houzez_option('srh_apply', 'Apply'); ?></button>
			<button class="btn btn-clear clear-checkboxes btn-primary-outlined" type="button"><?php echo houzez_option('srh_clear', 'Clear'); ?></button>
		</div>
	</div><!-- advanced-search-dropdown -->
</div><!-- btn-group -->