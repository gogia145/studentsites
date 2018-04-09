<?php

require_once(NSL_PATH . '/persistent.php');
require_once(NSL_PATH . '/class-settings.php');
require_once(NSL_PATH . '/includes/provider.php');
require_once(NSL_PATH . '/admin/admin.php');

require_once(NSL_PATH . '/compat.php');

class NextendSocialLogin {

    public static $version = '3.0.2';

    public static $nslPROMinVersion = '3.0.0';

    public static function checkVersion() {
        if (version_compare(self::$version, NextendSocialLoginPRO::$nslMinVersion, '<') || version_compare(NextendSocialLoginPRO::$version, self::$nslPROMinVersion, '<')) {
            if (is_admin()) {
                NextendSocialLoginAdminNotices::addError(sprintf(__('%5$s plugin (version: %1$s, required: %2$s or newer) is not compatible with the PRO addon (version: %3$s, required: %4$s or newer). Please upgrade to the latest version! PRO addon disabled.'), self::$version, NextendSocialLoginPRO::$nslMinVersion, NextendSocialLoginPRO::$version, self::$nslPROMinVersion, "Nextend Social Login"));
            }

            return false;
        }

        return true;
    }

    /** @var NextendSocialLoginSettings */
    public static $settings;

    private static $styles = array(
        'default' => array(
            'container' => 'nsl-container-block'
        ),
        'icon'    => array(
            'container' => 'nsl-container-inline'
        )
    );

    public static $providersPath;

    /**
     * @var NextendSocialProviderDummy[]
     */
    public static $providers = array();

    /**
     * @var NextendSocialProvider[]
     */
    public static $enabledProviders = array();

    private static $ordering = array();

    private static $loginHeadAdded = false;
    private static $counter = 1;

    public static function init() {
        add_action('plugins_loaded', 'NextendSocialLogin::plugins_loaded');
        register_activation_hook(NSL_PATH_FILE, 'NextendSocialLogin::install');

        add_action('activate_nextend-google-connect/nextend-google-connect.php', 'NextendSocialLogin::compatPreventActivationGoogle');
        add_action('activate_nextend-twitter-connect/nextend-twitter-connect.php', 'NextendSocialLogin::compatPreventActivationTwitter');

        add_action('delete_user', 'NextendSocialLogin::delete_user');

        self::$settings = new NextendSocialLoginSettings('nextend_social_login', array(
            'enabled'                        => array(),
            'ordering'                       => array(
                'facebook',
                'google',
                'twitter'
            ),
            'license_key'                    => '',
            'license_key_ok'                 => '0',
            'redirect'                       => '',
            'redirect_reg'                   => '',
            'login_form_layout'              => 'below',
            'login_form_button_style'        => 'default',
            'comment_login_button'           => 'show',
            'comment_button_style'           => 'default',
            'woocommerce_login'              => 'after',
            'woocommerce_billing'            => 'before',
            'woocoommerce_form_button_style' => 'default',
            'woocommerce_account_details'    => 'before',
            'debug'                          => '0'
        ));
    }

