<?php

namespace App\Lib\SignIn;

use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class SignInWithApple
{
    /**
     * The client ID.
     *
     * @var string
     */
    protected $clientId;

    /**
     * The key ID.
     *
     * @var string
     */
    protected $keyId;

    /**
     * The team ID.
     *
     * @var string
     */
    protected $teamId;

    /**
     * The client secret.
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * The redirect URL.
     *
     * @var string
     */
    protected $redirectUrl;

    /**
     * The request.
     *
     * @var object
     */
    protected $request;


    public function __construct($request = [])
    {
        $this->request = $request;
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $social = !empty($settings['social']) ? $settings['social'] : null;

        if (!empty($social['appleid_client_id'])) {
            $this->clientId = $social['appleid_client_id'];
        }

        if (!empty($social['appleid_secret'])) {
            $this->clientSecret = $social['appleid_secret'];
        }

        $this->redirectUrl = !empty($social['appleid_redirect_uri']) ? $social['appleid_redirect_uri'] :  $request->scheme() . '://' . $request->host() . '/member/oauth/apple';
    }

    protected function getCode()
    {
        return $this->request->getData('code');
    }

    /**
     * @param string $der
     * @param int    $partLength
     *
     * @return string
     */
    public function fromDER($der, $partLength)
    {
        $hex = unpack('H*', $der)[1];
        if ('30' !== mb_substr($hex, 0, 2, '8bit')) { // SEQUENCE
            throw new \RuntimeException();
        }
        if ('81' === mb_substr($hex, 2, 2, '8bit')) { // LENGTH > 128
            $hex = mb_substr($hex, 6, null, '8bit');
        } else {
            $hex = mb_substr($hex, 4, null, '8bit');
        }
        if ('02' !== mb_substr($hex, 0, 2, '8bit')) { // INTEGER
            throw new \RuntimeException();
        }
        $Rl = hexdec(mb_substr($hex, 2, 2, '8bit'));
        $R = $this->retrievePositiveInteger(mb_substr($hex, 4, $Rl * 2, '8bit'));
        $R = str_pad($R, $partLength, '0', STR_PAD_LEFT);
        $hex = mb_substr($hex, 4 + $Rl * 2, null, '8bit');
        if ('02' !== mb_substr($hex, 0, 2, '8bit')) { // INTEGER
            throw new \RuntimeException();
        }
        $Sl = hexdec(mb_substr($hex, 2, 2, '8bit'));
        $S = $this->retrievePositiveInteger(mb_substr($hex, 4, $Sl * 2, '8bit'));
        $S = str_pad($S, $partLength, '0', STR_PAD_LEFT);
        return pack('H*', $R.$S);
    }

    /**
     * @param string $data
     *
     * @return string
     */
    public function retrievePositiveInteger($data)
    {
        while ('00' === mb_substr($data, 0, 2, '8bit') && mb_substr($data, 2, 2, '8bit') > '7f') {
            $data = mb_substr($data, 2, null, '8bit');
        }
        return $data;
    }

    public function encode($data)
    {
        $encoded = strtr(base64_encode($data), '+/', '-_');
        return rtrim($encoded, '=');
    }

    public function generateJWT($kid, $iss, $sub)
    {
        $header = [
            'alg' => 'ES256',
            'kid' => $kid
        ];
        $body = [
            'iss' => $iss,
            'iat' => time(),
            'exp' => time() + 86400 * 150, // must not be greater than 15777000 (6 months in seconds)
            'aud' => 'https://appleid.apple.com',
            'sub' => $sub
        ];

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $social = !empty($settings['social']) ? $settings['social'] : [];
        $contentFileAuthKey = !empty($social['appleid_authkey']) ? $social['appleid_authkey'] : '';

        // $pathFileAuthKeyP8 = "app/pathFile.p8";
        // $contentFileAuthKey = File::get(storage_path($pathFileAuthKeyP8));
        $privKey = openssl_pkey_get_private($contentFileAuthKey);       

        if (!$privKey){
           return false;
        }

        $payload = $this->encode(json_encode($header)) . '.' . $this->encode(json_encode($body));
        $signature = '';
        $success = openssl_sign($payload, $signature, $privKey, OPENSSL_ALGO_SHA256);

        debug($success); die;

        if (!$success) return false;

        $raw_signature = $this->fromDER($signature, 64);
        
        return $payload . '.' . $this->encode($raw_signature);
    }
    
    public function getClientSecret()
    {
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $social = !empty($settings['social']) ? $settings['social'] : [];
        $keyId = '8W7KZX52FR';
        $teamId = 'NBW9AK8M69';
        $clientId = 'com.nguonbds.xorg.services';
        $clientSecret = $this->generateJWT($keyId, $teamId, $clientId);

        return $clientSecret;
    }

    protected function getUserByToken($token)
    {
        $claims = explode('.', $token)[1];

        return json_decode(base64_decode($claims), true);
    }

    protected function mapUserToObject(array $user)
    {
        $params = $this->request->getData();
        $userRequest = !empty($params['user']) ? json_decode($params['user'], true) : [];

        $first_name = !empty($user['name']['firstName']) ? $user['name']['firstName'] : '';
        $mid_last_name = !empty($user['name']['lastName']) ? $user['name']['lastName'] : '';
        $fullName = trim($first_name . " " . $mid_last_name);


        if (!empty($params['user'])) {
            $userRequest = json_decode($params['user'], true);

            if (array_key_exists("name", $userRequest)) {
                $first_name = !empty($userRequest['name']['firstName']) ? $userRequest['name']['firstName'] : '';
                $mid_last_name = !empty($userRequest['name']['lastName']) ? $userRequest['name']['lastName'] : '';
                $fullName = trim($first_name . " " . $mid_last_name);

                $user["first_name"] = $first_name;
                $user["mid_last_name"] = $mid_last_name;
                $user["full_name"] = $fullName;
            }
        }


        return $user;
    }

    protected function getAccessTokenResponse($code) {

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://appleid.apple.com/auth/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUrl,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret
            ])
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $responseData = json_decode($response, true);

        return $responseData;
    }

    public function getAuthUrl($params) {

        $query = [
            'client_id' => $this->clientId,
            'response_type' => !empty($params['response_type']) ? $params['response_type'] : 'code id_token',
            'scope' => !empty($params['scope']) ? $params['scope'] : 'name email',
            'redirect_uri' => $params['redirect_uri'],
            'state' => !empty($params['state']) ? $params['state'] : '',
            'nonce' => !empty($params['nonce']) ? $params['nonce'] : '',
            'response_mode' => 'form_post'
        ];

        $redirect = 'https://appleid.apple.com/auth/authorize?' . http_build_query($query);

        return $redirect;
    }

    public function user()
    {
        $params = $this->request->getData();

        $code = !empty($params['code']) ? $params['code'] : '';
        $id_token = !empty($response['id_token']) ? $response['id_token'] : '';        

        $response = $this->getAccessTokenResponse($code);


        if (empty($response['access_token']) || empty($response['id_token'])) {
            return false;
        }

        $user = $this->mapUserToObject($this->getUserByToken(
            $response['id_token']
        ));

        $user['type'] = 'apple';
        $user['social_id'] = !empty($user['sub']) ? $user['sub'] : null;

        return $user;
    }
}

?>