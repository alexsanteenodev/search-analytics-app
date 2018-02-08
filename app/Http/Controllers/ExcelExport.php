<?php
namespace App\Http\Controllers;
use Maatwebsite\Excel\Files\NewExcelFile;
use Auth;

class ExcelExport extends NewExcelFile{

    public function getFilename()
    {
        return 'export'.Auth::user()->name;
    }

}