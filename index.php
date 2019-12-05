<?php
/**
 * Created by PhpStorm.
 * User: julius
 */
declare(strict_types=1);

require 'src\interfaces\ICsvParseModel.php';
require 'src\classes\AppGlobals.php';
require 'src\classes\CsvParseModel.php';
require 'src\classes\DataJoin.php';
require 'src\classes\AllocParser.php';

// variable declarations
use Redstone\Auto\AppGlobals;
use Redstone\Auto\CsvParseModel;
use Redstone\Auto\DataJoin;

$jobBoardAccountingCsvData = null;
$commissionAuto = null;

$dataJoin = new DataJoin();

// job board indexes
$jbi = findColumnOrder($jobBoardData[0]);

// Job Board CSV data joined with Allocadence Orders
$jobBoardData = $dataJoin->allocadenceJoin($jobBoardData, $jbi);

// variable initializations
$path2folder = AppGlobals::PathToCommissionCsvFolder();
$accountingData = CsvParseModel::specificCsv2array($path2folder, AppGlobals::$accounting_csv);

// export the summed [client] AND [sales_rep] data
CsvParseModel::export2csv();
CsvParseModel::export2csv();