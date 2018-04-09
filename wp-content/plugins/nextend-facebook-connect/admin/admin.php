<?php
define('NSL_ADMIN_PATH', __FILE__);

require_once dirname(__FILE__) . '/upgrader.php';
NextendSocialUpgrader::init();

class NextendSocialLoginAdmin {

    public static function init() {
        add_action('admin_menu', 'NextendSocialLoginAdmin::admin_menu', 1);
        add_action('admin_init', 'NextendSocialLoginAdmin::admin_init');

        add_filter('plugin_action_links', 'NextendSocialLoginAdmin::plugin_action_links', 10, 2);

        add_filter('nsl_update_settings_validate_nextend_social_login', 'NextendSocialLoginAdmin::validateSettings', 10, 2);

        require_once dirname(__FILE__) . '/notices.php';
        NextendSocialLoginAdminNotices::init();
    }

    public static function getAdminUrl($view = 'providers') {

        return add_query_arg(array(
            'page' => 'nextend-social-login',
            'view' => $view
        ), admin_url('options-general.php'));
    }

    public static function admin_menu() {
        $menu = add_options_page('Nextend Social Login', 'Nextend Social Login', 'manage_options', 'nextend-social-login', array(
            'NextendSocialLoginAdmin',
            'display_admin'
        ));

        add_action('admin_print_styles-' . $menu, 'NextendSocialLoginAdmin::admin_css');
    }

    public static function admin_css() {
        wp_enqueue_style('nsl-admin-stylesheet', plugins_url('/style.css', NSL_ADMIN_PATH));
    }

    public static function display_admin() {
        $view = !empty($_REQUEST['view']) ? $_REQUEST['view'] : '';

        if (substr($view, 0, 9) == 'provider-') {
            $providerID = substr($view, 9);
            if (isset(NextendSocialLogin::$providers[$providerID])) {
                self::display_admin_area('provider', $providerID);

                return;
            }
        }
        switch ($view) {
            case 'global-settings':
                self::display_admin_area('global-settings');
                break;
            case 'pro-addon':
                self::display_admin_area('pro-addon');
                break;
            case 'install-pro':
                if (check_admin_referer('nextend-social-login')) {
                    self::display_admin_area('install-pro');
                } else {
                    self::display_admin_area('providers');
                }
                break;
            default:
                self::display_admin_area('providers');
                break;
        }
    }

    /**
     * @param string $view
     * @param string $currentProvider
     */
    private static function display_admin_area($view, $currentProvider = '') {
        if (empty($currentProvider)) {
            include(dirname(__FILE__) . '/templates/header.php');
            include(dirname(__FILE__) . '/templates/menu.php');

            NextendSocialLoginAdminNotices::displayNotices();

            /** @var string $view */
            include(dirname(__FILE__) . '/templates/' . $view . '.php');
            include(dirname(__FILE__) . '/templates/footer.php');
        } else {
            include(dirname(__FILE__) . '/templates/' . $view . '.php');
        }
    }

    public static function renderProSettings() {
        include(dirname(__FILE__) . '/templates/global-settings-pro.php');
    }

    public static function admin_init() {

        if (isset($_GET['page']) && $_GET['page'] == 'nextend-social-login') {
            if (!empty($_GET['view']) && !empty($_GET['provider'])) {
                switch ($_GET['view']) {
                    case 'enable':
                    case 'sub-enable':
                        if (check_admin_referer('nextend-social-login_enable_' . $_GET['provider'])) {
                            NextendSocialLogin::enableProvider($_GET['provider']);
                        }
                        if ($_GET['view'] == 'sub-enable') {
                            wp_redirect(NextendSocialLogin::$providers[$_GET['provider']]->getAdminUrl('settings'));
                            exit;
                        }
                        break;
                    case 'disable':
                    case 'sub-disable':
                        if (check_admin_referer('nextend-social-login_disable_' . $_GET['provider'])) {
                            NextendSocialLogin::disableProvider($_GET['provider']);
                        }
                        if ($_GET['view'] == 'sub-disable') {
                            wp_redirect(NextendSocialLogin::$providers[$_GET['provider']]->getAdminUrl('settings'));
                            exit;
                        }
                        break;
                }
                wp_redirect(self::getAdminUrl());
                exit;
            }
        }
        add_action('admin_post_nextend-social-login', 'NextendSocialLoginAdmin::save_form_data');
        add_action('wp_ajax_nextend-social-login', 'NextendSocialLoginAdmin::ajax_save_form_data');


        add_action('admin_enqueue_scripts', 'NextendSocialLoginAdmin::admin_enqueue_scripts');

        if (!function_exists('curl_init')) {
            add_settings_error('nextend-social', 'settings_updated', printf(__('%s needs the CURL PHP extension.', 'nextend-facebook-connect'), 'Nextend Social Login') . ' ' . __('Please contact your server administrator and ask for solution!', 'nextend-facebook-connect'), 'error');
        } else {
            $version       = curl_version();
            $ssl_supported = ($version['features'] & CURL_VERSION_SSL);
            if (!$ssl_supported) {
                add_settings_error('nextend-social', 'settings_updated', __('Https protocol is not supported or disabled in CURL.', 'nextend-facebook-connect') . ' ' . __('Please contact your server administrator and ask for solution!', 'nextend-facebook-connect'), 'error');

                ob_start();
                var_dump($version);
                $curlDebugHTML = ob_get_clean();
                add_settings_error('nextend-social', 'settings_updated', '<h2>CURL debug</h2>' . $curlDebugHTML, 'error');
            }
        }

        if (!function_exists('json_decode')) {
            add_settings_error('nextend-social', 'settings_updated', printf(__('%s needs json_decode function.', 'nextend-facebook-connect'), 'Nextend Social Login') . ' ' . __('Please contact your server administrator and ask for solution!', 'nextend-facebook-connect'), 'error');
        }
    }

