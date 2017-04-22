<?php
if ( !defined( 'ABSPATH' ) ) {
    return;
}

$tools = get_option( 'classcube-lti-tools', [ ] );
?>
<div class="wrap">
    <div class="cc-links">
        <?php include(__DIR__ . '/admin-links.php'); ?>
    </div>
    <div class="cc-body">
        <h1>
            <?php
            _e( 'View Tool', 'cc-lti' );
            if ( isset( $tools[ $_GET[ 'view' ] ] ) ) {
                echo ' <a class="page-title-action" href="' . admin_url( 'admin.php?page=cc-lti&edit=' . $_GET[ 'view' ] ) . '">' . __( 'Edit', 'cc-lti' ) . '</a>';
                
            }
            ?>
        </h1>
        <?php
        if ( !isset( $tools[ $_GET[ 'view' ] ] ) ) {
            echo '<div class="notice error"><p>' . __( 'LTI tool not found', 'cc-lti' ) . '</p></div>';
        }
        else {
            $tool = $tools[ $_GET[ 'view' ] ];
            ?>
            <table class="form-table">
                <tr>
                    <th><?php _e( 'Tool Name', 'cc-lti' ); ?></th>
                    <td>
                        <?php echo $tool[ 'name' ]; ?>
                    </td>
                </tr>
                <tr>
                    <th><?php _e( 'Base URL', 'cc-lti' ); ?></th>
                    <td><?php echo $tool[ 'base_url' ]; ?></td>
                </tr>
                <tr>
                    <th><?php _e( 'Consumer Key', 'cc-lti' ); ?></th>
                    <td><?php echo $tool[ 'consumer_key' ]; ?></td>
                </tr>
                <tr>
                    <th><?php _e( 'Shared Secret', 'cc-lti' ); ?></th>
                    <td><?php echo $tool[ 'shared_secret' ]; ?></td>
                </tr>
                <tr>
                    <th><?php _e( 'Custom Parameters', 'cc-lti' ); ?></th>
                    <td><?php echo nl2br( $tool[ 'custom_parameters' ] ); ?></td>
                </tr>
                <tr>
                    <th><?php _e( 'Require Login', 'cc-lti' ); ?></th>
                    <td><?php echo $tool[ 'require_login' ] ? 'Yes' : 'No'; ?></td>
                </tr>
                <tr>
                    <th><?php _e( 'Share Username', 'cc-lti' ); ?></th>
                    <td><?php echo $tool[ 'share_username' ] ? 'Yes' : 'No'; ?></td>
                </tr>
                <tr>
                    <th><?php _e( 'Share Email', 'cc-lti' ); ?></th>
                    <td><?php echo $tool[ 'share_email' ] ? 'Yes' : 'No'; ?></td>
                </tr>
            </table>
            <?php
        }
        ?>
    </div>
</div>