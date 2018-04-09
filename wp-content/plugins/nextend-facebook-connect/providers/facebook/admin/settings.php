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
			<?php if (!defined('NEXTEND_FB_APP_ID')): ?>
                <tr>
                    <th scope="row"><label for="appid"><?php _e('App ID', 'nextend-facebook-connect'); ?>
                            - <em>(<?php _e('Required', 'nextend-facebook-connect'); ?>)</em></label></th>
                    <td>
                        <input name="appid" type="text" id="appid"
                               value="<?php echo esc_attr($settings->get('appid')); ?>" class="regular-text">
                        <p class="description"
                           id="tagline-appid"><?php printf(__('If you are not sure what is your %s, please head over to <a href="%s">Getting Started</a>', 'nextend-facebook-connect'), 'App ID', $this->getAdminUrl()); ?></p>
                    </td>
                </tr>
            <?php endif; ?>
            <?php if (!defined('NEXTEND_FB_APP_SECRET')): ?>
                <tr>
                    <th scope="row"><label for="secret"><?php _e('App Secret', 'nextend-facebook-connect'); ?>
                            - <em>(<?php _e('Required', 'nextend-facebook-connect'); ?>)</em></label>
                    </th>
                    <td><input name="secret" type="text" id="secret"
                               value="<?php echo esc_attr($settings->get('secret')); ?>" class="regular-text"></td>
                </tr>
            <?php endif; ?>
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