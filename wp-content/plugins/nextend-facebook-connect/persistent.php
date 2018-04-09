<?php

class NextendSocialLoginPersistentAnonymous {

    private static function getSessionID($mustCreate = false) {
        if (isset($_COOKIE['nsl_session'])) {
            if (get_site_transient('n_' . $_COOKIE['nsl_session']) !== false) {
                return $_COOKIE['nsl_session'];
            }
        }
        if ($mustCreate) {
            $_COOKIE['nsl_session'] = uniqid('nsl', true);
            self::setcookie('nsl_session', $_COOKIE['nsl_session'], time() + DAY_IN_SECONDS, apply_filters('nsl_session_use_secure_cookie', false));
            set_site_transient('n_' . $_COOKIE['nsl_session'], 1, 3600);

            return $_COOKIE['nsl_session'];
        }

        return false;
    }

    public static function set($key, $value, $expiration = 3600) {

        set_site_transient(self::getSessionID(true) . $key, (string)$value, $expiration);
    }

    public static function get($key) {

        $session = self::getSessionID();
        if ($session) {
            return get_site_transient($session . $key);
        }

        return false;
    }

    public static function delete($key) {

        $session = self::getSessionID();
        if ($session) {
            delete_site_transient(self::getSessionID() . $key);
        }
    }

    public static function destroy() {
        $sessionID = self::getSessionID();
        if ($sessionID) {
            self::setcookie('nsl_session', $sessionID, time() - YEAR_IN_SECONDS, apply_filters('nsl_session_use_secure_cookie', false));
            delete_site_transient('n_' . $sessionID);
        }
    }

    private static function setcookie($name, $value, $expire, $secure = false) {

        setcookie($name, $value, $expire, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, $secure);
    }

}

class NextendSocialLoginPersistentUser {

    private static function getSessionID() {
        return get_current_user_id();
    }

    public static function set($key, $value, $expiration = 3600) {

        set_site_transient(self::getSessionID() . $key, (string)$value, $expiration);
    }

    public static function get($key) {

        return get_site_transient(self::getSessionID() . $key);
    }

    public static function delete($key) {

        delete_site_transient(self::getSessionID() . $key);
    }

}