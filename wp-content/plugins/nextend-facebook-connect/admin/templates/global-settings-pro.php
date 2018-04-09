<?php
defined('ABSPATH') || die();

$isPRO = apply_filters('nsl-pro', false);

$attr = '';
if (!$isPRO) {
    $attr = ' disabled ';
}

$settings = NextendSocialLogin::$settings;
?>
<hr/>
<h1><?php _e('PRO settings', 'nextend-facebook-connect'); ?></h1>


<?php
NextendSocialLoginAdmin::showProBox();
?>
<table class="form-table" <?php if (0 && !$isPRO): ?> style="opacity:0.5;"<?php endif; ?>>
    <tbody>
    <tr>
        <th scope="row"><?php _e('Login form button style', 'nextend-facebook-connect'); ?></th>
        <td>
            <fieldset>
                <legend class="screen-reader-text">
                    <span><?php _e('Login form button style', 'nextend-facebook-connect'); ?></span></legend>
                <label>
                    <input type="radio" name="login_form_button_style"
                           value="default" <?php if ($settings->get('login_form_button_style') == 'default') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('Default', 'nextend-facebook-connect'); ?></span><br/>
                    <img src="<?php echo plugins_url('images/buttons/default.png', NSL_ADMIN_PATH) ?>"/>
                </label>
                <label>
                    <input type="radio" name="login_form_button_style"
                           value="icon" <?php if ($settings->get('login_form_button_style') == 'icon') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('Icon', 'nextend-facebook-connect'); ?></span><br/>
                    <img src="<?php echo plugins_url('images/buttons/icon.png', NSL_ADMIN_PATH) ?>"/>
                </label><br>
            </fieldset>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php _e('Login layout', 'nextend-facebook-connect'); ?></th>
        <td>
            <fieldset>
                <legend class="screen-reader-text">
                    <span><?php _e('Login layout', 'nextend-facebook-connect'); ?></span></legend>
                <label>
                    <input type="radio" name="login_form_layout"
                           value="below" <?php if ($settings->get('login_form_layout') == 'below') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('Below', 'nextend-facebook-connect'); ?></span><br/>
                    <img src="<?php echo plugins_url('images/layouts/below.png', NSL_ADMIN_PATH) ?>"/>
                </label>
                <label>
                    <input type="radio" name="login_form_layout"
                           value="below-separator" <?php if ($settings->get('login_form_layout') == 'below-separator') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('Below with separator', 'nextend-facebook-connect'); ?></span><br/>
                    <img src="<?php echo plugins_url('images/layouts/below-separator.png', NSL_ADMIN_PATH) ?>"/>
                </label>
                <label>
                    <input type="radio" name="login_form_layout"
                           value="below-floating" <?php if ($settings->get('login_form_layout') == 'below-floating') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('Below and floating', 'nextend-facebook-connect'); ?></span><br/>
                    <img src="<?php echo plugins_url('images/layouts/below-floating.png', NSL_ADMIN_PATH) ?>"/>
                </label>
                <label>
                    <input type="radio" name="login_form_layout"
                           value="above" <?php if ($settings->get('login_form_layout') == 'above') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('Above', 'nextend-facebook-connect'); ?></span><br/>
                    <img src="<?php echo plugins_url('images/layouts/above.png', NSL_ADMIN_PATH) ?>"/>
                </label>
                <label>
                    <input type="radio" name="login_form_layout"
                           value="above-separator" <?php if ($settings->get('login_form_layout') == 'above-separator') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('Above with separator', 'nextend-facebook-connect'); ?></span><br/>
                    <img src="<?php echo plugins_url('images/layouts/above-separator.png', NSL_ADMIN_PATH) ?>"/>
                </label><br>
            </fieldset>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php _e('Comment login button', 'nextend-facebook-connect'); ?></th>
        <td>
            <fieldset>
                <legend class="screen-reader-text">
                    <span><?php _e('Comment login button', 'nextend-facebook-connect'); ?></span></legend>
                <label><input type="radio" name="comment_login_button"
                              value="show" <?php if ($settings->get('comment_login_button') == 'show') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('Show', 'nextend-facebook-connect'); ?></span></label><br>
                <label><input type="radio" name="comment_login_button"
                              value="hide" <?php if ($settings->get('comment_login_button') == 'hide') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('Hide', 'nextend-facebook-connect'); ?></span></label><br>
            </fieldset>
            <p class="description"><?php printf(__('You need to turn on the \' %1$s > %2$s > %3$s \' for this feature to work', 'nextend-facebook-connect'), __('Settings'), __('Discussion'), __('Users must be registered and logged in to comment')); ?></p>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php _e('Comment button style', 'nextend-facebook-connect'); ?></th>
        <td>
            <fieldset>
                <legend class="screen-reader-text">
                    <span><?php _e('Comment button style', 'nextend-facebook-connect'); ?></span></legend>
                <label>
                    <input type="radio" name="comment_button_style"
                           value="default" <?php if ($settings->get('comment_button_style') == 'default') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('Default', 'nextend-facebook-connect'); ?></span><br/>
                    <img src="<?php echo plugins_url('images/buttons/default.png', NSL_ADMIN_PATH) ?>"/>
                </label>
                <label>
                    <input type="radio" name="comment_button_style"
                           value="icon" <?php if ($settings->get('comment_button_style') == 'icon') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('Icon', 'nextend-facebook-connect'); ?></span><br/>
                    <img src="<?php echo plugins_url('images/buttons/icon.png', NSL_ADMIN_PATH) ?>"/>
                </label><br>
            </fieldset>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php _e('WooCommerce login form', 'nextend-facebook-connect'); ?></th>
        <td>
            <fieldset>
                <legend class="screen-reader-text">
                    <span><?php _e('WooCommerce login form', 'nextend-facebook-connect'); ?></span></legend>
                <label><input type="radio" name="woocommerce_login"
                              value="" <?php if ($settings->get('woocommerce_login') == '') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('No Connect button in login form', 'nextend-facebook-connect'); ?></span></label><br>
                <label><input type="radio" name="woocommerce_login"
                              value="before" <?php if ($settings->get('woocommerce_login') == 'before') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('Connect button before login form', 'nextend-facebook-connect'); ?></span>
                    <code><?php _e('Action:'); ?>
                        woocommerce_login_form_start</code></label><br>
                <label><input type="radio" name="woocommerce_login"
                              value="after" <?php if ($settings->get('woocommerce_login') == 'after') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('Connect button after login form', 'nextend-facebook-connect'); ?></span>
                    <code><?php _e('Action:'); ?>
                        woocommerce_login_form_end</code></label><br>
            </fieldset>
        </td>
    </tr>

    <tr>
        <th scope="row"><?php _e('WooCommerce billing form', 'nextend-facebook-connect'); ?></th>
        <td>
            <fieldset>
                <legend class="screen-reader-text">
                    <span><?php _e('WooCommerce billing form', 'nextend-facebook-connect'); ?></span></legend>
                <label><input type="radio" name="woocommerce_billing"
                              value="" <?php if ($settings->get('woocommerce_billing') == '') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('No Connect button in billing form', 'nextend-facebook-connect'); ?></span></label><br>
                <label><input type="radio" name="woocommerce_billing"
                              value="before" <?php if ($settings->get('woocommerce_billing') == 'before') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('Connect button before billing form', 'nextend-facebook-connect'); ?></span>
                    <code><?php _e('Action:'); ?>
                        woocommerce_before_checkout_billing_form</code></label><br>
                <label><input type="radio" name="woocommerce_billing"
                              value="after" <?php if ($settings->get('woocommerce_billing') == 'after') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('Connect button after billing form', 'nextend-facebook-connect'); ?></span></label>
                <code><?php _e('Action:'); ?>
                    woocommerce_after_checkout_billing_form</code><br>
            </fieldset>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php _e('WooCommerce account details', 'nextend-facebook-connect'); ?></th>
        <td>
            <fieldset>
                <legend class="screen-reader-text">
                    <span><?php _e('WooCommerce account details', 'nextend-facebook-connect'); ?></span></legend>
                <label><input type="radio" name="woocommerce_account_details"
                              value="before" <?php if ($settings->get('woocommerce_account_details') == 'before') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('Link buttons before account details', 'nextend-facebook-connect'); ?></span>
                    <code><?php _e('Action:'); ?>
                        woocommerce_edit_account_form_start</code></label><br>
                <label><input type="radio" name="woocommerce_account_details"
                              value="after" <?php if ($settings->get('woocommerce_account_details') == 'after') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('Link buttons after account details', 'nextend-facebook-connect'); ?></span>
                    <code><?php _e('Action:'); ?>
                        woocommerce_edit_account_form_end</code></label><br>
            </fieldset>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php _e('WooCommerce button style', 'nextend-facebook-connect'); ?></th>
        <td>
            <fieldset>
                <legend class="screen-reader-text">
                    <span><?php _e('WooCommerce button style', 'nextend-facebook-connect'); ?></span></legend>
                <label>
                    <input type="radio" name="woocoommerce_form_button_style"
                           value="default" <?php if ($settings->get('woocoommerce_form_button_style') == 'default') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('Default', 'nextend-facebook-connect'); ?></span><br/>
                    <img src="<?php echo plugins_url('images/buttons/default.png', NSL_ADMIN_PATH) ?>"/>
                </label>
                <label>
                    <input type="radio" name="woocoommerce_form_button_style"
                           value="icon" <?php if ($settings->get('woocoommerce_form_button_style') == 'icon') : ?> checked="checked" <?php endif; ?><?php echo $attr; ?>>
                    <span><?php _e('Icon', 'nextend-facebook-connect'); ?></span><br/>
                    <img src="<?php echo plugins_url('images/buttons/icon.png', NSL_ADMIN_PATH) ?>"/>
                </label><br>
            </fieldset>
        </td>
    </tr>
    </tbody>
</table>

<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
                         value="<?php _e('Save Changes'); ?>"></p>