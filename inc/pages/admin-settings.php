<?php
if ( !defined( 'ABSPATH' ) ) {
    return;
}

$updated = false;
if ( isset( $_POST[ 'cc-submitted' ] ) ) {
    self::update_settings();
    $updated = true;
}
?>
<div class="wrap">
    <div class="cc-links">
        <?php include(__DIR__.'/admin-links.php'); ?>
    </div>
    <div class="cc-body">
        <h1>
            <?php _e( 'LTI Consumer Settings', 'cc-lti' ); ?>                    
        </h1>
        <?php
        if ( $updated ) {
            ?>
            <div class="updated notice">
                <p><?php _e( 'Settings have been updated', 'cc-lti' ); ?></p>
            </div>
            <?php
        }
        ?>
        <form method="POST">
            <h3><?php _e( 'Privacy Defaults', 'cc-lti' ); ?></h3>
            <table class="form-table">
                <tr>
                    <th><?php _e( 'Share Email', 'cc-lti' ); ?></th>
                    <td><input type="checkbox" name="cc-share-email" <?php checked( self::get_setting( 'share_email' ), true, true ); ?>></td>
                </tr>
                <tr>
                    <th><?php _e( 'Share Username', 'cc-lti' ); ?></th>
                    <td>
                        <input type="checkbox" name="cc-share-username" <?php checked( self::get_setting( 'share_username' ), true, true ); ?>>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Require Login', 'cc-lti'); ?></th>
                    <td>
                        <input type="checkbox" name="cc-require-login" <?php checked(self::get_setting('require_login'), true, true); ?>>
                    </td>
                </tr>
            </table>

            <h3><?php _e( 'Frame Settings', 'cc-lti' ); ?></h3>
            <table class="form-table">
                <tr>
                    <th><?php _e( 'CSS Class', 'cc-lti' ); ?></th>
                    <td>
                        <input type="text" class="widefat" name="cc-css-class" value="<?php echo esc_attr( self::get_setting( 'css_class' ) ); ?>">
                    </td>
                </tr>
                <tr>
                    <th><?php _e( 'CSS Style', 'cc-lti' ); ?></th>
                    <td>
                        <input type="text" class="widefat" name="cc-css-style" value="<?php echo esc_attr( self::get_setting( 'css_style' ) ); ?>">
                    </td>
                </tr>
                <tr>
                    <th><?php _e( 'Allow Fullscreen', 'cc-lti' ); ?></th>
                    <td>
                        <input type="checkbox" name="cc-allow-fullscreen" <?php checked( self::get_setting( 'allow_fullscreen', true, true ) ); ?>>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="cc-submitted" value="1">
            <input type="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'cc-lti' ); ?>">
        </form>
    </div>
    <br style="clear:both;">
</div>
