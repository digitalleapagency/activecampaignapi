<?php

namespace DigitalLeapAgency\ActiveCampaign;

use Config;
use DigitalLeapAgency\ActiveCampaign\Models\Contact as ContactModel;
use Illuminate\Http\Request;
use Validator;

class Contact {

    // specify the version of active campaign api
    public $app_version = 3;

    /** function to add contact in active campaign account
     *  use the api url and api key from the config file
     *  validate the request input by user and if it validates, guzzle httpclient object is initialized with curl data
     *  add contact detail to migrated table once received status code 201 from active campaign
     * */
    public function addContact($contact) {

        $validate = Validator::make($contact, [
            'firstName' => 'required|max:255',
            'lastName' => 'required|max:255',
            'email' => 'required|email|unique:active_contacts|max:255',
            'phone' => 'required|digits:10',
        ]);

        if ($validate->fails()) {
            return $validate->errors();
        } else {

            try {

                $endpoint = Config::get('activecampaign.API_URL') . '/api/' . $this->app_version . '/contacts';
                $client = new \GuzzleHttp\Client();

                $response = $client->request('POST', $endpoint, ['body' => json_encode(['contact' => $contact]), 'headers' => [
                    'Api-Token' => Config::get('activecampaign.API_KEY'),
                ], 'verify' => false]);

                $statusCode = $response->getStatusCode();
                $content = $response->getBody();

                if ($statusCode == 201) {

                    $body = json_decode($content);

                    $data = [
                        'activeContactID' => $body->contact->id,
                        'email' => $body->contact->email,
                        'firstName' => $body->contact->firstName,
                        'lastName' => $body->contact->lastName,
                        'phone_number' => $body->contact->phone,
                    ];

                    ContactModel::create($data);
                }

                return $content;

            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $response = $e->getResponse();

                return json_encode([
                    'status' => $response->getStatusCode(),
                    'error' => $response->getReasonPhrase(),
                ]);
            } catch (\Exception $e) {
                return $e;
            }
        }
    }

    /** function to update contact in active campaign account for given contact id
     *  use the api url and api key from the config file
     *  validate the request input by user and if it validates, guzzle httpclient object is initialized with curl data
     *  update contact detail to migrated table once received status code 200 from active campaign
     * */
    public function updateContact($contact, $id) {

        $validate = Validator::make($contact, [
            'firstName' => 'required|max:255',
            'lastName' => 'required|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|digits:10',
        ]);

        if ($validate->fails()) {
            return $validate->errors();
        } else {

            try {

                $endpoint = Config::get('activecampaign.API_URL') . '/api/' . $this->app_version . '/contacts/' . $id;
                $client = new \GuzzleHttp\Client();

                $response = $client->request('PUT', $endpoint, ['body' => json_encode(['contact' => $contact]), 'headers' => [
                    'Api-Token' => Config::get('activecampaign.API_KEY'),
                ], 'verify' => false]);

                $statusCode = $response->getStatusCode();
                $content = $response->getBody();

                if ($statusCode == 200) {

                    $body = json_decode($content);

                    $data = [
                        'activeContactID' => $body->contact->id,
                        'email' => $body->contact->email,
                        'firstName' => $body->contact->firstName,
                        'lastName' => $body->contact->lastName,
                        'phone_number' => $body->contact->phone,
                    ];

                    if (ContactModel::whereEmail($body->contact->email)->whereStatus('AC')->count() > 0) {
                        ContactModel::whereEmail($body->contact->email)->update($data);
                    } else {
                        ContactModel::create($data);
                    }

                }

                return $content;

            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $response = $e->getResponse();

                return json_encode([
                    'status' => $response->getStatusCode(),
                    'error' => $response->getReasonPhrase(),
                ]);
            } catch (\Exception $e) {
                return $e;
            }
        }
    }

    /** function to list contacts from active campaign account
     *  use the api url and api key from the config file
     *  specify the requested query parameters to filter out the result
     * */
    public function getContacts(Request $request) {

        try {

            $endpoint = Config::get('activecampaign.API_URL') . '/api/' . $this->app_version . '/contacts';
            $client = new \GuzzleHttp\Client();

            /*$query = [
            'status' => isset($request->status) ? $request->status : '-1',
            ];

            if (isset($request->ids)) {
            $query['ids[]'] = $request->ids;
            }

            if (isset($request->email)) {
            $query['email'] = $request->email;
            }

            if (isset($request->email_like)) {
            $query['email_like'] = $request->email_like;
            }

            if (isset($request->keyword)) {
            $query['search'] = $request->keyword;
            }

            if (isset($request->limit)) {
            $query['limit'] = $request->limit;
            }

            if (isset($request->offset)) {
            $query['offset'] = $request->offset;
            }

            if (isset($request->orderby)) {
            foreach ($request->orderby as $key => $dir) {
            switch ($key) {
            case 'email':
            $query['orders[email]'] = (isset($dir) && in_array($dir, ['ASC', 'DESC'])) ? $dir : 'ASC';
            break;
            case 'cdate':
            $query['orders[cdate]'] = (isset($dir) && in_array($dir, ['ASC', 'DESC'])) ? $dir : 'ASC';
            break;
            case 'first_name':
            $query['orders[first_name]'] = (isset($dir) && in_array($dir, ['ASC', 'DESC'])) ? $dir : 'ASC';
            break;
            case 'last_name':
            $query['orders[last_name]'] = (isset($dir) && in_array($dir, ['ASC', 'DESC'])) ? $dir : 'ASC';
            break;
            case 'name':
            $query['orders[name]'] = (isset($dir) && in_array($dir, ['ASC', 'DESC'])) ? $dir : 'ASC';
            break;
            }
            }
            }*/

            $response = $client->request('GET', $endpoint, ['query' => $request->all(), 'headers' => [
                'Api-Token' => Config::get('activecampaign.API_KEY'),
            ], 'verify' => false]);

            $statusCode = $response->getStatusCode();
            $content = $response->getBody();

            return $content;

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();

            return json_encode([
                'status' => $response->getStatusCode(),
                'error' => $response->getReasonPhrase(),
            ]);
        } catch (\Exception $e) {
            return $e;
        }

    }
}

?>