<?php
ini_set('output_buffering', 'off');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$NoAuth = true;
session_start();
$_SESSION['companyCode'] = 'RHG';
$_SESSION['dbName'] = 'XBI_RHG';

require_once("../../../inc/DB.php");
require_once("../../../inc/AuthObjects.php");
require_once("../../../inc/CoreObjects.php");
require_once("../../../inc/DisplayObjects.php");
require_once("../../../inc/ExportObjects.php");
require_once("../../../inc/phpspreadsheetStyling.php");

use PhpOffice\PhpSpreadsheet\IOFactory;

$inputFileType = 'Xlsx';
$inputFileName = __DIR__ . '/budgetConsolidated_2020_20200117_flat.xlsx';

$dataColumns = range('A', 'S');
$dataRows = range(7, 2000);
$rowData = [];

function scrubExponent_RHG_BudgetImport($string) {
    $return = $string;
    $position = strpos($string, 'E-');
    if($position > 0) {
        $return = substr($string, 0, $position);
    }
    if(strlen($string) == '0') {
        $return = '0';
    }
    
    return $return;
}

$sql = <<<T_SQLx
SELECT [facID]
      ,[facName]
      ,[facCode]
      ,[ahtCono]
  FROM [XBI_RHG].[dbo].[facInfo]
  where (ahtCono<'700' OR ahtcono='901')
  and activeFlag=1
UNION all
SELECT
 facGroupID AS facID
 ,facIDGroupName AS facName
 ,facIDGroupName AS facCode
 ,facIDGroupName AS ahtCono
FROM dbo.facGroup
WHERE facGroupID IN (8012,8005,8006,8007,8008,8014,8013)
order by ahtCono
T_SQLx;

$facList = DB::Query($sql);

foreach($facList as $fac) {
    echo "Loading sheet: " . $fac['facCode'] . "\r\n";
    $reader = IOFactory::createReader($inputFileType);
    $reader->setLoadSheetsOnly($fac['facCode']);
    $spreadsheet = $reader->load($inputFileName);
    
    foreach($dataRows as $rowID) {
        foreach($dataColumns as $colID) {
            //$rowData[$rowID][$colID]=$spreadsheet->setActiveSheetIndex(0)->getCell($colID .$rowID)->getCalculatedValue();
            $rowData[$rowID][$colID] = $spreadsheet
                ->setActiveSheetIndex(0)
                ->getCell($colID . $rowID)->getValue();
            if($colID == 'E') {
                $bold = $spreadsheet
                    ->getActiveSheet()
                    ->getStyle($colID . $rowID)
                    ->getFont()->getBold();
                if($bold) {
                    //echo $rowID ." - ". $rowData[$rowID][$colID] ."\r\n";
                    $acctCategory = $rowData[$rowID][$colID];
                }
            }
        }
        
        if(StringHandler::NumericOnly(DB::Scrub($rowData[$rowID]['S'])) != '') { // Only import if year is not blank
            $sql = "insert into cust.facBudgetStaging_Consolidated (facID, cono, rowID, colA, colB, colC, colD, colE, acctCategory, budgetYear, period1, period2, period3, period4, period5, period6, period7, period8, period9, period10, period11, period12) values
          (
            '" . $fac['facID'] . "',
            '" . $fac['facCode'] . "',
            " . $rowID . ",
            '" . DB::Scrub($rowData[$rowID]['A']) . "',
            '" . DB::Scrub($rowData[$rowID]['B']) . "',
            '" . DB::Scrub($rowData[$rowID]['C']) . "',
            '" . DB::Scrub($rowData[$rowID]['D']) . "',
            '" . DB::Scrub($rowData[$rowID]['E']) . "',
            '" . DB::Scrub($acctCategory) . "',
            '2020',
            '" . scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['G'])) . "',
            '" . scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['H'])) . "',
            '" . scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['I'])) . "',
            '" . scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['J'])) . "',
            '" . scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['K'])) . "',
            '" . scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['L'])) . "',
            '" . scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['M'])) . "',
            '" . scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['N'])) . "',
            '" . scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['O'])) . "',
            '" . scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['P'])) . "',
            '" . scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['Q'])) . "',
            '" . scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['R'])) . "'
          )
";
            DB::Query($sql, 'bool');
            
        }
    }
    
    unset($reader);
    unset($spreadsheet);
    unset($loadedSheetNames);
    unset($rowData);
}