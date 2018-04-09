<?php
require_once NSL_PATH . '/includes/oauth2.php';

class NextendSocialProviderFacebookClient extends NextendSocialOauth2 {

    const DEFAULT_GRAPH_VERSION = 'v2.11';


    protected $access_token_data = array(
        'access_token' => '',
        'expires_in'   => -1,
        'created'      => -1
    );

    protected $scopes = array('email');

    public function __construct($providerID, $isTest) {
        parent::__construct($providerID);

        $this->endpointAuthorization = 'https://www.facebook.com/' . self::DEFAULT_GRAPH_VERSION . '/dialog/oauth';
        if ((isset($_GET['display']) && $_GET['display'] == 'popup') || $isTest) {
            $this->endpointAuthorization .= '?display=popup';
        }
        $this->endpointAccessToken = 'https://graph.facebook.com/' . self::DEFAULT_GRAPH_VERSION . '/oauth/access_token';
        $this->endpointRestAPI     = 'https://graph.facebook.com/' . self::DEFAULT_GRAPH_VERSION . '/';
    }

    protected function formatScopes($scopes) {
        return implode(',', $scopes);
    }

    public function isAccessTokenLongLived() {

        return $this->access_token_data['created'] + $this->access_token_data['expires_in'] > time() + (60 * 60 * 2);
    }

    public function requestLongLivedAccessToken() {

        $curl = new NSLCurl();

        $accessTokenData = $curl->get($this->endpointAccessToken, array(
            'grant_type'        => 'fb_exchange_token',
            'client_id'         => $this->client_id,
            'client_secret'     => $this->client_secret,
            'fb_exchange_token' => $this->access_token_data['access_token']
        ));

        if ($curl->error) {

            if (isset($accessTokenData['error'])) {
                throw new Exception($accessTokenData['error'] . ': ' . $accessTokenData['error_description']);
            } else {
                throw new Exception($curl->errorCode . ': ' . $curl->errorMessage);
            }
        }

        $accessTokenData['created'] = time();

        $this->access_token_data = $accessTokenData;

        return wp_json_encode($accessTokenData);
    }

    protected function errorFromResponse($response) {
        if (isset($response['error'])) {
            throw new Exception($response['error']['message']);
        }
    }

}