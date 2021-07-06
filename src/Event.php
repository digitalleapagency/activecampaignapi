<?php

namespace DigitalLeapAgency\ActiveCampaign;

use Config;
use DigitalLeapAgency\ActiveCampaign\Models\ContactEvent;
use DigitalLeapAgency\ActiveCampaign\Models\Event as EventModel;
use Illuminate\Http\Request;
use Validator;

class Event {

    // specify the version of active campaign api
    public $app_version = 3;

    /** function to add event in active campaign account
     *  use the api url and api key from the config file
     *  validate the request input by user and if it validates, guzzle httpclient object is initialized with curl data
     *  add event detail to migrated table once received status code 201 from active campaign
     * */
    public function addEvent($event) {

        $validate = Validator::make($event, [
            'event_name' => 'required|max:255',
        ]);

        if ($validate->fails()) {
            return $validate->errors();
        } else {

            try {
                $req = [
                    'name' => $event['event_name'],
                ];

                $endpoint = Config::get('activecampaign.API_URL') . '/api/' . $this->app_version . '/eventTrackingEvents';
                $client = new \GuzzleHttp\Client();

                $response = $client->request('POST', $endpoint, ['body' => json_encode(['eventTrackingEvent' => $req]), 'headers' => [
                    'Api-Token' => Config::get('activecampaign.API_KEY'),
                ], 'verify' => false]);

                $statusCode = $response->getStatusCode();
                $content = $response->getBody();

                if ($statusCode == 201) {

                    $body = json_decode($content);

                    $data = [
                        'event' => $body->eventTrackingEvent->name,
                    ];

                    EventModel::create($data);

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

    /** function to track event in active campaign account for given contact
     *  use the api url and api key from the config file
     *  validate the request input by user and if it validates, guzzle httpclient object is initialized with curl data
     *  update event detail to migrated table once received status code 200 from active campaign
     * */
    public function trackEvent($event) {

        $validate = Validator::make($event, [
            'event' => 'required|max:255',
            'eventdata' => 'required',
            'email' => 'required|email',
        ]);

        if ($validate->fails()) {
            return $validate->errors();
        } else {

            try {

                // if event is tracked for a contact, then fetch contact id from active campaign from email address and save the event details in migrated table
                $endpoint = Config::get('activecampaign.API_URL') . '/api/' . $this->app_version . '/contacts';
                $client = new \GuzzleHttp\Client();

                $response = $client->request('GET', $endpoint, ['query' => ['email' => $event['email']], 'headers' => [
                    'Api-Token' => Config::get('activecampaign.API_KEY'),
                ], 'verify' => false]);

                $statusCode = $response->getStatusCode();
                $content = $response->getBody();

                if ($statusCode == 200) {
                    $res = json_decode($content);

                    if (isset($res->contacts) && count($res->contacts) > 0) {
                        $req = [
                            'event' => $event['event'],
                            'actid' => Config::get('activecampaign.ACTID'),
                            'key' => Config::get('activecampaign.EVENT_KEY'),
                            'eventdata' => $event['eventdata'],
                            'visit' => json_encode(['email' => $event['email']]),
                        ];

                        $endpoint = 'https://trackcmp.net/event';
                        $client = new \GuzzleHttp\Client();

                        $response = $client->request('POST', $endpoint, ['body' => http_build_query($req), 'headers' => [
                            'Content-Type' => 'application/x-www-form-urlencoded',
                        ], 'verify' => false]);

                        $statusCode = $response->getStatusCode();
                        $content = $response->getBody();

                        if ($statusCode == 200) {

                            $body = json_decode($content);

                            $data = [
                                'event' => $req['event'],
                                'eventdata' => $req['eventdata'],
                                'ContactID' => $res->contacts[0]->id,
                            ];
                            ContactEvent::create($data);
                        }
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
}

?>