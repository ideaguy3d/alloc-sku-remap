<?php
/**
 * Created by PhpStorm.
 * User: julius
 */
declare(strict_types=1);

// manually require files so composer auto loading is not needed
require 'src\interfaces\ICsvParseModel.php';
require 'src\classes\AppGlobals.php';
require 'src\classes\CsvParseModel.php';
require 'src\classes\DataJoin.php';
require 'src\classes\AllocParser.php';

// namespaces to use
use Redstone\Auto\CsvParseModel;
use Redstone\Auto\DataJoin;

//-- SET THE FOLDER PATHS:
$ordersCsvFolderPath = 'C:\xampp\htdocs\alloc-sku-remap\csv\allocadence';
$accountingCsvFolderPath = 'C:\xampp\htdocs\alloc-sku-remap\csv\job-board';
$joinedDataResultFolderPath = 'C:\xampp\htdocs\alloc-sku-remap\csv\result-set';

//-- SET THE FILE NAMES:
$ordersCsvFileName = 'orders.csv';
$accountingCsvFileName = 'accounting.csv';
$joinedDataFileName = 'alloc_job-board';

// convert the CSV for Accounting CSV file to an array
$accountingArray = CsvParseModel::specificCsv2array($accountingCsvFolderPath, $accountingCsvFileName);

// create an instance of the DataJoin class
$dataJoin = new DataJoin($ordersCsvFolderPath, $ordersCsvFileName);

// Job Board CSV for Accounting data joined with Allocadence Orders
$allocJobBoardData = $dataJoin->allocadenceJoin($accountingArray);

// Export the joined data to a CSV
CsvParseModel::export2csv($allocJobBoardData, $joinedDataResultFolderPath, $joinedDataFileName);