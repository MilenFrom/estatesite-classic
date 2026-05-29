<?php
$lead = $first_name = $last_name = '';
if(isset($_GET['lead-id'])) {

	$lead_id = isset($_GET['lead-id']) ? intval($_GET['lead-id']) : 0;
	
	$lead = Houzez_Leads::get_lead($lead_id);

	$prefix = $lead->prefix;
	$display_name = $lead->display_name;
	$first_name = $lead->first_name;
	$last_name = $lead->last_name;
	$email = $lead->email;
	$type = $lead->type;
	$mobile = $lead->mobile;
	$work_phone = $lead->work_phone;
	$home_phone = $lead->home_phone;
	$address = $lead->address;
	$country = $lead->country;
	$state = $lead->state;
	$city = $lead->city;
	$zipcode = $lead->zipcode;
	$enquiry_to = $lead->enquiry_to;
	$source = $lead->source;
	$source_link = $lead->source_link;
	$message = $lead->message;
	$enquiry_user_type = $lead->enquiry_user_type;
	$facebook_url = $lead->facebook_url;
	$twitter_url = $lead->twitter_url;
	$linkedin_url = $lead->linkedin_url;
	$private_note = $lead->private_note;

	$agent_info = houzezcrm_get_assigned_agent( $enquiry_to, $enquiry_user_type );
	
} 

