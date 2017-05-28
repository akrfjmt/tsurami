<?php
namespace AppBundle\Service;

/**
 * Class TwitcastingService
 * @package AppBundle\Service
 */
class TwitcastingService {

    private const APIV2_OAUTH2_AUTHORIZE    = 'https://apiv2.twitcasting.tv/oauth2/authorize';
    private const APIV2_OAUTH2_ACCESS_TOKEN = 'https://apiv2.twitcasting.tv/oauth2/access_token';
    private const APIV2_VERIFY_CREDENTIALS  = 'https://apiv2.twitcasting.tv/verify_credentials';
    private const APIV2_USERS               = 'https://apiv2.twitcasting.tv/users';

    /** @var string */
    private $clientId;
    /** @var string */
    private $clientSecret;

    /**
     * TwitcastingService constructor.
     * @param string $clientId アプリケーションのClientID
     * @param string $clientSecret アプリケーションのClientSecret
     */
    public function __construct(string $clientId, string $clientSecret) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * Get Access Token Step 1
     * @see http://apiv2-doc.twitcasting.tv/#get-access-token
     *
     * @return string
     */
    public function getGetAccessTokenStep1Uri() {
        return self::APIV2_OAUTH2_AUTHORIZE . '?' . http_build_query([
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'state' => 'csrf_token'
        ]);
    }

    /**
     * Get Access Token Step 2
     * @see http://apiv2-doc.twitcasting.tv/#get-access-token
     *
     * @param string $code
     * @param string $redirectUri
     * @return string アクセストークン
     */
    public function getAccessTokenStep2($code, string $redirectUri) {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => self::APIV2_OAUTH2_ACCESS_TOKEN,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query(
                [
                    'code' => $code,
                    'grant_type' => 'authorization_code',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'redirect_uri' => $redirectUri
                ]
            ),
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        $curlResponse = curl_exec($curl);
        curl_close($curl);

        if ($curlResponse === false) {
            throw new \RuntimeException('curl失敗');
        }

        $decodedResponse = json_decode($curlResponse, true);
        if ($decodedResponse === null) {
            throw new \RuntimeException('json_decode失敗');
        }

// 未使用なのでコメントアウト
//        $tokenType = isset($decodedResponse['token_type']) ? $decodedResponse['token_type'] : null;
//        $expiresIn = isset($decodedResponse['expires_in']) ? $decodedResponse['expires_in'] : null;
        $accessToken = isset($decodedResponse['access_token']) ? $decodedResponse['access_token'] : null;

        return $accessToken;
    }

    /**
     * Verify Credentials
     * @see http://apiv2-doc.twitcasting.tv/#verify-credentials
     *
     * @param string $token
     * @return array
     */
    public function verifyCredentials(string $token) {

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => self::APIV2_VERIFY_CREDENTIALS,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'X-Api-Version: 2.0',
                'Authorization: Bearer ' . $token
            ],
        ]);

        /** @var string|boolean $curlResponse */
        $curlResponse = curl_exec($curl);
        curl_close($curl);

        if ($curlResponse === false) {
            throw new \RuntimeException('curl失敗');
        }

        $decodedResponse = json_decode($curlResponse, true);
        if ($decodedResponse === null) {
            throw new \RuntimeException('json_decode失敗');
        }

        return $decodedResponse;
    }

    /**
     * @param string $token
     * @return string
     */
    public function verifyCredentialsAndExtractTwitcastingUserId(string $token) {
        $credentials = $this->verifyCredentials($token);
        if (!isset($credentials['user']['id'])) {
            throw new \RuntimeException('ユーザIDがありません');
        }

        if (!is_string($credentials['user']['id'])) {
            throw new \RuntimeException('ユーザIDが文字列ではありません');
        }

        return $credentials['user']['id'];
    }

    /**
     * Get User Info
     * @see http://apiv2-doc.twitcasting.tv/#get-user-info
     *
     * @param string $token
     * @param string $userId
     * @return mixed
     */
    public function getUserInfo($token, $userId) {

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => self::APIV2_USERS . '/' . $userId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'X-Api-Version: 2.0',
                'Authorization: Bearer ' . $token
            ],
        ]);

        /** @var string|boolean $curlResponse */
        $curlResponse = curl_exec($curl);
        curl_close($curl);

        if ($curlResponse === false) {
            throw new \RuntimeException('curl失敗');
        }

        $decodedResponse = json_decode($curlResponse, true);
        if ($decodedResponse === null) {
            throw new \RuntimeException('json_decode失敗');
        }

        return $decodedResponse;
    }
}
