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
                        <h3><?php echo esc_html__('Expired', 'houzez'); ?></h3>
                        <p class="mb-0">
                            <?php
                            echo esc_html__('This property is expired and is no longer available.', 'houzez'); 
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>