if( !empty($lead) ) :
?>
<div class="inquiry-detail">
  <div class="inquiry-detail-inner">
    <h5><?php if($prefix) { echo $prefix.'. '; } echo esc_attr($display_name); ?></h5>
    <a class="edit-lead" data-id="<?php echo intval($lead->lead_id)?>" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasLead" data-bs-backdrop="true" aria-controls="offcanvasLead"><i class="houzez-icon icon-pencil-circle me-1"></i> <?php esc_html_e('Edit', 'houzez'); ?></a>
  </div>
  
  <ul class="list-unstyled d-flex flex-column gap-2 mb-4">
    <?php if(!empty($first_name)) { ?>
    <li class="border-bottom pb-2">
        <strong><?php esc_html_e('First Name', 'houzez'); ?></strong><br>
        <?php echo esc_attr($first_name); ?>
    </li>
    <?php } ?>

    <?php if(!empty($last_name)) { ?>
    <li class="border-bottom pb-2">
        <strong><?php esc_html_e('Last Name', 'houzez'); ?></strong><br>
        <?php echo esc_attr($last_name); ?>
    </li>
    <?php } ?>

    <li class="border-bottom pb-2">
        <strong><?php esc_html_e('Email', 'houzez'); ?></strong><br>
        <a href="mailto:<?php echo esc_attr($email); ?>"><strong><?php echo esc_attr($email); ?></strong></a>
    </li>

    <?php if($mobile) { ?>
    <li class="border-bottom pb-2">
        <strong><?php esc_html_e('Mobile', 'houzez'); ?></strong><br>
        <?php echo esc_attr($mobile); ?>
    </li>
    <?php } ?>

    <?php if($type) { ?>
    <li class="border-bottom pb-2">
        <strong><?php esc_html_e('Type', 'houzez'); ?></strong><br>
        <?php 
        $type = stripslashes($type);
        $type = htmlentities($type);
        echo esc_attr($type); ?>
    </li>
    <?php } ?>

    <?php if($message) { ?>
    <li class="border-bottom pb-2">
        <strong><?php esc_html_e('Message', 'houzez'); ?></strong><br>
        <?php echo ($message); ?>
    </li>
    <?php } ?>
  </ul>
  
  <h5 class="mb-3 pt-4"><?php esc_html_e('Details', 'houzez'); ?></h5>
  <ul class="list-unstyled d-flex flex-column gap-3 mb-4">
    <?php if($address) { ?>
    <li class="border-bottom pb-2">
        <strong><?php esc_html_e('Address', 'houzez'); ?></strong><br>
        <?php echo esc_attr($address); ?>
    </li>
    <?php } ?>

    <?php if($mobile) { ?>
    <li class="border-bottom pb-2">
        <strong><?php esc_html_e('Mobile', 'houzez'); ?></strong><br>
        <?php echo esc_attr($mobile); ?>
    </li>
    <?php } ?>

    <?php if($home_phone) { ?>
    <li class="border-bottom pb-2">
        <strong><?php esc_html_e('Home', 'houzez'); ?></strong><br>
        <?php echo esc_attr($home_phone); ?>
    </li>
    <?php } else { ?>
    <li class="border-bottom pb-2">
        <strong><?php esc_html_e('Home', 'houzez'); ?></strong><br>
        -
    </li>
    <?php } ?>

    <?php if($work_phone) { ?>
    <li class="border-bottom pb-2">
        <strong><?php esc_html_e('Work', 'houzez'); ?></strong><br>
        <?php echo esc_attr($work_phone); ?>
    </li>
    <?php } else { ?>
    <li class="border-bottom pb-2">
        <strong><?php esc_html_e('Work', 'houzez'); ?></strong><br>
        -
    </li>
    <?php } ?>

    <?php if($country || $city || $state) { ?>
    <li class="border-bottom pb-2">
        <strong><?php esc_html_e('Location', 'houzez'); ?></strong><br>
        <?php 
            echo esc_attr($country);
            if($state) {
                echo ', '.$state;
            } 
            if($city) {
                echo ', '.$city;
            } 
            if($zipcode) {
                echo ', '.$zipcode;
            } 
        ?>
    </li>
    <?php } ?>

    <li class="border-bottom pb-2">
        <strong><?php esc_html_e('Source', 'houzez'); ?></strong><br>
        <?php 
        if( !empty($source) || !empty($source_link)) {
            if( !empty($source)) {
                echo esc_attr($source).'<br/>';
            }

            if(!empty($source_link)) {
                echo '<a target="_blank" href="'.esc_url($source_link).'"><strong>'.esc_url($source_link).'</strong></a>';
            }
        } 
        ?>
    </li>
    
    <?php if(!empty($private_note)) { ?>
    <li class="border-bottom pb-2">
        <strong><?php esc_html_e('Private Note', 'houzez'); ?></strong><br>
        <?php echo ($private_note); ?>
    </li>
    <?php } ?>
  </ul>

  <?php if( $facebook_url != '' || $twitter_url != '' || $linkedin_url != '' ) { ?>
  <h5 class="mb-3 pt-4"><?php esc_html_e('Social', 'houzez'); ?></h5>
  <ul class="list-unstyled d-flex flex-column gap-2 mb-4">
    <?php if( $facebook_url != '' ) { ?>
    <li class="border-bottom pb-2">
        <a href="<?php echo esc_url($facebook_url); ?>"><strong><?php esc_html_e('Facebook', 'houzez'); ?></strong></a>
    </li>
    <?php } 
    if( $twitter_url != '' ) { ?>
    <li class="border-bottom pb-2">
        <a href="<?php echo esc_url($twitter_url); ?>"><strong><?php esc_html_e('Twitter', 'houzez'); ?></strong></a>
    </li>
    <?php } 
    if( $linkedin_url != '' ) { ?>
    <li class="border-bottom pb-2">
        <a href="<?php echo esc_url($linkedin_url); ?>"><strong><?php esc_html_e('Linkedin', 'houzez'); ?></strong></a>
    </li>
    <?php } ?>
  </ul>
  <?php } ?>

  <h5 class="mb-3 pt-4"><?php esc_html_e('Realtor', 'houzez'); ?></h5>
  <ul class="list-unstyled d-flex flex-column gap-3 mb-4">
    <li class="border-bottom pb-2">
        <strong><?php esc_html_e('Name', 'houzez'); ?></strong><br>
        <?php 
        if(!empty($agent_info['name'])) {
            echo esc_attr($agent_info['name']);
        } else {
            echo '-';
        }
        ?>
    </li>
    <li class="border-bottom pb-2">
        <strong><?php esc_html_e('Email', 'houzez'); ?></strong><br>
        <?php 
        if(!empty($agent_info['email'])) {
            echo '<a href="mailto:'.esc_attr($agent_info['email']).'"><strong>'.esc_attr($agent_info['email']).'</strong></a>';
        } else {
            echo '-';
        }
        ?>
    </li>
  </ul>
</div><!-- inquiry-detail -->
<?php endif; ?>