<?php

namespace App\Http\Controllers;

use App\UrlList;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Session;
use Yangqi\Htmldom\Htmldom;
use Illuminate\Support\Facades\Input;

/**
 * Class SearchContactsController
 * @package App\Http\Controllers
 */
class SearchContactsController extends Controller
{

    public $soc_array = array(
        'facebook',
        'plus.google',
        'vk.com',
        'linkedin',
        'twitter',
    );    
    public $button_array = array(
        'отправить',
        'написать',
        'submit',
        'send',
        'связаться',
    ); 
    public $keywords_array = array(
        'связаться',
        'обратная связь',
        'форма обратной связи',
        'написать нам',
        'напишите нам',
        'contact us',
        'реклама',
        'advertising',
        'Десятинная',
    );
    public $result_parsing = array();
    public $domain_list = array();
    public $url_list = array();
    public $new_url_list = array();



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('import_excel');
    }

    /**
     * Export excel.
     * @param Request $request
     * @param ExcelImport $import
     * @return $this
     */
    public function store(Request $request, ExcelImport $import )
    {
//        $results = $import->get();
        if ($request->isMethod('post'))
        {

            $xls = $import->select(array('domain'))->get();

            if($this->parseDomains($xls)  === true  && !empty($this->domain_list)){
                return view('import_grid')->with(array('domains'=> $this->domain_list));
            } else{
                echo 'error';
            }
        }else{
            echo 'error';
        }
    }

    
    public function htmlget(Request $request, ExcelExport $export){
        if ($request->isMethod('post') && !empty(Input::get('url'))){

            session_start();
            $urls = array_values(UrlList::all('url')->toArray());
            $url_list = array();
            foreach ($urls as $url){
                $url_list[] = 'http://'.Input::get('url').'/'.$url['url'];
            }
            
            $result_curl = $this->getRemoteMultiUrl($url_list);

            if($result_curl ===true){
                $domain_info = $this->getDomainInfo($this->new_url_list);

                if ((!isset($_SESSION['parsing_result']) or empty($_SESSION['parsing_result']) ) && !empty($domain_info)) {
                    $_SESSION['parsing_result'] = $domain_info;
                } else if(isset($_SESSION['parsing_result']) && is_array( $_SESSION['parsing_result']) && is_array($domain_info)){
                    $_SESSION['parsing_result'] = array_merge($_SESSION['parsing_result'],$domain_info);
                }

                if(Input::get('last') == 1){
                    $result_parsing = $_SESSION['parsing_result'];

                    unset($_SESSION['parsing_result']);

                    // work on the export
                    $excel =  $export->sheet('sheetName', function($sheet) use($result_parsing)
                    {

                        $sheet->row(1, array(
                            'domains','-' ,'email', 'social', 'Contact Form', 'Buttons', 'Keywords'
                        ));
                        $i=1;

                        foreach ($result_parsing as $address=> $address_info){
                            $domain = parse_url($address);
                            $domain = $domain['host'];
                            if(isset($address_info['email']) && is_array($address_info['email'])){
                                $address_info['email'] = implode(',', $address_info['email']);
                            }
                            $i++;
                            $sheet->row($i,
                                array(
                                    isset($address) ? $domain : '-' ,
                                    isset($address) ? $address : '-'  ,
                                    isset($address_info['email']) ?
                                         $address_info['email'] : '-',
                                    isset($address_info['soc_links'])?  implode(',', $address_info['soc_links']) : '-',
                                    isset($address_info['form']) ? $address_info['form'] : 'NO',
                                    isset($address_info['button']) ? $address_info['button'] : 'NO',
                                    isset($address_info['keywords']) ? $address_info['keywords'] : 'NO',
                                ));

                        }
                    })->store('xls',  storage_path(''), true);

                    echo json_encode($excel);
                }
            }elseif(Input::get('last') == 1 ){
                $result_parsing = $_SESSION['parsing_result'];
                unset($_SESSION['parsing_result']);
                // work on the export
                $excel =  $export->sheet('sheetName', function($sheet) use($result_parsing)
                {

                    $sheet->row(1, array(
                        'domains','-' ,'email', 'social', 'Contact Form', 'Buttons', 'Keywords'
                    ));
                    $i=1;
                    foreach ($result_parsing as $address=> $address_info){
                        $domain = parse_url($address);
                        $domain = $domain['host'];
                        $i++;
                        $sheet->row($i,
                            array(
                                isset($address) ? $domain : '-' ,
                                isset($address) ? $address : '-'  ,
                                isset($address_info['email']) ? implode(',', $address_info['email']) : '-',
                                isset($address_info['soc_links'])?  implode(',', $address_info['soc_links']) : '-',
                                isset($address_info['form']) ? $address_info['form'] : 'NO',
                                isset($address_info['button']) ? $address_info['button'] : 'NO',
                                isset($address_info['keywords']) ? $address_info['keywords'] : 'NO',
                            ));

                    }
                })->store('xls',  storage_path(), true);
                echo json_encode($excel);
            }


        }elseif($request->isMethod('post') && !empty(Input::get('error'))){

            session_start();
            $result_parsing = $_SESSION['parsing_result'];
            unset($_SESSION['parsing_result']);
            // work on the export
            $excel =  $export->sheet('sheetName', function($sheet) use($result_parsing)
            {

                $sheet->row(1, array(
                    'domains','-' ,'email', 'social', 'Contact Form', 'Buttons', 'Keywords'
                ));
                $i=1;
                foreach ($result_parsing as $address=> $address_info){
                    $domain = parse_url($address);
                    $domain = $domain['host'];
                    $i++;
                    $sheet->row($i,
                        array(
                            isset($address) ? $domain : '-' ,
                            isset($address) ? $address : '-'  ,
                            isset($address_info['email']) ? implode(',',$address_info['email']) : '-',
                            isset($address_info['soc_links'])?  implode(',', $address_info['soc_links']) : '-',
                            isset($address_info['form']) ? $address_info['form'] : 'NO',
                            isset($address_info['button']) ? $address_info['button'] : 'NO',
                            isset($address_info['keywords']) ? $address_info['keywords'] : 'NO',
                        ));

                }
            })->store('xls',  storage_path(), true);
            echo json_encode($excel);
        }
        
    }


    /**
     * Parse html dom of urls in excel file
     * @param $xls
     * @return bool
     */
    protected function parseDomains($xls){
        $xls->each(function($sheet) {
            // Loop through all rows
            if (!empty($sheet)){
                $sheet->each(function($row)  {
                    if(!empty($row)){
                        $this->domain_list[] = $row;
                    }
                });
            }

        });
        return true;
  
    }


    /**
     * @param $url_list
     * @return array|bool
     */
    protected function getDomainInfo($url_list){

        $res_array = array();
        $res = 0;
        foreach ($url_list as $item_url){

                $html = new Htmldom($item_url);
                if(!empty($html->find('form'))){
                    $res = 1;
                    $res_array[$item_url] ['form'] = 'YES';
                }

                foreach($html->find('a') as $element){
                    foreach ($this->soc_array as $soc_item){
                        if(strpos($element->href, $soc_item )!==false){
                            $res_array[$item_url]['soc_links'] [$soc_item]= $element->href;
                            $res = 1;
                        }
                    }
                    foreach ($this->button_array as $button_item){
                        if(strpos($element->innertext, $button_item )!==false){
                            $res_array[$item_url]['button'] ='YES';
                            $res = 1;
                        }
                    }
                    if(filter_var($element->innertext, FILTER_VALIDATE_EMAIL)){
                         $res_array[$item_url]['email'][]= $element->innertext;
                        $res = 1;
                    }


                }
                foreach($html->find('button') as $element){
                    foreach ($this->button_array as $button_item){
                        if(strpos(mb_strtolower($element->innertext), mb_strtolower($button_item) )!==false){
                            $res_array[$item_url]['button']= 'YES';
                            $res = 1;
                        }
                    }
                }
                foreach($html->find('[type=submit]') as $element){
                    foreach ($this->button_array as $button_item){
                        if(strpos(mb_strtolower($element->value), mb_strtolower($button_item) ) !==false){
                            $res_array[$item_url]['button'] = 'YES';
                            $res = 1;
                        }
                    }
                }

                foreach($html->find('html') as $element){
                    foreach ($this->keywords_array as $keywords_item){
                        if(strpos($element->innertext, $keywords_item )!==false){
                            $res = 1;
                            $res_array[$item_url] ['keywords'] = 'YES';
                        }
                    }
                    if(preg_match_all("([a-zA-Z0-9._\-]+@[a-zA-Z0-9._\-]+\.[a-zA-Z0-9]+)",
                        $element->innertext,
                        $out_email, PREG_PATTERN_ORDER)){
                        if(is_array($out_email[0][0])){
                            $out_email[0][0] = implode(',', $out_email[0][0]);
                        }
                        if(isset($res_array[$item_url]['email'])){
                            if(!in_array($out_email[0][0], $res_array[$item_url]['email'] )){
                                $res_array[$item_url]['email'][] =  (string)$out_email[0][0];
                            }
                        }else{
                            $res_array[$item_url]['email'] =  (string)$out_email[0][0];
                        }
                    }
                }
        }
        return !empty($res) ? $res_array : false;
    }


    /**
     * @param array $item_url_list
     * @param array $redirect_list
     * @return bool
     */
    protected function getRemoteMultiUrl ($item_url_list = array(), $redirect_list = array()){
        if(!empty($redirect_list)){
            $nodes = $redirect_list;
        }else{
            $nodes = $item_url_list;
        }
        $node_count = count($nodes);

        $curl_arr = array();
        $master = curl_multi_init();

        for($i = 0; $i < $node_count; $i++)
        {
            $url =$nodes[$i];
            $curl_arr[$i] = curl_init($url);
            curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_arr[$i], CURLOPT_TIMEOUT,10);
            curl_setopt($curl_arr[$i], CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl_arr[$i], CURLOPT_FORBID_REUSE, true);
            curl_multi_add_handle($master, $curl_arr[$i]);
        }

        do {
            curl_multi_exec($master,$running);
        } while($running > 0);


        $redirects = array();
        for($i = 0; $i < $node_count; $i++)
        {
            $http_code = curl_getinfo($curl_arr[$i], CURLINFO_HTTP_CODE);

            if($http_code == 404){
                curl_multi_remove_handle($master, $curl_arr[$i]);
                curl_close($curl_arr[$i]);
                continue;
            }elseif($http_code == 200){
                $url_200 = curl_getinfo($curl_arr[$i], CURLINFO_EFFECTIVE_URL);
                if(!in_array($url_200, $this->new_url_list)) {
                    $this->new_url_list[] = $url_200;
                }
                curl_multi_remove_handle($master, $curl_arr[$i]);
                curl_close($curl_arr[$i]);
            }elseif($http_code == 301){
                $url_301 = curl_getinfo($curl_arr[$i], CURLINFO_REDIRECT_URL);
                if(!in_array($url_301, $this->new_url_list)) {
                    $redirects[]  = $url_301;
                }
                curl_multi_remove_handle($master, $curl_arr[$i]);
                curl_close($curl_arr[$i]);
            }
        }
        curl_multi_close($master);
        if(!empty($redirects)){
            return $this->getRemoteMultiUrl(array(), $redirects);
        }else{
            return true;
        }
    }
}
