<?php
namespace App\Http\Controllers;

use \Maatwebsite\Excel\Files\ExcelFile;
use Illuminate\Support\Facades\Input;
use Symfony\Component\VarDumper\VarDumper;

class ExcelImport extends ExcelFile {

    public function getFile()
    {
        // Import a user provided file
        $file = Input::file('xls_import');
//        $xls = \PHPExcel_IOFactory::load($file);
//// Устанавливаем индекс активного листа
//        $xls->setActiveSheetIndex(0);
//        $sheet = $xls->getActiveSheet();
////        VarDumper::dump($sheet);
//        $rowIterator = $sheet->getRowIterator();
//
//echo "<table>";
//
//// Получили строки и обойдем их в цикле
//$rowIterator = $sheet->getRowIterator();
//foreach ($rowIterator as $row) {
//$cellIterator = $row->getCellIterator();
//
//echo "<tr>";
//
//foreach ($cellIterator as $cell) {
//    echo "<td>" . $cell->getCalculatedValue() . "</td>";
//}
//echo "</tr>";
//}
//echo "</table>";
        return $file;

//        return storage_path() . '/file.xls';
    }

    public function getFilters()
    {
        return [
            'chunk'
        ];
    }

}