<?php
/**
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 08/12/16
 * Time: 8:11 PM
 */

global $wpdb, $userID;
$tabel = $wpdb->prefix . 'houzez_threads';
$message_query = "SELECT * FROM $tabel WHERE sender_id = $userID OR receiver_id = $userID ORDER BY seen ASC";


$houzez_threads = $wpdb->get_results( $message_query );
?>

<div class="heading d-flex align-items-center justify-content-between mb-4">
	<div class="heading-text">
		<h2><?php echo houzez_option('dsh_messages', 'Messages'); ?></h2> 
	</div> 
</div> 
<div class="houzez-data-content">
    <div class="houzez-data-table">
        <div class="table-responsive">
            <?php if ( sizeof( $houzez_threads ) != 0 ) { ?>
            <table class="table table-hover align-middle m-0">
                <thead>
                    <tr>
                        <th data-label="From"><?php esc_html_e( 'From', 'houzez' ); ?></th>
                        <th data-label="Property"><?php esc_html_e( 'Property', 'houzez' ); ?></th>
                        <th data-label="Last Message"><?php esc_html_e( 'Last Message', 'houzez' ); ?></th>
                        <th data-label="Date"><?php esc_html_e( 'Date', 'houzez' ); ?></th>
                        <th data-label="Reply" class="text-center"><?php esc_html_e( 'Reply', 'houzez' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    
                <?php 
                foreach ( $houzez_threads as $thread ) { 

                    $sender_id = $thread->sender_id;
                    $receiver_id = $thread->receiver_id;

                    if($userID == $sender_id) {
                        $delete = $thread->sender_delete;
                    } elseif($userID == $receiver_id) {
                        $delete = $thread->receiver_delete;
                    }

                if($delete != 1) {

                $thread_class = 'msg-unread table-new';
                $tabel = $wpdb->prefix . 'houzez_thread_messages';
                $sender_id = $thread->sender_id;
                $thread_id = $thread->id;

                $last_message = $wpdb->get_row(
                    "SELECT *
                        FROM $tabel
                        WHERE thread_id = $thread_id
                        ORDER BY id DESC"
                );

                $user_custom_picture =  get_the_author_meta( 'fave_author_custom_picture' , $sender_id );
                $url_query = array( 'thread_id' => $thread_id, 'seen' => true );

                if ( $last_message->created_by == $userID || $thread->seen ) {
                    $thread_class = '';
                    unset( $url_query['seen'] );
                }

                if ( empty( $user_custom_picture )) {
                    $user_custom_picture = get_template_directory_uri().'/img/profile-avatar.png';
                }

                $thread_link = houzez_get_template_link_2('template/user_dashboard_messages.php');
                $thread_link = add_query_arg( $url_query, $thread_link );

                $sender_first_name  =  get_the_author_meta( 'first_name', $sender_id );
                $sender_last_name  =  get_the_author_meta( 'last_name', $sender_id );
                $sender_display_name = get_the_author_meta( 'display_name', $sender_id );
                if( !empty($sender_first_name) && !empty($sender_last_name) ) {
                    $sender_display_name = $sender_first_name.' '.$sender_last_name;
                }

                $last_sender_first_name  =  get_the_author_meta( 'first_name', $last_message->created_by );
                $last_sender_last_name  =  get_the_author_meta( 'last_name', $last_message->created_by );
                $last_sender_display_name = get_the_author_meta( 'display_name', $last_message->created_by );
                if( !empty($last_sender_first_name) && !empty($last_sender_last_name) ) {
                    $last_sender_display_name = $last_sender_first_name.' '.$last_sender_last_name;
                }

                ?>
                <tr class="<?php echo $thread_class; ?>">
                    <td class="text-nowrap" data-label="<?php esc_html_e( 'From', 'houzez' ); ?>">
                        <div class="houzez-customer d-flex align-items-center gap-4">
                            <div class="image-holder">
                                <a href="#">
                                    <img src="<?php echo esc_url( $user_custom_picture ); ?>" alt="" class="img-fluid"/>
                                </a>
                            </div>
                            <div class="text-box">
                                <a href="#"><?php echo ucfirst( $sender_display_name ); ?></a>
                            </div>
                        </div>
                    </td>
                    <td class="text-nowrap" data-label="<?php esc_html_e( 'Property', 'houzez' ); ?>">
                        <?php echo get_the_title( $thread->property_id ); ?>
                    </td>
                    <td data-label="<?php esc_html_e( 'Last Message', 'houzez' ); ?>"><?php echo ucfirst( $last_sender_display_name ).': '; ?><?php echo $last_message->message; ?></td>
                    
                    <td class="text-nowrap" data-label="<?php esc_html_e( 'Date', 'houzez' ); ?>">
                        <?php echo date_i18n( get_option('date_format').' '.get_option('time_format'), strtotime( $last_message->time ) ); ?>
                    </td>
                    <td class="text-lg-center text-start" data-label="<?php esc_html_e( 'Reply', 'houzez' ); ?>">
                        <div class="dropdown" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?php esc_html_e( 'Actions', 'houzez' ); ?>">
                            <a href="javascript:void(0)" class="action-btn" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="houzez-icon icon-navigation-menu-horizontal"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu3">
                                <li><a class="dropdown-item active" href="<?php echo esc_url( $thread_link ).'#message-'.intval($last_message->id); ?>"><i class="houzez-icon icon-share-2"></i> <?php esc_attr_e('Reply', 'houzez');?></a></li>
                                <li><a class="dropdown-item houzez_delete_msg_thread" href="javascript:void(0)" data-thread-id="<?php echo intval($thread_id); ?>" data-sender-id="<?php echo intval($sender_id); ?>" data-receiver-id="<?php echo intval($receiver_id); ?>"><i class="houzez-icon icon-remove-circle"></i> <?php esc_attr_e('Delete', 'houzez'); ?></a></li>
                            </ul> 
                        </div>
                    </td>
                </tr>

                <?php } }?>
                </tbody>
            </table>
            <?php } else { ?> 
				<div class="stats-box">
                    <?php echo esc_html__("You don't have any message.", 'houzez'); ?>
                </div>
            <?php } ?>
        </div>
    </div>

    <?php //include 'messages/pagination.php'; ?>
</div>	