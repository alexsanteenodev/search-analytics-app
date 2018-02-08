<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use HannesKirsman\GoogleSearchConsole\SearchConsoleApi;
use Illuminate\Http\Request;
use SchulzeFelix\SearchConsole\Period;
use SchulzeFelix\SearchConsole\SearchConsole;
use SchulzeFelix\SearchConsole\SearchConsoleClient;

class WebmasterController extends Controller
{

    public $analytics;
    public $webmaster;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        $redirect_uri = "http://".$_SERVER["HTTP_HOST"].'/webmaster';
//        $client = new \Google_Client();
////        $client->setAuthConfig(storage_path('/SeoSearchProject-a000fd0129c9.json'));
//        $client->setRedirectUri($redirect_uri);
////        $client->setScopes('email');
////        /************************************************
////         * If we're logging out we just need to clear our
////         * local access token in this case
////         ************************************************/
////        if (isset($_REQUEST['logout'])) {
////            unset($_SESSION['id_token_token']);
////        }
////        /************************************************
////         * If we have a code back from the OAuth 2.0 flow,
////         * we need to exchange that with the
////         * Google_Client::fetchAccessTokenWithAuthCode()
////         * function. We store the resultant access token
////         * bundle in the session, and redirect to ourself.
////         ************************************************/
////        if (isset($_GET['code'])) {
////            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
////            $client->setAccessToken($token);
////            // store in the session also
////            $_SESSION['id_token_token'] = $token;
////            // redirect back to the example
////            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
////        }
////        /************************************************
////        If we have an access token, we can make
////        requests, else we generate an authentication URL.
////         ************************************************/
////        if (
////            !empty($_SESSION['id_token_token'])
////            && isset($_SESSION['id_token_token']['id_token'])
////        ) {
////            $client->setAccessToken($_SESSION['id_token_token']);
////        } else {
////            $authUrl = $client->createAuthUrl();
////        }
//
//        putenv('GOOGLE_APPLICATION_CREDENTIALS='.storage_path('/SeoSearchProject-a000fd0129c9.json'));
//        $client->useApplicationDefaultCredentials();
//        $client->setAccessType("offline");        // offline access
//        $client->setIncludeGrantedScopes(true);   // incremental auth
//        $client->setClientId(env('GOOGLE_CLIENT_ID'));
//        $client->setClientSecret( env('GOOGLE_CLIENT_SECRET'));
////        $client->setDeveloperKey('INSERT HERE'); // API key
//        $client->addScope('https://www.googleapis.com/auth/webmasters');
//        $client->addScope('https://www.googleapis.com/auth/webmasters.readonly');
//
//        $client->setSubject('seosearch@seosearchproject.iam.gserviceaccount.com');
//
//
//        if (isset($_SESSION['token'])) { // extract token from session and configure client
//            $token = $_SESSION['token'];
//            $client->setAccessToken($token);
//        }
//
//        if (!$client->getAccessToken()) { // auth call to google
//            $authUrl = $client->createAuthUrl();
//            header("Location: ".$authUrl);
//            die;
//        }
//         $client->setAccessToken($token); // save this to a file

//        $client->setScopes('email');
//
        $client = new \Google_Client();
        $client->setApplicationName("Google Webmasters Hello");
        $client->setAuthConfig(storage_path('/SeoSearchProject-a000fd0129c9.json'));
//        $client->setAuthConfig(storage_path('/client_secret_568739715870-8kbflcvjqjdk0ikf2vjaifht1r71rg7a.apps.googleusercontent.com.json'));

        $client->addScope('https://www.googleapis.com/auth/webmasters');
        $client->addScope('https://www.googleapis.com/auth/webmasters.readonly');
        $searchclient = new SearchConsoleClient($client);
//
        $search = new SearchConsole($searchclient);
//
//        $search_console = new \Google_Service_Webmasters($client);
//        $site_list = $search_console->sites->listSites()->siteEntry;
        $sites = $search->listSites();
//
//        var_dump($site_list);
//        var_dump($sites);
//        $data = $search
//            ->searchAnalyticsQuery(
//                'https://www.skydecor.com.ua/',
//                Period::create(Carbon::now()->subDays(10), Carbon::now()->subDays(9)),
//                array(),
//                [['dimension' => 'query', 'operator' => 'сontains', 'expression' => 'MOBILE']],
//                10,
//                'web'
//            );
//

