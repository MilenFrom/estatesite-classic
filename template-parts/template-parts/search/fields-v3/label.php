<div class="btn-group" role="group">
	<button type="button" class="btn btn-light-grey-outlined" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false" aria-controls="label-dropdown">
		<?php echo houzez_option('srh_label','Label'); ?>
	</button>
	<div id="label-dropdown" class="dropdown-menu dropdown-menu-small dropdown-menu-right advanced-search-dropdown clearfix" role="menu">
		
		<?php
		$get_label = array();
		$get_label = isset ( $_GET['label'] ) ? $_GET['label'] : $get_label;

		if( taxonomy_exists('property_label') ) {
		    $prop_label = get_terms(
		        array(
		            "property_label"
		        ),
		        array(
		            'orderby' => 'name',
		            'order' => 'ASC',
		            'hide_empty' => false,
		            'parent' => 0
		        )
		    );
		
		    $checked_label = '';
		    $count = 0;
		    if (!empty($prop_label)) {
		        foreach ($prop_label as $label):
		       
		            echo '<label class="control control--checkbox">';
		            echo '<input class="'.houzez_get_ajax_search().'" name="label[]" type="checkbox" '.checked( $checked_label, $label->slug, false ).' value="' . esc_attr( $label->slug ) . '">';
		            echo esc_attr( $label->name );
		            echo '<span class="control__indicator"></span>';

		            $get_child = get_terms('property_label', array(
                        'hide_empty' => false,
                        'parent' => $label->term_id
                    ));

                    if (!empty($get_child)) {
                        foreach($get_child as $child) {
                            echo '<label class="control control--checkbox">';
                                echo '<input class="'.houzez_get_ajax_search().'" name="label[]" type="checkbox" '.checked( $checked_label, $child->slug, false ).' value="' . esc_attr( $child->slug ) . '">';
					            echo esc_attr( $child->name );
					            echo '<span class="control__indicator"></span>';
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