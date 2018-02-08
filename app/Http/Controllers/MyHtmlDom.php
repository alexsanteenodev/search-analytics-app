<?php
namespace App\Http\Controllers;

class MyHtmlDom extends \Yangqi\Htmldom\Htmldom
{
    public function __construct($str=null, $lowercase=true, $forceTagsClosed=true, $target_charset=DEFAULT_TARGET_CHARSET, $stripRN=true, $defaultBRText=DEFAULT_BR_TEXT, $defaultSpanText=DEFAULT_SPAN_TEXT)
    {

        if ($str)
        {
            if (preg_match("/^(http|https):\/\//i",$str) )
            {
                $this->load_file($str);
            }
            else
            {
                $this->load($str, $lowercase, $stripRN, $defaultBRText, $defaultSpanText);
            }
        }
        // Forcing tags to be closed implies that we don't trust the html, but it can lead to parsing errors if we SHOULD trust the html.
        if (!$forceTagsClosed) {
            $this->optional_closing_array=array();
        }
        $this->_target_charset = $target_charset;
    }
}