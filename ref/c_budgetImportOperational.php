<?php
ini_set('output_buffering', 'off');
error_reporting(E_ALL);
ini_set('display_errors', '1');

//session_start();
//require __DIR__. '../../../vendor/autoload.php';
//$dotenv = new Dotenv\Dotenv(__DIR__);
//$dotenv->load();

$NoAuth=true;
session_start();
$_SESSION['companyCode']='RHG';
$_SESSION['dbName']='XBI_RHG';

require_once("../../../inc/DB.php");
require_once("../../../inc/AuthObjects.php");
require_once("../../../inc/CoreObjects.php");
require_once("../../../inc/DisplayObjects.php");
require_once("../../../inc/ExportObjects.php");
require_once("../../../inc/phpspreadsheetStyling.php");

use PhpOffice\PhpSpreadsheet\IOFactory;
$inputFileType = 'Xlsx';
$inputFileName = __DIR__ . '/budgetOperational_2020_2020-01-27.xlsx';

$dataColumns=excelColumnRange('A','AE');
$dataRows=range(7,1000);

function scrubExponent_RHG_BudgetImport($string){
    $return = $string;
    $position = strpos($string,'E-');
    if($position > 0){
        $return = substr($string,0,$position);
    }
    if(strlen($string) == '0'){
        $return = '0';
    }

    return $return;
}

$rowData=array();

//DB::Query("truncate table cust.facBudgetStaging_Operational");

// Note: 2019 has no 901, 8011, 8013
$sql="
SELECT [facID]
      ,[facName]
      ,[facCode]
      ,[ahtCono]
  FROM [XBI_RHG].[dbo].[facInfo]
  where (ahtCono<'700' OR ahtcono='9010')
  --and ahtCono in ('104','105','106','107','108','120','112','114')
  --and ahtCono='104'
  and activeFlag=1
  --and activeFlag=0
UNION all
SELECT
 facGroupID AS facID
 ,facIDGroupName AS facName
 ,facIDGroupName AS facCode
 ,facIDGroupName AS ahtCono
FROM dbo.facGroup
	WHERE
		facGroupID IN (8012,8005,8006,8007,8008,8014,8013) -- No 8011 (Renew_Consol)
		--and facGroupID=8006
  order by ahtCono

";

$facList=DB::Query($sql);

foreach($facList as $fac){
    echo "Loading sheet: ". $fac['facCode'] ."\r\n";
    $reader = IOFactory::createReader($inputFileType);
    $reader->setLoadSheetsOnly($fac['facCode']);
    $spreadsheet = $reader->load($inputFileName);

//    $loadedSheetNames = $spreadsheet->getSheetNames();
//    foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
//        echo $sheetIndex . ' -> ' . $loadedSheetName ."\r\n";
//    }

    foreach($dataRows as $rowID){
        foreach($dataColumns as $colID){
//            $rowData[$rowID][$colID]=$spreadsheet->setActiveSheetIndex(0)->getCell($colID .$rowID)->getCalculatedValue();
            $rowData[$rowID][$colID]=$spreadsheet->setActiveSheetIndex(0)->getCell($colID .$rowID)->getValue();
            if($colID == 'E'){
                $bold = $spreadsheet->getActiveSheet()->getStyle($colID .$rowID)->getFont()->getBold();
                if($bold){
//                    echo $rowID ." - ". $rowData[$rowID][$colID] ."\r\n";
                    $acctCategory=$rowData[$rowID][$colID];
                }
            }
        }

        if(StringHandler::NumericOnly(DB::Scrub($rowData[$rowID]['AE'])) != ''){ // Only import if year value is not blank
            $sql="insert into cust.facBudgetStaging_Operational (facID, cono, rowID, colA, colB, colC, colD, colE, acctCategory, budgetYear, 
                    period1,
                    period1PPD,
                    period2,
                    period2PPD,
                    period3,
                    period3PPD,
                    period4,
                    period4PPD,
                    period5,
                    period5PPD,
                    period6,
                    period6PPD,
                    period7,
                    period7PPD,
                    period8,
                    period8PPD,
                    period9,
                    period9PPD,
                    period10,
                    period10PPD,
                    period11,
                    period11PPD,
                    period12,
                    period12PPD) values 
          (
            '". $fac['facID'] ."',
            '". $fac['facCode'] ."',
            ". $rowID .",
            '". DB::Scrub($rowData[$rowID]['A']) ."',
            '". DB::Scrub($rowData[$rowID]['B']) ."',
            '". DB::Scrub($rowData[$rowID]['C']) ."',
            '". DB::Scrub($rowData[$rowID]['D']) ."',
            '". DB::Scrub($rowData[$rowID]['E']) ."',
            '". DB::Scrub($acctCategory) ."',
            '2020',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['G'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['H'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['I'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['J'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['K'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['L'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['M'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['N'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['O'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['P'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['Q'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['R'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['S'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['T'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['U'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['V'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['W'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['X'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['Y'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['Z'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['AA'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['AB'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['AC'])) ."',
            '". scrubExponent_RHG_BudgetImport(DB::Scrub($rowData[$rowID]['AD'])) ."'
          )
";

            if($rowID == '253' and $fac['facID'] == '8006'){
                echo 'Data: '. $rowData[$rowID]['H']. PHP_EOL;
            }
            DB::Query($sql,'bool');

        }
    }

    unset($reader);
    unset($spreadsheet);
    unset($loadedSheetNames);
    unset($rowData);
}


//$highestColumnAsLetters = $this->objPHPExcel->setActiveSheetIndex(0)->getHighestColumn(); //e.g. 'AK'
//$highestRowNumber = $this->objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
//$highestColumnAsLetters++;
//for ($row = 1; $row < $highestRowNumber + 1; $row++) {
//    $dataset = array();
//    for ($columnAsLetters = 'A'; $columnAsLetters != $highestColumnAsLetters; $columnAsLetters++) {
//        $dataset[] = $this->objPHPExcel->setActiveSheetIndex(0)->getCell($columnAsLetters.$row)->getValue();
//        if ($row == 1)
//        {
//            $this->column_names[] = $columnAsLetters;
//        }
//    }
//    $this->datasets[] = $dataset;
//}