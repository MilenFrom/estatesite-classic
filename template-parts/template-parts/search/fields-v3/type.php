<div class="btn-group" role="group">
	<button type="button" class="btn btn-light-grey-outlined" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false" aria-controls="type-dropdown">
		<?php echo houzez_option('srh_type', 'Type'); ?>
	</button>
	<div id="type-dropdown" class="dropdown-menu dropdown-menu-small dropdown-menu-right advanced-search-dropdown clearfix" role="menu">
		
		<?php
		if( taxonomy_exists('property_type') ) {
		    $prop_type = get_terms(
		        array(
		            "property_type"
		        ),
		        array(
		            'orderby' => 'name',
		            'order' => 'ASC',
		            'hide_empty' => false,
		            'parent' => 0
		        )
		    );
		
		    $checked_type = '';
		    
		    if ( !empty($prop_type) && ! is_wp_error( $prop_type ) ) {

		    	$searched_type_slugs = array();
		        if (isset($_GET['type'])) {
		            $searched_type_slugs = $_GET['type'];
		        }

		        foreach ($prop_type as $type):

		        	$checked_type = '';

		            if (in_array($type->slug, $searched_type_slugs)) {
		                $checked_type = 'checked';
		            }
		       
		            echo '<label class="control control--checkbox">';
		            echo '<input class="'.houzez_get_ajax_search().'" name="type[]" type="checkbox" '.$checked_type.' value="' . esc_attr( $type->slug ) . '">';
		            echo '<span class="control__indicator"></span>';
					echo '<span class="control__label">'.esc_attr( $type->name ).'</span>';

		            $get_child = get_terms('property_type', array(
                        'hide_empty' => false,
                        'parent' => $type->term_id
                    ));

                    if (!empty($get_child)) {
                    	$count = 0;
                        foreach($get_child as $child) {

                        	$checked_type2 = '';
                        	if (in_array($child->slug, $searched_type_slugs)) {
				                $checked_type2 = 'checked';
				            }

                            echo '<label class="control control--checkbox hz-checkbox-'.$count.'">';
                                echo '<input class="'.houzez_get_ajax_search().'" name="type[]" type="checkbox" '.$checked_type2.' value="' . esc_attr( $child->slug ) . '">';
					            echo '<span class="control__indicator"></span>';
								echo '<span class="control__label">'.esc_attr( $child->name ).'</span>';
                            echo '</label>';
                            $count++;
                        }
                    }

		            echo '</label>';
		        endforeach;
		    }
		} ?>

		<div class="d-flex gap-2 mt-2 justify-content-start">
			<button class="btn btn-apply btn-primary" type="button"><?php echo houzez_option('srh_apply', 'Apply'); ?></button>
			<button class="btn btn-clear clear-checkboxes btn-primary-outlined" type="button"><?php echo houzez_option('srh_clear', 'Clear'); ?></button>
		</div>
	</div><!-- advanced-search-dropdown -->
</div><!-- btn-group -->