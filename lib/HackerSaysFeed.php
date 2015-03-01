<?php
/**
 * Created by PhpStorm.
 * User: Nav
 * Date: 1-3-2015
 * Time: 11:29
 */

namespace lib;
use Abraham\TwitterOAuth\TwitterOAuth;
use Guzzle\Http;
use GuzzleHttp\Client;

/**
 * Class HackerSaysFeed
 * @package lib
 */
class HackerSaysFeed {

    /**
     * Yahoo Query String for hackersays.com quotes
     * @var string
     */
    protected $yql_query = "https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20html%20where%20url%3D%22http%3A%2F%2Fhackersays.com%2F%22%20and%0A%20%20%20%20%20%20xpath%3D'%2F%2F*%5B%40id%3D%22quotes%22%5D%2Fli%2Fblockquote'&format=json";

    /**
     * Holds all quotes in a object
     * @var
     */
    public $quotes;

    /**
     * Holds the Guzzle Client for request
     * @var Client
     */
    protected $client;

    /**
     * Constructor
     * Makes a guzzle request and
     * scrapes the content from HackerSays.com
     * using Yahoo's Query Language.
     */
    function __construct()
    {
        $this->client = new Client();
        $this->quotes = $this->FetchHackerSaysQuotes();

        return $this->TweetThis($this->quotes[mt_rand(0,10)]->p->span);
    }

    /**
     * TODO: Make this better readable!
     */
    function FetchHackerSaysQuotes() {
        return $this->client->get($this->yql_query, ['verify'=>false])->json(['object'=>true])->query->results->blockquote;
    }


    /**
     * TODO: Make this class abstract for diffrent feeds
     */
    function TweetThis($tweet) {
        $twitter = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
        $result = $twitter->post("statuses/update", ["status" => $tweet]);

        return $result;
    }
}
