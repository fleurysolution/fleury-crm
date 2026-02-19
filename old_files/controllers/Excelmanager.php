<?php
namespace App\Controllers;

class Excelmanager extends Security_Controller
{
    public function index()
    {
       require_once(APPPATH . "ThirdParty/PHPOffice-PhpSpreadsheet/vendor/autoload.php");
       $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('gfg.xlsx'); 
   
        
        //$sheet = \PhpOffice\PhpSpreadsheet\src\Spreadsheet::createSheet();
        //$sheet->setCellValue('A1', 'Hello World !');
        //$writer = new Xlsx($spreadsheet);
        //$writer->save('hello world.xlsx');
    }
} 