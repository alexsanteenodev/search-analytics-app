<?php
namespace App\Http\Controllers;
use Maatwebsite\Excel\Files\NewExcelFile;
use Auth;

class ExcelKeywordsExport extends NewExcelFile{

    public $excel_file ;


    public function mySetFilename($filename)
    {
        return   $this->excel_file = Auth::user()->name.$filename;
    }
    public function getFilename()
    {
        return isset($this->excel_file) ? $this->excel_file : Auth::user()->name.'_export_keywords';
    }

}