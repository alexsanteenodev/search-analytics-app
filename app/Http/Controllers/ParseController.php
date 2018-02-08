<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Foundation\Application;
use Maatwebsite\Excel\Excel;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class ParseController
 * @package App\Http\Controllers
 */
class ParseController extends Controller
{

    public $keywords;
    protected $file_count = 30;
    private $config =
        [
            'token' => 'a4cc16496dada376e5cc30b1510d350b',
        ];
    protected $default_region = 'g_ua';
    protected $api_method = 'keyword_top';

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        return view('parse.index');
    }

    /**
     * @param ExcelImport $import
     * @return $this|bool
     */
    public function store(ExcelImport $import ){


        $xls = $import->get();

        $region = Input::get('region');
        if($this->parseKeywords($xls)  === true  && !empty($this->keywords)){
            return view('parse.keywords_grid')->with(array('keywords'=> $this->keywords, 'region'=>$region));
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
            $keyword = Input::get('keyword');
            $region = Input::get('region');
            $region = isset($region) ? $region : $this->default_region;
//            $url = 'http://api.serpstat.com/v3/'.$this->api_method.'?query='.$keyword.'&token='.$token.'&se='.$region;
//            $result = json_decode(file_get_contents($url));

            // create instance of any api method class
            // list of methods classes in folder src\Methods
            $apiClient = new \Serpstat\Sdk\Core\ApiGuzzleHttpClient($this->config['token']);

            $apiMethod = new \Serpstat\Sdk\Methods\KeywordTopMethod(
                $keyword,
                $region
            );
            try {
                // try call api method
                $result = $apiClient->call($apiMethod);

                if($result->getStatusMsg() == 'OK'){
                    $result = $result->getResult();
                    if( isset($result['top']) && is_array($result['top'])){
                        $parse_result = array();
                        foreach ($result['top'] as $item){
                            $parse_result[] = array(
                                'domain' => $item['domain'],
                                'position' => $item['position'],
                                'url' =>$item['url'],
                            );
                        }
                        session_start();
                        if(isset($_SESSION['parsing_result'])){
                            $_SESSION['parsing_result'][$keyword]= $parse_result;
                        }else{
                            $_SESSION['parsing_result']= array($keyword=> $parse_result);
                        }
                        if(!empty(Input::get('last') &&  Input::get('last')== 1)) {
                            $this->resultProcessing($app, $excel);
                        }
                    }

                }else{
                    echo json_encode(array('error' => $result->getStatusMsg()));
                }
            } catch (\Exception $e) {
                // catch api error
                $result = $e->getMessage();
                echo json_encode(array('error' =>$result));
            }


        }elseif($request->isMethod('post') && !empty(Input::get('error'))){
           $this->resultProcessing($app, $excel);
        }

    }

    /**
     * @param Application $app
     * @param Excel $excel
     */
    protected function resultProcessing(Application $app, Excel $excel){
        $result_parsing = $_SESSION['parsing_result'];
        unset($_SESSION['parsing_result']);
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
}
