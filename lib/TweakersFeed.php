<?php
/**
 * Created by PhpStorm.
 * User: Nav
 * Date: 1-3-2015
 * Time: 10:34
 */
namespace lib;
use Abraham\TwitterOAuth\TwitterOAuth;
use GuzzleHttp\Client;


/**
 * Class TweakersFeed
 * @package lib
 */
class TweakersFeed {

    /**
     * Tweakers news from Feedburner and passes it to Twitter.
     */
    function __construct(){

        // Tweaker feed, fetched from feedburner
        $tweakersFeed = $this->GetTweakerFeed();

        // Create a goo.gl shorter link to fit in twitter's 140 char limit
        $shorterLink = $this->GoogleUrlShortener($tweakersFeed->link);

        // Goo.gl url shortener gives a link of 20 chars, -2 for the seperator = 118 chars left to fill.
        $twitterMessage = mb_strimwidth($tweakersFeed->title, 0, 118, "...") .' -'. $shorterLink->id;

        // Finally tweet that using Twitter Oauth
        return $this->TweetThis($twitterMessage);
    }

    /*
     * Reduce length by using
     * goo.gl url shortener
     */
    function GoogleUrlShortener($longUrl) {
        $googleApiClient = new Client();
        $response = $googleApiClient->post(GOOGLE_URL_SHORTENER, [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => json_encode(['longUrl'=>$longUrl]),
            'verify'  => false,
        ]);

        return $response->json(['object'=>true]);
    }

    function GetTweakerFeed() {
        $client = new Client();
        $response = $client->get(TWEAKERS_FEED)->json(['object'=>true]);
        $entries = $response->responseData->feed->entries;
        $entryId = mt_rand(0, (count($entries)-1));

        return $entries[$entryId];
    }

    function TweetThis($tweet) {
        $twitter = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
        $result = $twitter->post("statuses/update", ["status" => $tweet]);

        return $result;
    }
}