<?php
defined('ABSPATH') || die();
/** @var $this NextendSocialProvider */

$settings = $this->settings;
?>

<div class="nsl-admin-sub-content">
	<?php
    $this->renderSettingsHeader();
    ?>

    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" novalidate="novalidate">

		<?php wp_nonce_field('nextend-social-login'); ?>
        <input type="hidden" name="action" value="nextend-social-login"/>
        <input type="hidden" name="view" value="provider-<?php echo $this->getId(); ?>"/>
        <input type="hidden" name="subview" value="settings"/>
        <input type="hidden" name="settings_saved" value="1"/>
        <input type="hidden" name="tested" id="tested" value="<?php echo esc_attr($settings->get('tested')); ?>"/>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row"><label
                            for="consumer_key"><?php _e('Consumer Key', 'nextend-facebook-connect'); ?></label>
                            - <em>(<?php _e('Required', 'nextend-facebook-connect'); ?>)</em></label></th>
                <td>
                    <input name="consumer_key" type="text" id="consumer_key"
                           value="<?php echo esc_attr($settings->get('consumer_key')); ?>" class="regular-text">
                    <p class="description"
                       id="tagline-consumer_key"><?php printf(__('If you are not sure what is your %s, please head over to <a href="%s">Getting Started</a>', 'nextend-facebook-connect'), 'Consumer Key', $this->getAdminUrl()); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label
                            for="consumer_secret"><?php _e('Consumer Secret', 'nextend-facebook-connect'); ?></label>
                </th>
                <td><input name="consumer_secret" type="text" id="consumer_secret"
                           value="<?php echo esc_attr($settings->get('consumer_secret')); ?>" class="regular-text"
                           style="width:40em;">
                </td>
            </tr>
            </tbody>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
                                 value="<?php _e('Save Changes'); ?>"></p>


        <hr/>
        <h2><?php _e('Other settings', 'nextend-facebook-connect'); ?></h2>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row"><label
                            for="user_prefix"><?php _e('Username prefix on register', 'nextend-facebook-connect'); ?></label></th>
                <td><input name="user_prefix" type="text" id="user_prefix"
                           value="<?php echo esc_attr($settings->get('user_prefix')); ?>" class="regular-text"></td>
            </tr>
            </tbody>
        </table>

        <?php
        $this->renderProSettings();
        ?>
    </form>
</div>
