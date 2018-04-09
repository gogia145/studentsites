<?php
require_once NSL_PATH . '/includes/oauth2.php';

class NextendSocialProviderGoogleClient extends NextendSocialOauth2 {

    protected $access_token_data = array(
        'access_token' => '',
        'expires_in'   => -1,
        'created'      => -1
    );

    private $accessType = 'offline';
    private $approvalPrompt = 'force';

    protected $scopes = array(
        'https://www.googleapis.com/auth/userinfo.profile',
        'https://www.googleapis.com/auth/userinfo.email'
    );

    protected $endpointAuthorization = 'https://accounts.google.com/o/oauth2/auth';

    protected $endpointAccessToken = 'https://accounts.google.com/o/oauth2/token';

    protected $endpointRestAPI = 'https://www.googleapis.com/oauth2/v1/';

    protected $defaultRestParams = array(
        'alt' => 'json'
    );

    /**
     * @param string $access_token_data
     */
    public function setAccessTokenData($access_token_data) {
        $this->access_token_data = json_decode($access_token_data, true);
    }


    public function createAuthUrl() {
        return add_query_arg(array(
            'access_type'     => urlencode($this->accessType),
            'approval_prompt' => urlencode($this->approvalPrompt)
        ), parent::createAuthUrl());
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

    /**
     * @param string $approvalPrompt
     */
    public function setApprovalPrompt($approvalPrompt) {
        $this->approvalPrompt = $approvalPrompt;
    }

    protected function errorFromResponse($response) {
        if (isset($response['error'])) {
            throw new Exception($response['error']);
        }
    }

}