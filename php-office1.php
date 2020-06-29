<?php
declare(strict_types=1);

ini_set('memory_limit', '-1');
error_reporting(E_ALL);
ini_set('display_errors', '1');

require 'vendor/autoload.php';
require 'SccAdpImportCopy.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Xchive\Scc\SccAdpImportCopy;

$sFile = 'input/c1_PTS_sm.xlsx';
$lFile = 'input/PTS_Data.xlsx';

$scc = new SccAdpImportCopy();
$scc->getMostRecentFile();
$scc->insertIntoStaging();

//dynamicLoad($sFile, $lFile);
//objectLoad($sFile, $lFile);
//typeLoad($sFile, $lFile);

$debug = 1;

function echoTime ($start, $end) {
    $totalTime = (($end - $start)/1e+6)/1000;
    $totalTimeF = $totalTime;
    $minTime = '';
    if($totalTime > 60) {
        $minTime = ', ' . round(($totalTime / 60), 5);
    }
    echo "\n\n time = $totalTimeF secs $minTime \n\n";
}

function loadFiles ($reader, $sFile, $lFile): void {
    $start = hrtime(true);
    $sxl = $reader->load($sFile);
    echoTime($start, hrtime(true));
    
    $start = hrtime(true);
    $lxl = $reader->load($lFile);
    echo "\n_> load time:";
    echoTime($start, hrtime(true));
    
    $start = hrtime(true);
    $lxlAr = $lxl->getActiveSheet()->toArray(null, true, false, false);
    echo "\n_> toArray time:";
    echoTime($start, hrtime(true));
    $debug = 1;
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
        $lxlAr = $lxl->getActiveSheet()->toArray(null, true, false, true);
        $debug = 1;
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
        // reader
        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        
        echo "\n-------- objectLoad() --------\n";
        
        // loader
        loadFiles($reader, $sFile, $lFile);
        /*
            run 1 = 322 secs, w/->setReadDataOnly(true)
            run 2 = 320 secs, ""
        */
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
        // reader
        $fileType = 'Xlsx';
        $reader = IOFactory::createReader($fileType);
        $reader->setReadDataOnly(true);
    
        echo "\n-------- typeLoad() --------\n";
        
        // loader
        loadFiles($reader, $sFile, $lFile);
        
        /*
            ~ run 1 ~
            load = 318.0 secs, w/->setReadDataOnly(true)
            toArray = 88.6 secs
        
            ~ run 2 ~
            load = 315.0 secs, w/->getReadDataOnly(true)
            toArray = 74.2 secs, ""
        */
    }
    catch(\Throwable $e) {
        echo $e->getMessage();
        $debug = 1;
    }
}













// end of file
