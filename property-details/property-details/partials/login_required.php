<?php
/**
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 08/01/16
 * Time: 4:23 PM
 */
$allowed_html_array = array(
    'i' => array(
        'class' => array()
    ),
    'span' => array(
        'class' => array()
    ),
    'a' => array(
        'href' => array(),
        'title' => array(),
        'target' => array(),
        'data-bs-toggle' => array(),
        'data-bs-target' => array(),
        'class' => array(),
    )
);
?>
<div class="container">
    <div class="row">
        <div class="col-12" role="region">                      
            <div class="property-view">
                <div class="login-required-block text-center mb-4 pb-4">
                    <div class="login-required-content block-wrap">
                        <h3><?php echo esc_html__( 'Login Required', 'houzez' ); ?></h3>
                        <p><?php echo esc_html__( 'Please login to view this property details', 'houzez' ); ?></p>
                        <div class="d-flex justify-content-center gap-2">
                            <?php
                            if( houzez_option('header_login') ) { 
                                echo '<a href="#" class="btn btn-primary hhh_login" data-bs-toggle="modal" data-bs-target="#login-register-form">'.esc_html__( 'Login', 'houzez' ).'</a>';
                            }

                            if( houzez_option('header_register') ) { 
                                echo '<a href="#" class="btn btn-primary-outlined hhh_register" data-bs-toggle="modal" data-bs-target="#login-register-form">'.esc_html__( 'Register', 'houzez' ).'</a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>