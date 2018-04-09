<?php

require_once NSL_PATH . '/includes/curl/Curl.php';
require_once NSL_PATH . '/includes/auth.php';


abstract class NextendSocialOauth2 extends NextendSocialAuth {

    const CSRF_LENGTH = 32;

    protected $state = false;

    protected $client_id;
    protected $client_secret;
    protected $redirect_uri;

    protected $endpointAuthorization;
    protected $endpointAccessToken;
    protected $endpointRestAPI;

    protected $defaultRestParams = array();

    protected $scopes = array();

    public function checkError() {
        if (isset($_GET['error']) && isset($_GET['error_description'])) {
            if ($this->validateState()) {
                throw new Exception($_GET['error'] . ': ' . htmlspecialchars_decode($_GET['error_description']));
            }
        }
    }

    public function hasAuthenticateData() {
        return isset($_REQUEST['code']);
    }

    /**
     * @param string $client_id
     */
    public function setClientId($client_id) {
        $this->client_id = $client_id;
    }

    /**
     * @param string $client_secret
     */
    public function setClientSecret($client_secret) {
        $this->client_secret = $client_secret;
    }

    /**
     * @param string $redirect_uri
     */
    public function setRedirectUri($redirect_uri) {
        $this->redirect_uri = $redirect_uri;
    }

    public function createAuthUrl() {

        $args = array(
            'response_type' => 'code',
            'client_id'     => urlencode($this->client_id),
            'redirect_uri'  => urlencode($this->redirect_uri),
            'state'         => urlencode($this->getState())
        );

        $scopes = apply_filters('nsl_' . $this->providerID . '_scopes', $this->scopes);
        if (count($scopes)) {
            $args['scope'] = urlencode($this->formatScopes($scopes));
        }

        return add_query_arg($args, $this->endpointAuthorization);
    }

    protected function formatScopes($scopes) {
        return implode(' ', $scopes);
    }

    public function authenticate() {
        if (isset($_GET['code'])) {
            if (!$this->validateState()) {
                throw  new Exception('Unable to validate CSRF state');
            }

            $curl = new NSLCurl();

            $accessTokenData = $curl->post($this->endpointAccessToken, array(
                'grant_type'    => 'authorization_code',
                'code'          => $_GET['code'],
                'redirect_uri'  => $this->redirect_uri,
                'client_id'     => $this->client_id,
                'client_secret' => $this->client_secret
            ));

            if ($curl->error) {

                $this->errorFromResponse($accessTokenData);

                throw new Exception($curl->errorCode . ': ' . $curl->errorMessage);
            }

            $accessTokenData['created'] = time();

            $this->access_token_data = $accessTokenData;

            return wp_json_encode($accessTokenData);
        }

        return false;
    }

    protected function errorFromResponse($response) {
        if (isset($response['error'])) {
            throw new Exception($response['error'] . ': ' . $response['error_description']);
        }
    }

    protected function validateState() {
        $this->state = NextendSocialLoginPersistentAnonymous::get($this->providerID . '_state');
        if ($this->state === false) {
            return false;
        }

        if (empty($_GET['state'])) {
            return false;
        }

        if ($_GET['state'] == $this->state) {
            return true;
        }

        return false;
    }

    protected function getState() {
        $this->state = NextendSocialLoginPersistentAnonymous::get($this->providerID . '_state');
        if ($this->state === false) {
            $this->state = $this->generateRandomState();

            NextendSocialLoginPersistentAnonymous::set($this->providerID . '_state', $this->state);
        }

        return $this->state;
    }

    protected function generateRandomState() {

        if (function_exists('random_bytes')) {
            return $this->bytesToString(random_bytes(self::CSRF_LENGTH));
        }

        if (class_exists('mcrypt_create_iv')) {
            /** @noinspection PhpDeprecationInspection */
            $binaryString = mcrypt_create_iv(self::CSRF_LENGTH, MCRYPT_DEV_URANDOM);

            if ($binaryString !== false) {
                return $this->bytesToString($binaryString);
            }
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            $wasCryptographicallyStrong = false;

            $binaryString = openssl_random_pseudo_bytes(self::CSRF_LENGTH, $wasCryptographicallyStrong);

            if ($binaryString !== false && $wasCryptographicallyStrong === true) {
                return $this->bytesToString($binaryString);
            }
        }

        return $this->randomStr(self::CSRF_LENGTH);
    }

    private function bytesToString($binaryString) {
        return substr(bin2hex($binaryString), 0, self::CSRF_LENGTH);
    }

    private function randomStr($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }

        return $str;
    }

    /**
     * @param       $path
     * @param array $data
     *
     * @return mixed
     * @throws Exception
     */
    public function get($path, $data = array()) {
        $curl = new NSLCurl();

        $data = array_merge($this->defaultRestParams, $data);

        $curl->setHeader('Authorization', 'Bearer ' . $this->access_token_data['access_token']);

        $result = $curl->get($this->endpointRestAPI . $path, $data);

        if ($curl->error) {
            if (isset($result['message'])) {
                throw new Exception($result['message']);
            } else {
                throw new Exception($curl->errorCode . ': ' . $curl->errorMessage);
            }
        }

        return $result;
    }
}