        $search_console= new \Google_Service_Webmasters($client);
        $site_list = $search_console->getClient();
//        var_dump($site_list);

        $analytics = new \Google_Service_Analytics( $client );
        $webmaster = new \Google_Service_Webmasters( $client );
        $this->analytics = $analytics;
        $this->webmaster = $webmaster;
        $results = $this->get_site_requests( 'https://skydecor.com.ua/', 400 );
        $results = $this->searchanalytics_array( $results );
        var_dump($results);

    }







    // Gets the webmaster tools search analytics data
   protected  function get_site_requests( $url, $limit = false, $dimensions = array( 'query' ), $aggregation_type = 'auto', $options = array() ) {
        // Create a Search Analytics Query object so we can pass that into our searchanalytics query() method
        $search = new \Google_Service_Webmasters_SearchAnalyticsQueryRequest;
        $search->setStartDate( isset($start) ? $start : date( 'Y-m-d', strtotime( '1 month ago' ) ) );
        $search->setEndDate( isset($end) ? $end : date( 'Y-m-d', strtotime( 'now' ) ) );
        $search->setDimensions( $dimensions );
        if ( $limit ) $search->setRowLimit( $limit );
        $search->setAggregationType( $aggregation_type );
        // Pass our Search Analytics Query object as the second param to our searchanalytics query() method
        return $this->webmaster->searchanalytics->query( $url, $search, $options )->getRows();
    }

    // Parses the response from the Core Reporting API and prints
    protected function searchanalytics_array( $results ) {
        if ( ! empty( $results ) ) {
            // Setup first row with column names
            $array_result = array();
            foreach ( $results as $key => $result ) {
                // Columns
                $columns = array(
                    $key + 1,
                    $result->keys[0],
                    $result->clicks,
                    $result->impressions,
                    round( $result->ctr * 100, 2 ) . '%',
                    round( $result->position, 1 )
                );
                $array_result[] = [
                    'Rank'=> $key + 1,
                    'Query'=>  $result->keys[0],
                    'Clicks'=> $result->clicks,
                    'Impressions'=> $result->impressions,
                    'CTR'=> round( $result->ctr * 100, 2 ) . '%',
                    'Position'=> round( $result->position, 1 ),
                ];
            }
            return $array_result;
        }
        return false;
    }

    public function indexSheets()
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path() . '/MyProject-3277f4af3178.json');
        $client = new \Google_Client;
        $client->useApplicationDefaultCredentials();

        $client->setApplicationName("Something to do with my representatives");
        $client->setScopes(['https://www.googleapis.com/auth/drive','https://spreadsheets.google.com/feeds']);

        if ($client->isAccessTokenExpired()) {
            $client->refreshTokenWithAssertion();
        }

        $accessToken = $client->fetchAccessTokenWithAssertion()["access_token"];
        // Get our spreadsheet
        $service = new \Google_Service_Sheets($client);
        $newSheet = new \Google_Service_Sheets_Spreadsheet();
        $newSheet->setSpreadsheetId('34534534'); // <- hardcoded for test
        $response = $service->spreadsheets->create($newSheet);
        print_r($response);




    }

    public function indexSearch()
    {

        $client = new \Google_Client();
        $client->setApplicationName("My_App");
        $client->setDeveloperKey('AIzaSyA_8qqRtcSNYqD5bPsJja8IvTN1Wqt_i40');
        $service = new \Google_Service_Customsearch($client);
        $optParams = array("cx"=>'007312982128481838520:sm7npu9muf8', 'start'=>10);
        $results = $service->cse->listCse("Лампа",$optParams);
        foreach($results->getItems() as $k=>$item){
            var_dump($item);
        }
    }

}