    public static function save_form_data() {
        if (current_user_can('manage_options') && check_admin_referer('nextend-social-login')) {
            foreach ($_POST AS $k => $v) {
                if (is_string($v)) {
                    $_POST[$k] = stripslashes($v);
                }
            }

            $view = !empty($_REQUEST['view']) ? $_REQUEST['view'] : '';

            if ($view == 'global-settings') {

                NextendSocialLogin::$settings->update($_POST);

                NextendSocialLoginAdminNotices::addSuccess(__('Settings saved.'));
                wp_redirect(self::getAdminUrl($view));
                exit;
            } else if ($view == 'pro-addon') {

                NextendSocialLogin::$settings->update($_POST);

                if (NextendSocialLogin::$settings->get('license_key_ok') == '1') {
                    NextendSocialLoginAdminNotices::addSuccess(__('The authorization was successful', 'nextend-facebook-connect'));
                }

                wp_redirect(self::getAdminUrl($view));
                exit;
            } else if ($view == 'pro-addon-deauthorize') {

                NextendSocialLogin::$settings->update(array(
                    'license_key' => ''
                ));

                NextendSocialLoginAdminNotices::addSuccess(__('Deauthorize completed.', 'nextend-facebook-connect'));

                wp_redirect(self::getAdminUrl('pro-addon'));
                exit;

            } else if ($view == 'import') {
                $provider = isset($_GET['provider']) ? $_GET['provider'] : '';
                if (!empty($provider) && isset(NextendSocialLogin::$providers[$provider]) && NextendSocialLogin::$providers[$provider]->getState() == 'legacy') {
                    NextendSocialLogin::$providers[$provider]->import();

                    wp_redirect(NextendSocialLogin::$providers[$provider]->getAdminUrl('settings'));
                    exit;
                }

                wp_redirect(NextendSocialLoginAdmin::getAdminUrl());
                exit;
            } else if (substr($view, 0, 9) == 'provider-') {
                $providerID = substr($view, 9);
                if (isset(NextendSocialLogin::$providers[$providerID])) {
                    NextendSocialLogin::$providers[$providerID]->settings->update($_POST);

                    NextendSocialLoginAdminNotices::addSuccess(__('Settings saved.'));
                    wp_redirect(NextendSocialLogin::$providers[$providerID]->getAdminUrl(isset($_POST['subview']) ? $_POST['subview'] : ''));
                    exit;
                }
            }
        }

        wp_redirect(self::getAdminUrl());
        exit;
    }

    public static function ajax_save_form_data() {
        check_ajax_referer('nextend-social-login');
        if (current_user_can('manage_options')) {
            $view = !empty($_POST['view']) ? $_POST['view'] : '';
            if ($view === 'orderProviders') {
                if (!empty($_POST['ordering'])) {
                    NextendSocialLogin::$settings->update(array(
                        'ordering' => $_POST['ordering']
                    ));
                }
            }
        }
    }

    public static function validateSettings($newData, $postedData) {

        if (isset($postedData['redirect'])) {
            if (isset($postedData['custom_redirect_enabled']) && $postedData['custom_redirect_enabled'] == '1') {
                $newData['redirect'] = trim(sanitize_text_field($postedData['redirect']));
            } else {
                $newData['redirect'] = '';
            }
        }

        if (isset($postedData['redirect_reg'])) {
            if (isset($postedData['custom_redirect_reg_enabled']) && $postedData['custom_redirect_reg_enabled'] == '1') {
                $newData['redirect_reg'] = trim(sanitize_text_field($postedData['redirect_reg']));
            } else {
                $newData['redirect_reg'] = '';
            }
        }

        foreach ($postedData as $key => $value) {
            switch ($key) {
                case 'debug':
                    if ($value == 1) {
                        $newData[$key] = 1;
                    } else {
                        $newData[$key] = 0;
                    }
                    break;
                case 'enabled':
                    if (is_array($value)) {
                        $newData[$key] = $value;
                    }
                    break;
                case 'ordering':
                    if (is_array($value)) {
                        $newData[$key] = $value;
                    }
                    break;
                case 'license_key':
                    $value         = trim(sanitize_text_field($value));
                    $newData[$key] = $value;
                    if ($value != NextendSocialLogin::$settings->get('license_key')) {
                        $newData['license_key_ok'] = '0';

                        if (!empty($value)) {
                            try {
                                $response = self::apiCall('test-license', array('license_key' => $value));
                                if ($response === 'OK') {
                                    $newData['license_key_ok'] = '1';
                                }
                            } catch (Exception $e) {
                                NextendSocialLoginAdminNotices::addError($e->getMessage());
                            }
                        }
                    }
                    break;
            }
        }

        return $newData;
    }

