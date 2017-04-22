<?php
if ( !defined( 'ABSPATH' ) ) {
    return;
}

if ( isset( $_GET[ 'edit' ] ) ) {
    $tool = \ClassCube\LTI_Consumer::get_tool( $_GET[ 'edit' ] );
    if ( empty( $tool ) ) {
        echo '<div class="notice error"><p>' . __( 'Cannot find LTI tool', 'cc-lti' ) . '</p></div>';
        return;
    }
    $page_header = sprintf( __( 'Edit %1$s', 'cc-lti' ), $tool[ 'name' ] );
}
else {
    /* Make blank dummy values */
    $tool[ 'name' ] = '';
    $tool[ 'base_url' ] = '';
    $tool[ 'consumer_key' ] = '';
    $tool[ 'shared_secret' ] = '';
    $tool[ 'custom_parameters' ] = '';
    $tool[ 'share_username' ] = self::get_setting( 'share_username' );
    $tool[ 'share_email' ] = self::get_setting( 'share_email' );
    $tool[ 'require_login' ] = self::get_setting( 'require_login' );
    $tool[ 'id' ] = '';

    $page_header = __( 'Add new LTI tool', 'cc-lti' );
}
?>
<div class="wrap">
    <div class="cc-links">
        <?php include(__DIR__ . '/admin-links.php'); ?>
    </div>
    <div class="cc-body">

        <form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="POST">
            <h1><?php echo $page_header; ?></h1>
            <?php
            if ( isset( $_GET[ 'req' ] ) ) {
                echo '<div class="notice error"><p>' . __( 'Tool name and base url are required fields', 'cc-lti' ) . '</p></div>';
            }
            ?>
            <table class="form-table">
                <tr>
                    <th><?php _e( 'Tool Name', 'cc-lti' ); ?></th>
                    <td>
                        <input type="text" class="widefat" name="cc-tool-name" value="<?php echo esc_attr( $tool[ 'name' ] ); ?>">
                    </td>
                </tr>
                <tr>
                    <th><?php _e( 'Base URL', 'cc-lti' ); ?></th>
                    <td>
                        <input type="text" class="widefat" name="cc-base-url" value="<?php echo esc_attr( $tool[ 'base_url' ] ); ?>">
                    </td>
                </tr>
                <tr>
                    <th><?php _e( 'Consumer Key', 'cc-lti' ); ?></th>
                    <td>
                        <input type="text" class="widefat" name="cc-consumer-key" value="<?php echo esc_attr( $tool[ 'consumer_key' ] ); ?>">
                    </td>
                </tr>
                <tr>
                    <th><?php _e( 'Shared Secret', 'cc-lti' ); ?></th>
                    <td>
                        <input type="text" class="widefat" name="cc-shared-secret" value="<?php echo esc_attr( $tool[ 'shared_secret' ] ); ?>">
                    </td>
                </tr>
                <tr>
                    <th><?php _e( 'Custom Parameters', 'cc-lti' ); ?></th>
                    <td>
                        <textarea class="widefat" name="cc-custom-parameters"><?php echo esc_attr( $tool[ 'custom_parameters' ] ); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Require Login', 'cc-lti'); ?></th>
                    <td>
                        <input type="checkbox" name="cc-require-login" <?php checked($tool['require_login'], true, true); ?>>
                    </td>
                </tr>
                <tr>
                    <th><?php _e( 'Share Username', 'cc-lti' ); ?></th>
                    <td>
                        <input type="checkbox" name="cc-share-username" <?php checked( $tool[ 'share_username' ], true, true ); ?>>
                    </td>                                
                </tr>
                <tr>
                    <th><?php _e( 'Share Email Address', 'cc-lti' ); ?></th>
                    <td>
                        <input type="checkbox" name="cc-share-email" <?php checked( $tool[ 'share_email' ], true, true ); ?>>
                    </td>
                </tr>
            </table>
            <input type="submit" class="button button-primary" value="<?php _e( 'Add Tool', 'cc-lti' ); ?>">
            <input type="hidden" name="action" value="add_tool">
            <input type="hidden" name="cc-id" value="<?php echo esc_attr( $tool[ 'id' ] ); ?>">
        </form>
    </div>
</div>