<?php
declare(strict_types=1);

ini_set('memory_limit', '-1');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$sFile = 'input/c1_PTS_sm.xlsx';
$lFile = 'input/PTS_Data.xlsx';

// METHOD 1 =  IOFactory::load()
if(false) {
    // guess the file type
    try {
        $sxl = IOFactory::load($sFile);
        $lxl = IOFactory::load($lFile);
        $xlAr = $lxl->getActiveSheet()->toArray(null, true, false, true);
    }
    catch(\Throwable $e) {
        echo $e->getMessage();
        $debug = 1;
    }
    
    $debug = 1;
}













// end of file
