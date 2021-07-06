<?php

namespace DigitalLeapAgency\ActiveCampaign;

use Config;
use DigitalLeapAgency\ActiveCampaign\Models\ContactTag;
use DigitalLeapAgency\ActiveCampaign\Models\Tag as TagModel;
use Illuminate\Http\Request;
use Validator;

class Tag {

    // specify the version of active campaign api
    public $app_version = 3;

    /** function to add tag in active campaign account
     *  use the api url and api key from the config file
     *  validate the request input by user and if it validates, guzzle httpclient object is initialized with curl data
     *  add tag detail to migrated table once received status code 201 from active campaign
     * */
    public function addTag($tag, $contactId) {

        $validate = Validator::make($tag, [
            'tag' => 'required|max:255',
            'description' => 'required',
            // 'tagType' => 'required|in:template,contact',
        ]);

        if ($validate->fails()) {
            return $validate->errors();
        } else {

            try {
                $req = [
                    'tag' => $tag['tag'],
                    'tagType' => 'contact',
                    'description' => $tag['description'],
                ];

                $endpoint = Config::get('activecampaign.API_URL') . '/api/' . $this->app_version . '/tags';
                $client = new \GuzzleHttp\Client();

                $response = $client->request('POST', $endpoint, ['body' => json_encode(['tag' => $req]), 'headers' => [
                    'Api-Token' => Config::get('activecampaign.API_KEY'),
                ], 'verify' => false]);

                $statusCode = $response->getStatusCode();
                $content = $response->getBody();

                if ($statusCode == 201) {

                    $body = json_decode($content);

                    $data = [
                        'activeTagID' => $body->tag->id,
                        'tag' => $body->tag->tag,
                        'tagType' => $body->tag->tagType,
                        'description' => $body->tag->description,
                    ];

                    $tagid = $data['activeTagID'];

                    TagModel::create($data);

                    $endpoint = Config::get('activecampaign.API_URL') . '/api/' . $this->app_version . '/contactTags';
                    $client = new \GuzzleHttp\Client();

                    $contacttag = [
                        'contact' => $contactId,
                        'tag' => $tagid,
                    ];

                    $response = $client->request('POST', $endpoint, ['body' => json_encode(['contactTag' => $contacttag]), 'headers' => [
                        'Api-Token' => Config::get('activecampaign.API_KEY'),
                    ], 'verify' => false]);

                    $statusCode = $response->getStatusCode();
                    $content = $response->getBody();

                    if ($statusCode == 201) {

                        $body = json_decode($content);

                        $data = [
                            'activeContactTagID' => $body->contactTag->id,
                            'ContactID' => $contactId,
                            'TagID' => $body->contactTag->tag,
                        ];

                        ContactTag::create($data);

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

    /** function to update tag in active campaign account for given tag id
     *  use the api url and api key from the config file
     *  validate the request input by user and if it validates, guzzle httpclient object is initialized with curl data
     *  update tag detail to migrated table once received status code 200 from active campaign
     * */
    public function updateTag($tag, $id, $contactId) {

        $validate = Validator::make($tag, [
            'tag' => 'required|max:255',
            'description' => 'required',
            // 'tagType' => 'required|in:template,contact',
        ]);

        if ($validate->fails()) {
            return $validate->errors();
        } else {

            try {

                $req = [
                    'tag' => $tag['tag'],
                    'tagType' => 'contact',
                    'description' => $tag['description'],
                ];

                $endpoint = Config::get('activecampaign.API_URL') . '/api/' . $this->app_version . '/tags/' . $id;
                $client = new \GuzzleHttp\Client();

                $response = $client->request('PUT', $endpoint, ['body' => json_encode(['tag' => $req]), 'headers' => [
                    'Api-Token' => Config::get('activecampaign.API_KEY'),
                ], 'verify' => false]);

                $statusCode = $response->getStatusCode();
                $content = $response->getBody();

                if ($statusCode == 200) {

                    $body = json_decode($content);

                    $data = [
                        'activeTagID' => $body->tag->id,
                        'tag' => $body->tag->tag,
                        'tagType' => $body->tag->tagType,
                        'description' => $body->tag->description,
                    ];

                    if (TagModel::where('activeTagID', $body->tag->id)->whereStatus('AC')->count() > 0) {
                        TagModel::where('activeTagID', $body->tag->id)->update($data);
                    } else {
                        TagModel::create($data);

                        $tagid = $data['activeTagID'];

                        $endpoint = Config::get('activecampaign.API_URL') . '/api/' . $this->app_version . '/contactTags';
                        $client = new \GuzzleHttp\Client();

                        $contacttag = [
                            'contact' => $contactId,
                            'tag' => $tagid,
                        ];

                        $response = $client->request('POST', $endpoint, ['body' => json_encode(['contactTag' => $contacttag]), 'headers' => [
                            'Api-Token' => Config::get('activecampaign.API_KEY'),
                        ], 'verify' => false]);

                        $statusCode = $response->getStatusCode();
                        $content = $response->getBody();

                        if ($statusCode == 201) {

                            $body = json_decode($content);

                            $data = [
                                'activeContactTagID' => $body->contactTag->id,
                                'ContactID' => $contactId,
                                'TagID' => $body->contactTag->tag,
                            ];
                            ContactTag::create($data);
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

    /** function to remove tag from given contact
     *  use the api url and api key from the config file
     * */
    public function removeTag($tagid, $contactid) {

        try {

            $ct = ContactTag::where('ContactID', $contactid)->where('TagID', $tagid)->whereStatus('AC')->first();

            if (isset($ct->id)) {

                $endpoint = Config::get('activecampaign.API_URL') . '/api/' . $this->app_version . '/contactTags/' . $ct->activeContactTagID;
                $client = new \GuzzleHttp\Client();

                $response = $client->request('DELETE', $endpoint, ['headers' => [
                    'Api-Token' => Config::get('activecampaign.API_KEY'),
                ], 'verify' => false]);

                $statusCode = $response->getStatusCode();
                $content = $response->getBody();

                if ($statusCode == 200) {
                    $ct->status = 'DL';
                    $ct->save();
                }

                return $content;
            } else {
                return json_encode([
                    'status' => 404,
                    'message' => 'Not Found',
                ]);
            }

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