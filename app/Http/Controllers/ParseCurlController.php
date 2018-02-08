<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Foundation\Application;
use Maatwebsite\Excel\Excel;
use Yangqi\Htmldom\Htmldom;

/**
 * Class ParseCurlController
 * @package App\Http\Controllers
 */
class ParseCurlController extends Controller
{

    protected $keywords;
    protected $file_count = 30;
    protected $search_result = 100;
    private $user_agent_file = 'useragent_list.txt';
    private $proxy_file = 'proxy_list.txt';
    protected $default_region = 'com';

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        return view('parse-curl.index');
    } 



    /**
     * @param ExcelImport $import
     * @return $this|bool
     */
    public function store(ExcelImport $import ){


        $xls = $import->select(array('keywords'))->get();
        $region = Input::get('region');
        if($this->parseKeywords($xls)  === true  && !empty($this->keywords)){
            
            return view('parse-curl.keywords_grid')->with(array('keywords'=> $this->keywords, 'region'=>$region));
        }
        return false;
    }


    /**
     * @param Request $request
     * @param Application $app
     * @param Excel $excel
     */
    public function keyget (Request $request , Application $app, Excel $excel){
        if ($request->isMethod('post') && !empty(Input::get('keyword'))){
            session_start();
            $region = Input::get('region') ?? $this->default_region;
            $keyword = Input::get('keyword');
            $keyword_clean = str_replace(' ', '+', $keyword);
            $url = "https://www.google.".$region."/search?num=".$this->search_result."&q=".$keyword_clean;

            $user_agents = $this->load_from_file(storage_path('curl_import/').$this->user_agent_file);
            $proxy = $this->load_from_file(storage_path('curl_import/').$this->proxy_file);
            $curl_options = array(
                CURLOPT_ENCODING => 'UTF-8',
                CURLOPT_HEADER => 1,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; ru; rv:1.9.1.2) Gecko/20090729 MRA 5.5 (build 02842) Firefox/3.5.2 GTB6',
            );
            $rollingCurl = new \RollingCurl\RollingCurl();


            if(!empty($user_agents) && is_array($user_agents)){
                $curl_options [CURLOPT_USERAGENT] = $user_agents[array_rand($user_agents, 1)];
            }
            if(!empty($proxy) && is_array($proxy)){
//                $curl_options [CURLOPT_PROXY] = '83.238.160.215:8080';
            }
            $rollingCurl->addOptions($curl_options);

            $rollingCurl
                ->get($url)
                ->setCallback(function( \RollingCurl\Request $request, \RollingCurl\RollingCurl $rollingCurl) use ($keyword)  {
                    $response_info = $request->getResponseInfo();
                    if($response_info['http_code'] == 200)
                    {
                        $html = new MyHtmlDom($request->getResponseText());
                        $i=0;
                        $parse_result = array();
                        foreach($html->find('cite') as $element){
                            $i++;
                            $domain = $this->getDomain(strip_tags($element->innertext));

                            $parse_result[] = array(
                                'domain' => $domain,
                                'position' => $i,
                                'url' => strip_tags($element->innertext),
                            );
                        }
                        if(isset($_SESSION['parsing_result'])){
                            $_SESSION['parsing_result'][$keyword]= $parse_result;
                        }else{
                            $_SESSION['parsing_result']= array($keyword=> $parse_result);
                        }
                    }else{
                        $_SESSION['parsing_result'][$keyword] = array(
                          'error'=>   $response_info['http_code'],
                        );

                        if(empty(Input::get('last') &&  Input::get('last')!== 1)) {
                            echo json_encode([
                                'error' => 'error parsing: '.$keyword,
                            ]); die(500);
                        }
                    }
                });


            $rollingCurl->setSimultaneousLimit(3);
            sleep(rand(1, 5));
            $rollingCurl->execute();

                if(!empty(Input::get('last') &&  Input::get('last')== 1)) {
                    $this->resultProcessing($app, $excel);
                }

        }elseif($request->isMethod('post') && !empty(Input::get('error'))){
            session_start();
           $this->resultProcessing($app, $excel);
        }

    }




    /**
     * @param Application $app
     * @param Excel $excel
     */
    protected function resultProcessing(Application $app, Excel $excel){
        $result_parsing = $_SESSION['parsing_result'] ?? '';
        session_unset();
        if(floor(count($result_parsing)/$this->file_count) >=1){
            $result_parsing = array_chunk($result_parsing, $this->file_count, true);
            $folder = 0;

            $result_excel = array();
            foreach ($result_parsing as $parsing_count){
                $export = new ExcelKeywordsExport($app,$excel);
                $result_excel ['files'][] = $this->xlsExport($export, $parsing_count,$folder);
                $folder++;
            }
        }else{
            $export = new ExcelKeywordsExport($app,$excel);
            $result_excel = $this->xlsExport($export, $result_parsing, 0);
        }
        echo json_encode($result_excel);
    }

    /**
     * @param ExcelKeywordsExport $export
     * @param $parsing
     * @param $folder
     * @return mixed
     */
    public function xlsExport(ExcelKeywordsExport $export, $parsing,$folder){
        $j=0;
        foreach ($parsing as $key=> $keyword){
            $j++;
            // Our first sheet
            $invalidCharacters = \PHPExcel_Worksheet::getInvalidCharacters();
            $key = str_replace($invalidCharacters, '', $key);
            $excel =  $export->sheet($key, function($sheet)use ($keyword) {
                $sheet->row(1, array(
                    'domain','position' ,'url'
                ));
                $i=1;

                foreach ($keyword as $info){
                    if(isset($address_info['email']) && is_array($address_info['email'])){
                        $address_info['email'] = implode(',', $address_info['email']);
                    }
                    $i++;
                    $sheet->row($i,
                        array(
                            isset($info['domain']) ? $info['domain'] : '-' ,
                            isset($info['position']) ? $info['position'] : '-'  ,
                            isset($info['url']) ? $info['url'] : 'url',
                        ));
                }
            });

        }
        $result_excel  = $excel->store('xls',  storage_path('keywords_parsing/'.$folder), true);
        return $result_excel;

    }

    /**
     * Parse html dom of keywords in excel file
     * @param $xls
     * @return bool
     */
    protected function parseKeywords($xls){
        $xls->each(function($sheet) {
            // Loop through all rows
            if (!empty($sheet)){
                $sheet->each(function($row)  {
                    if(!empty($row)){
                        $this->keywords[] = $row;
                    }
                });
            }

        });
        return true;

    }

    /**
     * Loading info from external files
     *
     * @access private
     * @param string $filename
     * @param string $delim
     * @return array
     */
    protected static function load_from_file($filename, $delim = "\n")
    {

        $fp = @fopen($filename, "r");

        if(!$fp)
        {
            return array();
        }
        $data = @fread($fp, filesize($filename) );
        fclose($fp);
        if(strlen($data)<1)
        {
            return array();
        }

        $array = explode($delim, $data);


        if(is_array($array) && count($array)>0)
        {
            foreach($array as $k => $v)
            {
                if(strlen( trim($v) ) > 0)
                    $array[$k] = trim($v);
            }
            return $array;
        }
        else
        {
            return array();
        }
    }


    public function getDomain ($address) {

        $parse = parse_url($address);
        if(isset($parse['scheme'])){
            $output = $parse['scheme'].'://'.$parse['host'];
        }else{
            $output = explode( '/',$address);
            $output = $output[0];
        }
        $output = str_replace('http://', '', $output);
        $output = str_replace('https://', '', $output);
        $output = str_replace('www.', '', $output);
        $output = str_replace('/', '', $output);
        return $output;
    }
}
