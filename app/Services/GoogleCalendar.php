<?php

namespace App\Services;

use App\Models\GoogleToken;
use Google_Service_Calendar_Event;
use Google_Service_Calendar;
use Google_Client;
use Google_Service_Oauth2;
use Carbon\Carbon;
use Google_Service_Exception;


class GoogleCalendar
{
    //this is the function that redirect the user to the google login page
    public static function getLoginUrl()
    {
        $client = new self;
        //attach csrf token to the url; this will be returned back to us from google`s request
        $authUrl = $client->getClient()->createAuthUrl(null, ['state' => csrf_token()]);
        return $authUrl;
    }

    public function fetchCustomerToken($code)
    {
        $googleToken = GoogleToken::where('user_id', auth()->user()->id)->first();
        $accessToken = $this->getClient()->fetchAccessTokenWithAuthCode($code);

        //to get the google email to save in db , first i need to initialize a client and the oauth2 service
        $client = $this->getClient();
        $client->setAccessToken($accessToken['access_token']);
        $userInfoService = new Google_Service_Oauth2($client);
        $userInfo = $userInfoService->userinfo->get();
        if (!$googleToken) 
        {
            //2. store token
            return \App\Models\GoogleToken::create([
                'access_token' => $accessToken['access_token'],
                'refresh_token' => $accessToken['refresh_token'],
                'user_id' => auth()->user()->id,
                'email' => $userInfo->getEmail(),
            ]);
        }
        $googleToken->update([
            'access_token' => $accessToken['access_token'],
        ]);

        return $googleToken;
    }

    //returns an authorized API client using the google-api.php config file
    public function getClient()
    {
        $client = new Google_Client();
        $client->setAuthConfig(config('google-api'));
        $client->addScope(Google_Service_Calendar::CALENDAR);
        $client->addScope('https://www.googleapis.com/auth/userinfo.email');
        $client->setAccessType('offline');
        return $client;
    }

    //checks if the accessToken is valid and if not, it refreshes it
    public function oauth()
    {
        $client = $this->getClient();
        $googleTokens = GoogleToken::where('user_id', auth()->user()->id)->first();
        $client->setAccessToken($googleTokens->access_token);
        //if the access token is expired, refresh it
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($googleTokens->refresh_token);
            $googleTokens->access_token = $client->getAccessToken()['access_token'];
            //save the new access token in db 
            $googleTokens->save();
        }

        return $client;
    }

    public function initializeGoogleService()
    {
        $client = $this->oauth();
        $service = new Google_Service_Calendar($client);
        return $service;
    }

    //this function maps the ARC calendar event fields to the google calendar event fields
    public function mapLocalToGoogleEvent(array $data): Google_Service_Calendar_Event
    {
        // Check if it's an all-day event
        // If it's an all-day event, the dates will be in the format YYYY-MM-DD
        // If it's not an normal event, the dates will be in the format YYYY-MM-DDTHH:MM:SS
        if ($data['all_day'] == true) {
            $all_dayStart = Carbon::parse($data['starts_at'], 'Europe/Bucharest')->format('Y-m-d');
            $all_dayEnd = Carbon::parse($data['ends_at'], 'Europe/Bucharest')->addDay()->format('Y-m-d');

        } else {
            // Normal event dates
            $normal_eventStart = Carbon::parse($data['starts_at'], 'Europe/Bucharest');
            $normal_eventEnd = Carbon::parse($data['ends_at'], 'Europe/Bucharest');
        }

        // Format the rrule recurrence string
        $recurrance_freq = $data['is_recurrent'] ? "RRULE:FREQ=" . strtoupper($data['recurrence']['recurrence']) : null;

        // Creating the Google event using the event schema from Google documentation
        $googleEvent = new Google_Service_Calendar_Event(array(
            'summary' => $data['title'],
            'location' => $data['location'],
            'description' => $data['description'],
            'start' => array(
                'date' => $all_dayStart ?? null,
                'dateTime' => $normal_eventStart ?? null,
                'timeZone' => 'Europe/Bucharest',
            ),
            'end' => array(
                'date' => $all_dayEnd ?? null,
                'dateTime' => $normal_eventEnd ?? null,
                'timeZone' => 'Europe/Bucharest',
            ),
            'recurrence' => $recurrance_freq ? [$recurrance_freq] : null,
            'reminders' => array(
                'useDefault' => false,
                'overrides' => array(
                    array('method' => 'email', 'minutes' => 24 * 60),
                    array('method' => 'popup', 'minutes' => 10),
                ),
            ),
        ));

        return $googleEvent;
    }

    public function addEventToGoogleCalendar(array $data)
    {
        $service = $this->initializeGoogleService();
        $googleEvent = $this->mapLocalToGoogleEvent($data);
        $createdEvent = $service->events->insert('primary', $googleEvent);
        $createdEventID = $createdEvent->getId();
        return $createdEventID;
    }

    public function deleteEventFromGoolgeCalendar(string $eventID)
    {
        try{
            $service = $this->initializeGoogleService();
            $service->events->delete('primary', $eventID);
        } catch (Google_Service_Exception $e) {
            //ADD MESSAGE IF THE EVENT IS ALREADY DELETED FROM GOOGLE CALENDAR
        }
    }

    public function updateEventInGoolgeCalendar(array $data)
    {
        try{
            $service = $this->initializeGoogleService();
            $googleEvent = $this->mapLocalToGoogleEvent($data);
            //recreate the event into google calendar
            //if the event is not synced with google calendar, create it and return the event_id
            if($data['google_calendar_id'] === null)
            {
                $createdEvent = $service->events->insert('primary', $googleEvent);
                return $createdEvent->getId();
            }
            //if is synced, only update it
            $service->events->update('primary', $data['google_calendar_id'], $googleEvent);
        } catch (Google_Service_Exception $e) {
            //ADD MESSAGE IF THE EVENT IS ALREADY DELETED FROM GOOGLE CALENDAR
        }
    }

    //when revoking an access token, it disables it, so will be not usable anymore
    //the token will be deleted from database
    public function disconnect()
    {
        $client = $this->getClient();
        $accessToken = GoogleToken::where('user_id', auth()->user()->id)->first()->access_token;
        $client->revokeToken($accessToken);
        GoogleToken::where('user_id', auth()->user()->id)->delete();
    }
}
