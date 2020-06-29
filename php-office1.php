<?php
declare(strict_types=1);

ini_set('memory_limit', '-1');
error_reporting(E_ALL);
ini_set('display_errors', '1');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

$sFile = 'input/c1_PTS_sm.xlsx';
$lFile = 'input/PTS_Data.xlsx';

dynamicLoad($sFile, $lFile);
objectLoad($sFile, $lFile);
typeLoad($sFile, $lFile);

function echoTime ($start, $end) {
    $totalTime = (($end - $start)/1e+6)/1000;
    echo "\n\n time = $totalTime secs \n\n";
}

/**
 *
 * @param $sFile
 * @param $lFile
 */
function dynamicLoad($sFile, $lFile) {
    // guess the file type
    try {
        $start = hrtime(true);
        $sxl = IOFactory::load($sFile);
        echoTime($start, hrtime(true));
        
        $start = hrtime(true);
        $lxl = IOFactory::load($lFile);
        echoTime($start, hrtime(true));
        // run 1 time = 338.2422553 secs, 5.6 minutes
        $xlAr = $lxl->getActiveSheet()->toArray(null, true, false, true);
    }
    catch(\Throwable $e) {
        echo $e->getMessage();
        $debug = 1;
    }
    
    $debug = 1;
}

/**
 *
 * @param $sFile
 * @param $lFile
 */
function objectLoad($sFile, $lFile) {
    try {
        $reader = new Xlsx();
        $sxl = $reader->load($sFile);
        $lxl = $reader->load($lFile);
    }
    catch(\Throwable $e) {
        echo $e->getMessage();
        $debug = 1;
    }
}

/**
 * @param $sFile
 * @param $lFile
 */
function typeLoad($sFile, $lFile) {
    try {
        $fileType = 'Xlsx';
        $reader = IOFactory::createReader($fileType);
        
        $start = hrtime(true);
        $sxl = $reader->load($sFile);
        echoTime($start, hrtime(true));
        
        
    }
    catch(\Throwable $e) {
    
    }
}













// end of file
