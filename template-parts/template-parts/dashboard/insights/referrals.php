<?php
global $insights_stats, $houzez_local;

$referrers = $insights_stats['others']['referrers'];

?>

<div class="block-wrap">
	<div class="card-body">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h5 class="card-title"><?php esc_html_e('Referrals', 'houzez'); ?></h5>
		</div>
		
		<?php 
		$i = 0;
		$output = '';
		if(!empty($referrers)) { 
			$output .= '<div class="accordion" id="referralAccordion">';
			
			foreach ($referrers as $ref) {
				
				$i++;
				$domain = $ref['domain'];
				$visit_counts = $ref['count'];
				$subrefs = $ref['subrefs'];

				$view_text = $houzez_local['view_label'];
				if($visit_counts > 1) {
					$view_text = $houzez_local['views_label'];
				}

				$output .= '<div class="accordion-item">';
				$output .= '<h2 class="accordion-header">';
				$output .= '<button class="accordion-button d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#refCollapse'.$i.'">';
				$output .= '<div class="d-flex align-items-center gap-2">';
				$output .= '<span class="fw-medium">'.esc_attr($domain).'</span>';
				$output .= '<span class="text-muted small">('.$visit_counts.' '.$view_text.')</span>';
				$output .= '</div>';
				$output .= '</button>';
				$output .= '</h2>';
				$output .= '<div id="refCollapse'.$i.'" class="accordion-collapse collapse" data-bs-parent="#referralAccordion">';
				$output .= '<div class="accordion-body bg-light">';
				$output .= '<div class="d-flex flex-column gap-3">';

				if(!empty($subrefs)) {
					foreach ($subrefs as $sub) {
						$url = $sub['url'];
						$counts = $sub['count'];

						$view_text = $houzez_local['view_label'];
						if($counts > 1) {
							$view_text = $houzez_local['views_label'];
						}

						$output .= '<div class="d-flex justify-content-between align-items-center">';
						$output .= '<span class="text-muted text-truncate flex-grow-1">'.esc_attr($url).'</span>';
						$output .= '<span class="text-muted small ms-2">('.$counts.' '.$view_text.')</span>';
						$output .= '</div>';
					}
				}

				$output .= '</div>'; // end flex-column
				$output .= '</div>'; // end accordion-body
				$output .= '</div>'; // end accordion-collapse
				$output .= '</div>'; // end accordion-item
			}
			
			$output .= '</div>'; // end accordion
		} 
		echo $output;
		?>
		
	</div>
</div>
