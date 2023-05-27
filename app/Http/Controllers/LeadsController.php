<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Mail\ProspectCreatedEmail;

class LeadsController extends Controller
{
    public function getRecentProspects()
    {

        $client = new Client();

        $url = 'https://accounts.zoho.in/oauth/v2/token';

        $response = $client->post($url, [
            'verify' => false,
            'form_params' => [
                'client_id' => '1000.6K68377R2FGKZO0NKR2J7EH0EQWVDA',
                'client_secret' => '8bffa5fcc9c076b77107acceea0ae6c82690407b96',
                'refresh_token' => '1000.1ef31d415528d0d3d93772e3949c07fa.2d09e0e1444a7a19f534204071247743',
                'grant_type' => 'refresh_token',
            ],
        ]);

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        $accessToken = $data['access_token'];

        $client = new Client();

        $response = $client->request('GET', 'https://www.zohoapis.in/crm/v2/leads', [
            'verify' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ],
            'query' => [
                'sort_by' => 'Modified_Time',
                'sort_order' => 'desc',
                'page' => '1',
                'per_page' => '5'
            ]
        ]);

        // echo "<pre>";
        // return $response->getBody();
        $prospects = json_decode($response->getBody(), true)['data'];

        return response()->json($prospects);
    }


    public function storeProspect(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'First_Name' => 'required',
            'Last_Name' => 'required',
            'Mobile' => 'required|regex:/^04\d{2} \d{3} \d{3}$/',
            'Email' => 'required|email',
            'DOB' => 'required|date_format:Y-m-d',
            'Tax_File_Number' => 'required|digits:9|regex:/^\d{9}$/',
            'Agreed_Terms' => 'required|in:Yes,No',
            'Status' => 'required|in:Ready For Search,New Prospect',
        ]);


        $client = new Client();

        $url = 'https://accounts.zoho.in/oauth/v2/token';

        $response = $client->post($url, [
            'verify' => false,
            'form_params' => [
                'client_id' => '1000.6K68377R2FGKZO0NKR2J7EH0EQWVDA',
                'client_secret' => '8bffa5fcc9c076b77107acceea0ae6c82690407b96',
                'refresh_token' => '1000.1ef31d415528d0d3d93772e3949c07fa.2d09e0e1444a7a19f534204071247743',
                'grant_type' => 'refresh_token',
            ],
        ]);

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        $accessToken = $data['access_token'];

        $response = \Http::withOptions(['verify' => false])->
        withHeaders([
            'Authorization' => 'Bearer '.$accessToken, // Replace with your access token
            'Content-Type' => 'application/json',
        ])->post('https://www.zohoapis.in/crm/v2/Leads', [
            'data' => [$request->all()],
        ]);

        if ($response->successful()) {


            // Prospect created successfully


            $prospectData = $request->all();
            $prospectId = $response['data'][0]['details']['id'];
            $prospectName = $request->First_Name;
            $prospectEmail = $request->Email;
        
            // Send email to IT department
            \Mail::to('it@truewealth.com.au')->send(new ProspectCreatedEmail($prospectId, $prospectName, $prospectEmail));

            return $response->json();
            // Process the response as needed
            // ...
        } else {
            // Failed to create the prospect
            return $response->json();
        }
    
    }
}
