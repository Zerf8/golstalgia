<?php

class GoogleHelper
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;

    public function __construct()
    {
        $this->clientId     = getenv('GOOGLE_CLIENT_ID') ?: '';
        $this->clientSecret = getenv('GOOGLE_CLIENT_SECRET') ?: '';
        $this->redirectUri  = getenv('GOOGLE_REDIRECT_URL') ?: '';
    }

    public function getAuthUrl(): string
    {
        $params = [
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUri,
            'response_type' => 'code',
            'scope'         => 'email profile openid',
            'access_type'   => 'offline',
            'prompt'        => 'select_account'
        ];

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    public function getAccessToken(string $code): ?string
    {
        $url = 'https://oauth2.googleapis.com/token';
        $params = [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri'  => $this->redirectUri,
            'grant_type'    => 'authorization_code',
            'code'          => $code
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
    }

    public function getUserInfo(string $accessToken): ?array
    {
        $url = 'https://www.googleapis.com/oauth2/v3/userinfo';
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?: null;
    }
}
