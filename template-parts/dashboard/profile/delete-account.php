<?php if( ! isset( $_GET['edit_user'] ) ) { ?>
<div class="block-wrap">
    <div class="block-title-wrap">
        <h2><?php esc_html_e( 'Delete Account', 'houzez' ); ?></h2>
    </div>
    <div class="block-content-wrap">
        <form>
            <div class="row">
                <div class="col-12">
                    <a id="houzez_delete_account" class="btn btn-danger"><?php esc_html_e( 'Delete Account', 'houzez' ); ?></a>
                </div>
            </div>
        </form>
    </div>
</div>
<?php } ?>