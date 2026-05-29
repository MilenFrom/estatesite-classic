<?php 
global $search_style;

if($search_style == 'v1') {
	get_template_part('template-parts/search/search-v1'); 
} elseif($search_style == 'v2') {
	get_template_part('template-parts/search/search-v2'); 
} elseif($search_style == 'v3') {
	get_template_part('template-parts/search/search-v3'); 
} 