    public static function plugin_action_links($links, $file) {

        if ($file != NSL_PLUGIN_BASENAME) {
            return $links;
        }
        $settings_link = '<a href="' . esc_url(menu_page_url('nextend-social-login', false)) . '">' . __('Settings') . '</a>';
        array_unshift($links, $settings_link);

        return $links;
    }

    public static function admin_enqueue_scripts() {
        if ('settings_page_nextend-social-login' === get_current_screen()->id) {

            // Since WordPress 4.9
            if (function_exists('wp_enqueue_code_editor')) {
                // Enqueue code editor and settings for manipulating HTML.
                $settings = wp_enqueue_code_editor(array('type' => 'text/html'));

                // Bail if user disabled CodeMirror.
                if (false === $settings) {
                    return;
                }

                wp_add_inline_script('code-editor', sprintf('jQuery( function() { var settings = %s; jQuery(".nextend-html-editor").each(function(i, el){wp.codeEditor.initialize( el, settings);}); } );', wp_json_encode($settings)));

                $settings['codemirror']['readOnly'] = 'nocursor';

                wp_add_inline_script('code-editor', sprintf('jQuery( function() { var settings = %s; jQuery(".nextend-html-editor-readonly").each(function(i, el){wp.codeEditor.initialize( el, settings);}); } );', wp_json_encode($settings)));
            }

            if (isset($_GET['view']) && $_GET['view'] == 'pro-addon') {
                wp_enqueue_script('plugin-install');
                wp_enqueue_script('updates');
            }
        }
    }

    private static $endpoint = 'https://secure.nextendweb.com/wp-json/nextend-api/v2/';

    public static function getEndpoint($action = '') {
        return self::$endpoint . 'product/nsl/' . urlencode($action);
    }

    /**
     * @param       $action
     * @param array $args
     *
     * @return bool|mixed
     * @throws Exception
     */
    public static function apiCall($action, $args = array()) {

        require_once NSL_PATH . '/includes/curl/Curl.php';

        $curl = new NSLCurl();


        $response = $curl->get(self::apiUrl($action, $args));

        if ($curl->error) {
            if (isset($response['message'])) {
                $message = 'Nextend Social Login Pro Addon: ' . $response['message'];

                NextendSocialLoginAdminNotices::addError($message);

                return new WP_Error('error', $message);
            } else {
                throw new Exception('Nextend Social Login Pro Addon: ' . $curl->errorCode . ': ' . $curl->errorMessage);
            }
        }

        return $response;
    }

    public static function apiUrl($action, $args = array()) {
        $args = array_merge(array(
            'platform'    => 'wordpress',
            'domain'      => parse_url(site_url(), PHP_URL_HOST),
            'license_key' => NextendSocialLogin::$settings->get('license_key')
        ), $args);

        foreach ($args AS $key => $value) {
            if (is_string($value)) {
                $args[$key] = urlencode($value);
            }
        }

        return add_query_arg($args, self::getEndpoint($action));
    }

    public static function showProBox() {
        $isPRO = apply_filters('nsl-pro', false);
        if (!$isPRO) {
            include(dirname(__FILE__) . '/templates/pro.php');
        }
    }

    public static function getProState() {
        if (NextendSocialLogin::$settings->get('license_key_ok') == '1') {
            $isPRO = apply_filters('nsl-pro', false);
            if ($isPRO) {
                return 'activated';
            } else if (!current_user_can('install_plugins')) {
                return 'no-capability';
            } else {
                if (file_exists(WP_PLUGIN_DIR . '/nextend-social-login-pro/nextend-social-login-pro.php')) {
                    return 'installed';
                } else {
                    return 'not-installed';
                }
            }
        }

        return 'no-license';
    }

    public static function trackUrl($url, $source) {
        return add_query_arg(array(
            'utm_campaign' => 'nsl',
            'utm_source'   => urlencode($source),
            'utm_medium'   => 'nsl-wordpress-' . (apply_filters('nsl-pro', false) ? 'pro' : 'free')
        ), $url);
    }
}