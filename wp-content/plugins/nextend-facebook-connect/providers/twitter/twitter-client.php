<?php
require_once NSL_PATH . '/includes/auth.php';

class NextendSocialProviderTwitterClient extends NextendSocialAuth {

    protected $consumer_key = '';

    protected $consumer_secret = '';

    protected $redirect_uri = '';

    /**
     * @var NSLTmhOAuth
     */
    private $client;

    public function __construct($providerID, $consumer_key, $consumer_secret) {
        parent::__construct($providerID);

        if (!class_exists('NSLTmhOAuth')) {
            require_once dirname(__FILE__) . '/sdk/tmhOAuth.php';
        }

        $this->consumer_key    = $consumer_key;
        $this->consumer_secret = $consumer_secret;

        $this->client = new NSLTmhOAuth(array(
            'consumer_key'    => $this->consumer_key,
            'consumer_secret' => $this->consumer_secret
        ));
    }

    public function setAccessTokenData($access_token_data) {
        parent::setAccessTokenData($access_token_data);

        $this->client->reconfigure(array(
            'consumer_key'    => $this->consumer_key,
            'consumer_secret' => $this->consumer_secret,
            'token'           => $this->access_token_data['oauth_token'],
            'secret'          => $this->access_token_data['oauth_token_secret']
        ));
    }

    /**
     * @param string $redirect_uri
     */
    public function setRedirectUri($redirect_uri) {
        $this->redirect_uri = $redirect_uri;
    }

    public function createAuthUrl() {
        $code = $this->client->request('POST', $this->client->url('oauth/request_token', ''), array(
            'oauth_callback' => $this->redirect_uri
        ));


        if ($code == 200) {
            $oauth = $this->client->extract_params($this->client->response['response']);
        } else {
            $response = json_decode($this->client->response['response'], true);
            throw new Exception($response['errors'][0]['message']);
        }

        NextendSocialLoginPersistentAnonymous::set($this->providerID . '_request_token', maybe_serialize($oauth));

        return $this->client->url("oauth/authenticate", '') . "?oauth_token=" . $oauth['oauth_token'] . "&force_login=1";
    }

    public function hasAuthenticateData() {
        return isset($_REQUEST['oauth_token']) && isset($_REQUEST['oauth_verifier']);
    }

    public function authenticate() {
        $requestToken = maybe_unserialize(NextendSocialLoginPersistentAnonymous::get($this->providerID . '_request_token'));

        $this->client->reconfigure(array(
            'consumer_key'    => $this->consumer_key,
            'consumer_secret' => $this->consumer_secret,
            'token'           => $requestToken['oauth_token'],
            'secret'          => $requestToken['oauth_token_secret']
        ));

        $code = $this->client->request('POST', $this->client->url('oauth/access_token', ''), array(
            'oauth_verifier' => $_GET['oauth_verifier']
        ));

        if ($code != 200) {
            $response = json_decode($this->client->response['response'], true);
            throw new Exception($response['errors'][0]['message']);
        }

        $user_info = $this->client->extract_params($this->client->response['response']);

        $access_token_data = wp_json_encode(array(
            'oauth_token'        => $user_info['oauth_token'],
            'oauth_token_secret' => $user_info['oauth_token_secret'],
            'user_id'            => $user_info['user_id'],
            'screen_name'        => $user_info['screen_name']
        ));

        $this->setAccessTokenData($access_token_data);

        return $access_token_data;
    }

    public function get($path, $data = array()) {


        $code = $this->client->request('GET', $this->client->url('1.1/' . $path), array(
            'user_id' => $this->access_token_data['user_id']
        ));

        if ($code != 200) {
            $response = json_decode($this->client->response['response'], true);
            throw new Exception($response['errors'][0]['message']);
        }


        return json_decode($this->client->response['response'], true);
    }

}