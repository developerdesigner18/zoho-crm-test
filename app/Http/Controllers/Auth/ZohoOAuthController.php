<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ZohoOAuthController extends Controller
{
    public function redirectToZoho()
    {
        $clientId = '1000.V9HFIY15Z64PIG29UJJQ4CK9108IAG';
        $redirectUri = 'http://127.0.0.1:8000/oauth/callback';
        $state = uniqid();

        $authorizationUrl = "https://accounts.zoho.com/oauth/v2/auth?response_type=code&client_id={$clientId}&scope=ZohoCRM.modules.all&redirect_uri={$redirectUri}&state={$state}";

        return redirect($authorizationUrl);
    }

    public function handleCallback(Request $request)
    {
        $clientId = '1000.V9HFIY15Z64PIG29UJJQ4CK9108IAG';
        $clientSecret = '8c406a560df2d16843beea4ced971bf046298b792c';
        $redirectUri = 'http://127.0.0.1:8000/oauth/callback';

        $code = $request->input('code');

        $client = new Client();

        $response = $client->request('POST', 'https://accounts.zoho.com/oauth/v2/token', [
            'form_params' => [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code'
            ]
        ]);

        $accessToken = json_decode($response->getBody(), true)['access_token'];
        $refreshToken = json_decode($response->getBody(), true)['refresh_token'];

        // Store the tokens securely in your preferred storage method
        // For example, you can store them in the session
    }
}
