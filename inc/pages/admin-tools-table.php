<?php
if ( !defined( 'ABSPATH' ) ) {
    return;
}

require_once(dirname( __DIR__ ) . '/class-lti-table.php');
$toolTable = new \ClassCube\LTI_Table();
$toolTable->prepare_items();
?>
<div class="wrap">
    <h1>
        <?php _e( 'LTI Tools', 'cc-lti' ); ?>                    
        <a href="<?php echo admin_url( 'admin.php?page=cc-lti&add' ); ?>" class="page-title-action">
            <?php _e( 'Add new tool', 'cc-lti' ); ?>
        </a>
    </h1>
    <?php $toolTable->display(); ?>
</div>