    public static function plugins_loaded() {

        if (get_option('nsl-version') != self::$version) {
            NextendSocialLogin::install();
            update_option('nsl-version', self::$version, true);
            wp_redirect(set_url_scheme('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
            exit;
        }

        load_plugin_textdomain('nextend-facebook-connect', FALSE, basename(dirname(__FILE__)) . '/languages/');

        NextendSocialLoginAdmin::init();

        self::$providersPath = NSL_PATH . '/providers/';

        $providers = array_diff(scandir(self::$providersPath), array(
            '..',
            '.'
        ));

        foreach ($providers AS $provider) {
            if (file_exists(self::$providersPath . $provider . '/' . $provider . '.php')) {
                require_once(self::$providersPath . $provider . '/' . $provider . '.php');
            }
        }

        do_action('nsl-add-providers');

        self::$ordering = array_flip(self::$settings->get('ordering'));
        uksort(self::$providers, 'NextendSocialLogin::sortProviders');
        uksort(self::$enabledProviders, 'NextendSocialLogin::sortProviders');

        do_action('nsl-providers-loaded');

        add_action('login_init', 'NextendSocialLogin::login_init');
        add_action('wp_logout', 'NextendSocialLogin::wp_logout');

        add_action('parse_request', 'NextendSocialLogin::editProfileRedirect');

        if (count(self::$enabledProviders) > 0) {


            add_action('login_form', 'NextendSocialLogin::addLoginFormButtons');
            add_action('register_form', 'NextendSocialLogin::addLoginFormButtons');
            add_action('bp_sidebar_login_form', 'NextendSocialLogin::addLoginButtons');


            add_action('profile_personal_options', 'NextendSocialLogin::addLinkAndUnlinkButtons');

            add_action('login_form_login', 'NextendSocialLogin::jQuery');
            add_action('login_form_register', 'NextendSocialLogin::jQuery');

            add_action('wp_head', 'NextendSocialLogin::styles', 100);
            add_action('admin_head', 'NextendSocialLogin::styles', 100);
            add_action('login_head', 'NextendSocialLogin::loginHead', 100);

            add_action('wp_print_footer_scripts', 'NextendSocialLogin::scripts', 100);
            add_action('login_footer', 'NextendSocialLogin::scripts', 100);


            add_filter('get_avatar', 'NextendSocialLogin::renderAvatar', 5, 5);
            add_filter('bp_core_fetch_avatar', 'NextendSocialLogin::renderAvatarBP', 3, 5);


            add_shortcode('nextend_social_login', 'NextendSocialLogin::shortcode');
        }

        add_action('admin_print_footer_scripts', 'NextendSocialLogin::scripts', 100);

        require_once(NSL_PATH . '/widget.php');
    }

    public static function styles() {

        $stylesheet = self::get_template_part('style.css');
        if (!empty($stylesheet) && file_exists($stylesheet)) {
            echo '<style type="text/css">' . file_get_contents($stylesheet) . '</style>';
        }
    }

    public static function loginHead() {
        self::styles();

        $template = self::get_template_part('login-layout-' . sanitize_file_name(self::$settings->get('login_form_layout')) . '.php');
        if (!empty($template) && file_exists($template)) {
            require($template);
        }

        self::$loginHeadAdded = true;
    }

    public static function scripts() {
        $scripts = NSL_PATH . '/js/nsl.js';
        if (file_exists($scripts)) {
            echo '<script type="text/javascript">' . file_get_contents($scripts) . '</script>';
        }
    }

    public static function install() {
        global $wpdb;
        $table_name = $wpdb->prefix . "social_users";
        $sql        = "CREATE TABLE " . $table_name . " (`ID` int(11) NOT NULL, `type` varchar(20) NOT NULL, `identifier` varchar(100) NOT NULL, KEY `ID` (`ID`,`type`));";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        //Legacy

        $facebookSettings = maybe_unserialize(get_option('nextend_fb_connect'));

        if (!empty($facebookSettings['fb_appid']) && !empty($facebookSettings['fb_secret'])) {
            $providerPath = NSL_PATH . '/providers/facebook/facebook.php';
            if (file_exists($providerPath)) {

                require_once($providerPath);

                self::$providers['facebook']->settings->update(array(
                    'legacy' => 1
                ));
            }
        }
        if (function_exists('new_google_connect_install')) {
            $googleSettings = maybe_unserialize(get_option('nextend_google_connect'));
            if (!empty($googleSettings['google_client_id']) && !empty($googleSettings['google_client_secret'])) {
                $providerPath = NSL_PATH . '/providers/google/google.php';
                if (file_exists($providerPath)) {

                    require_once($providerPath);

                    self::$providers['google']->settings->update(array(
                        'legacy' => 1
                    ));
                }
            }

            NextendSocialLogin::compatDeactivateGoogle();
            add_action('activated_plugin', 'NextendSocialLogin::compatDeactivateGoogle');
        }

        if (function_exists('new_twitter_connect_install')) {
            $twitterSettings = maybe_unserialize(get_option('nextend_twitter_connect'));
            if (!empty($twitterSettings['twitter_consumer_key']) && !empty($twitterSettings['twitter_consumer_secret'])) {
                $providerPath = NSL_PATH . '/providers/twitter/twitter.php';
                if (file_exists($providerPath)) {

                    require_once($providerPath);

                    self::$providers['twitter']->settings->update(array(
                        'legacy' => 1
                    ));
                }
            }

            NextendSocialLogin::compatDeactivateTwitter();
            add_action('activated_plugin', 'NextendSocialLogin::compatDeactivateTwitter');
        }
    }

    public static function compatDeactivateGoogle() {
        if (is_plugin_active('nextend-google-connect/nextend-google-connect.php')) {
            deactivate_plugins('nextend-google-connect/nextend-google-connect.php');
        }
    }

    public static function compatPreventActivationGoogle() {
        printf(__('%s took the place of Nextend Google Connect. You can delete Nextend Google Connect as it is not needed anymore.', 'nextend-facebook-connect'), "Nextend Social Login");
        exit;
    }

    public static function compatDeactivateTwitter() {
        if (is_plugin_active('nextend-twitter-connect/nextend-twitter-connect.php')) {
            deactivate_plugins('nextend-twitter-connect/nextend-twitter-connect.php');
        }
    }

    public static function compatPreventActivationTwitter() {
        printf(__('%s took the place of Nextend Twitter Connect. You can delete Nextend Twitter Connect as it is not needed anymore.', 'nextend-facebook-connect'), "Nextend Social Login");
        exit;
    }

    public static function sortProviders($a, $b) {
        if (isset(self::$ordering[$a]) && isset(self::$ordering[$b])) {
            if (self::$ordering[$a] < self::$ordering[$b]) {
                return -1;
            }

            return 1;
        }
        if (isset(self::$ordering[$a])) {
            return -1;
        }

        return 1;
    }

    /**
     * @param $provider NextendSocialProviderDummy
     */
    public static function addProvider($provider) {
        if (in_array($provider->getId(), self::$settings->get('enabled'))) {
            if ($provider->isTested() && $provider->enable()) {
                self::$enabledProviders[$provider->getId()] = $provider;
            }
        }
        self::$providers[$provider->getId()] = $provider;
    }

    public static function enableProvider($providerID) {
        if (isset(self::$providers[$providerID])) {
            $enabled   = self::$settings->get('enabled');
            $enabled[] = self::$providers[$providerID]->getId();
            $enabled   = array_unique($enabled);

            self::$settings->update(array(
                'enabled' => $enabled
            ));
        }
    }

    public static function disableProvider($providerID) {
        if (isset(self::$providers[$providerID])) {

            $enabled = array_diff(self::$settings->get('enabled'), array(self::$providers[$providerID]->getId()));

            self::$settings->update(array(
                'enabled' => $enabled
            ));
        }
    }

    public static function isProviderEnabled($providerID) {
        return isset(self::$enabledProviders[$providerID]);
    }

    public static function wp_logout() {
        NextendSocialLoginPersistentAnonymous::destroy();
    }

    public static function login_init() {

        add_filter('wp_login_errors', 'NextendSocialLogin::wp_login_errors');

        if (isset($_GET['interim_login']) && $_GET['interim_login'] === 'nsl' && is_user_logged_in()) {
            self::onInterimLoginSuccess();
        }

        if (isset($_REQUEST['loginFacebook']) && $_REQUEST['loginFacebook'] == '1') {
            $_REQUEST['loginSocial'] = 'facebook';
        }
        if (isset($_REQUEST['loginGoogle']) && $_REQUEST['loginGoogle'] == '1') {
            $_REQUEST['loginSocial'] = 'google';
        }
        if (isset($_REQUEST['loginTwitter']) && $_REQUEST['loginTwitter'] == '1') {
            $_REQUEST['loginTwitter'] = 'twitter';
        }

        if (isset($_REQUEST['loginSocial']) && isset(self::$providers[$_REQUEST['loginSocial']]) && (self::$providers[$_REQUEST['loginSocial']]->isEnabled() || self::$providers[$_REQUEST['loginSocial']]->isTest())) {
            self::$providers[$_REQUEST['loginSocial']]->connect();
        }
    }

    private static function onInterimLoginSuccess() {
        global $interim_login;
        do_action("login_form_login");
        $customize_login = isset($_REQUEST['customize-login']);
        if ($customize_login) {
            wp_enqueue_script('customize-base');
        }

        $message       = '<p class="message">' . __('You have logged in successfully.') . '</p>';
        $interim_login = 'success';
        login_header('', $message); ?>
        </div>
        <?php
        /** This action is documented in wp-login.php */
        do_action('login_footer'); ?>
        <?php if ($customize_login) : ?>
            <script type="text/javascript">setTimeout(function () {
                    new wp.customize.Messenger({url: '<?php echo wp_customize_url(); ?>', channel: 'login'}).send(
                        'login');
                }, 1000);</script>
        <?php endif; ?>
        </body></html>
        <?php exit;
    }

    public static function wp_login_errors($errors) {

        if (empty($errors)) {
            $errors = new WP_Error();
        }


        $error = NextendSocialLoginPersistentAnonymous::get('_login_error');
        if ($error !== false) {
            $errors->add('error', $error);
            NextendSocialLoginPersistentAnonymous::delete('_login_error');
        }

        return $errors;
    }

    public static function editProfileRedirect() {
        global $wp;

        if (isset($wp->query_vars['editProfileRedirect'])) {
            if (function_exists('bp_loggedin_user_domain')) {
                header('LOCATION: ' . bp_loggedin_user_domain() . 'profile/edit/group/1/');
            } else {
                header('LOCATION: ' . self_admin_url('profile.php'));
            }
            exit;
        }
    }

    public static function jQuery() {
        wp_enqueue_script('jquery');
    }

    public static function addLoginFormButtons() {
        self::renderLoginButtons();
    }

    public static function addLoginButtons() {
        self::renderLoginButtons(NextendSocialLogin::getCurrentPageURL());
    }

    private static function renderLoginButtons($redirect_to = false) {

        if (!self::$loginHeadAdded) {
            $index = self::$counter++;
            echo '<div id="nsl-custom-login-form-' . $index . '">' . self::renderButtonsWithContainer(self::$settings->get('login_form_button_style'), false, $redirect_to) . '</div>';
            echo '<script type="text/javascript">(function($){$("document").ready(function(){var el = $("#nsl-custom-login-form-' . $index . '");el.appendTo(el.closest("form"))})})(jQuery)</script>';
        } else {

            echo self::renderButtonsWithContainer(self::$settings->get('login_form_button_style'), false, $redirect_to);
        }

    }

    public static function addLinkAndUnlinkButtons() {
        echo self::renderLinkAndUnlinkButtons();
    }

    public static function renderLinkAndUnlinkButtons() {
        if (count(self::$enabledProviders)) {
            $buttons = '<h2>' . __('Social Login', 'nextend-facebook-connect') . '</h2>';
            foreach (self::$enabledProviders AS $provider) {
                if ($provider->isCurrentUserConnected()) {
                    $buttons .= $provider->getUnLinkButton();
                } else {
                    $buttons .= $provider->getLinkButton();
                }
            }

            return '<div class="nsl-container ' . self::$styles['default']['container'] . '">' . $buttons . '</div>';
        }

        return '';
    }

    public static function getAvatar($user_id) {
        foreach (self::$enabledProviders AS $provider) {
            $avatar = $provider->getAvatar($user_id);
            if ($avatar !== false) {
                return $avatar;
            }
        }

        return false;
    }

    public static function renderAvatar($avatar = '', $id_or_email, $size = 96, $default = '', $alt = false) {

        $id = 0;

        if (is_numeric($id_or_email)) {
            $id = $id_or_email;
        } else if (is_string($id_or_email)) {
            $user = get_user_by('email', $id_or_email);
            if ($user) {
                $id = $user->ID;
            }
        } else if (is_object($id_or_email)) {
            if (!empty($id_or_email->comment_author_email)) {
                $user = get_user_by('email', $id_or_email->comment_author_email);
                if ($user) {
                    $id = $user->ID;
                }
            } else if (!empty($id_or_email->user_id)) {
                $id = $id_or_email->user_id;
            }
        }
        if ($id == 0) {
            return $avatar;
        }

        $pic = self::getAvatar($id);
        if (!$pic) {
            return $avatar;
        }
        $avatar = preg_replace('/src=("|\').*?("|\')/i', 'src=\'' . $pic . '\'', $avatar);

        return $avatar;
    }

    public static function renderAvatarBP($avatar = '', $params, $id) {
        if (!is_numeric($id) || strpos($avatar, 'gravatar') === false) {
            return $avatar;
        }

        $pic = self::getAvatar($id);
        if (!$pic || $pic == '') {
            return $avatar;
        }
        $avatar = preg_replace('/src=("|\').*?("|\')/i', 'src=\'' . $pic . '\'', $avatar);

        return $avatar;
    }

    public static function shortcode($atts) {
        if (!is_user_logged_in()) {

            $atts = array_merge(array(
                'style'    => 'default',
                'provider' => false,
                'redirect' => false
            ), $atts);

            $providers  = false;
            $providerID = $atts['provider'] === false ? false : $atts['provider'];
            if ($providerID !== false && isset(self::$enabledProviders[$providerID])) {
                $providers = array(self::$enabledProviders[$providerID]);
            }

            return self::renderButtonsWithContainer($atts['style'], $providers, $atts['redirect']);
        }

        return '';
    }

    /**
     * @param string                       $style
     * @param bool|NextendSocialProvider[] $providers
     * @param bool|string                  $redirect_to
     *
     * @return string
     */
    public static function renderButtonsWithContainer($style = 'default', $providers = false, $redirect_to = false) {

        if (!isset(self::$styles[$style])) {
            $style = 'default';
        }

        $enabledProviders = false;
        if (is_array($providers)) {
            $enabledProviders = array();
            foreach ($providers AS $provider) {
                if ($provider && isset(self::$enabledProviders[$provider->getId()])) {
                    $enabledProviders[$provider->getId()] = $provider;
                }
            }
        }
        if ($enabledProviders === false) {
            $enabledProviders = self::$enabledProviders;
        }

        if (count($enabledProviders)) {
            $buttons = '';
            foreach ($enabledProviders AS $provider) {
                $buttons .= $provider->getConnectButton($style, $redirect_to);
            }

            return '<div class="nsl-container ' . self::$styles[$style]['container'] . '">' . $buttons . '</div>';
        }

        return '';
    }

    public static function getCurrentPageURL() {

        return set_url_scheme('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }

    public static function get_template_part($file_name, $name = null) {
        // Execute code for this part
        do_action('get_template_part_' . $file_name, $file_name, $name);

        // Setup possible parts
        $templates   = array();
        $templates[] = $file_name;

        // Allow template parts to be filtered
        $templates = apply_filters('nsl_get_template_part', $templates, $file_name, $name);

        // Return the part that is found
        return self::locate_template($templates);
    }

    public static function locate_template($template_names) {
        // No file found yet
        $located = false;

        // Try to find a template file
        foreach ((array)$template_names as $template_name) {

            // Continue if template is empty
            if (empty($template_name)) {
                continue;
            }

            // Trim off any slashes from the template name
            $template_name = ltrim($template_name, '/');
            // Check child theme first
            if (file_exists(trailingslashit(get_stylesheet_directory()) . 'nsl/' . $template_name)) {
                $located = trailingslashit(get_stylesheet_directory()) . 'nsl/' . $template_name;
                break;

                // Check parent theme next
            } elseif (file_exists(trailingslashit(get_template_directory()) . 'nsl/' . $template_name)) {
                $located = trailingslashit(get_template_directory()) . 'nsl/' . $template_name;
                break;

                // Check theme compatibility last
            } elseif (file_exists(trailingslashit(self::get_templates_dir()) . $template_name)) {
                $located = trailingslashit(self::get_templates_dir()) . $template_name;
                break;
            } elseif (defined('NSL_PRO_PATH') && file_exists(trailingslashit(NSL_PRO_PATH) . 'template-parts/' . $template_name)) {
                $located = trailingslashit(NSL_PRO_PATH) . 'template-parts/' . $template_name;
                break;
            }
        }

        return $located;
    }

    public static function get_templates_dir() {
        return NSL_PATH . '/template-parts';
    }

    public static function delete_user($user_id) {
        /** @var $wpdb WPDB */
        global $wpdb;

        $wpdb->delete($wpdb->prefix . 'social_users', array(
            'ID' => $user_id
        ), array(
            '%d'
        ));

    }
}

NextendSocialLogin::init();
