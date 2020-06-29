<?php
declare(strict_types=1);

namespace Xchive\Scc;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PDO;

class SccAdpImportCopy
{
    /**
     * @var array
     */
    public $resultSet = [];
    
    /**
     * Manually set this to production or development path in constructor
     * @var string
     */
    public $folderPath;
    
    /**
     * Here for reference
     * @var string
     */
    public $proFolderPath = '/srv/nfs/storage/DataAgentIncoming/c.sccpbjupload/adpUpload/';
    
    /**
     * Manually set the upload folder path
     * @var string
     */
    public $uploadFolderPath = '@mockAdpUpload';
    
    /**
     * Manually set the archive folder path
     * @var string
     */
    public $archiveFolderPath = '@mockArchive';
    
    private $otherFiles =  [];
    
    /**
     * @var PDO
     */
    public $pdo;
    
    public $dbName = 'XBI_ARENA';
    
    public $dbHost = 'xcdevdb0.xchive.local';
    
    public $stagingTable = 'pts1_staging';
    
    public $fileData = [];
    
    public function __construct() {
        $this->folderPath = $this->uploadFolderPath;
        $this->initPdo();
        $this->truncateTable();
    }
    
    public function initPdo(): void {
        $this->pdo = new PDO(
            "sqlsrv:Database=$this->dbName;server=$this->dbHost",
            // TOTALLY NOT SECURE... but it's a private repo & a dev db
            'julius', 'jiha1989'
        );
        
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
    
    public function truncateTable() {
        // using a HEREDOC (similar to " ") for code folding in PhpStorm
        $sql = <<<T_SQLx
truncate table [$this->stagingTable];
T_SQLx;
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
        }
        catch(\Throwable $e) {
            echo $e->getMessage();
        }
    }
    
    /**
     * Get the most recent file by date
     */
    public function getMostRecentFile() {
        $fileNames = scandir($this->folderPath);
        $createdModifiedAr = [];
        $timeStampAr = [];
        
        // to get files' modified and created info
        $createdModifiedLambda = function() use (&$fileName, &$fullPathFileName, &$createdModifiedAr) {
            $dateModified = date('d M Y H:i:s', filemtime($fullPathFileName));
            $dateCreated = date('d M Y H:i:s', filectime($fullPathFileName));
            $createdModifiedAr[$fileName] = ['mod' => $dateModified, 'cre' => $dateCreated];
        };
        
        $createFullPath = function(string $fileName): string {
            return './' . $this->folderPath . '/' . $fileName;
        };
        
        // figure out the most recent file by date
        foreach($fileNames as $fileName) {
            if(in_array($fileName, ['.', '..'])) continue;
            $fullPathFileName = $createFullPath($fileName);
            // $createdModifiedLambda(); // to get created and modified dates
            $timeStampAr[$fileName] = strtotime(date('d M Y H:i:s', filectime($fullPathFileName)));
            $this->otherFiles [] = $fullPathFileName;
        }
        
        arsort($timeStampAr);
        $mostRecentlyCreatedFileName = array_key_first($timeStampAr);
        
        // remove most recent file
        array_splice(
            $this->otherFiles,
            array_search($createFullPath($mostRecentlyCreatedFileName), $this->otherFiles),
            1
        );
    
        foreach($this->otherFiles as $otherFile) {
            $pathToMove = $this->archiveFolderPath . '/'. basename($otherFile);
            rename($otherFile, $pathToMove);
        }
        
        $debug = 1;
        
        try {
            /* -- method 1 --
            $xl = new Xlsx();
            $xl = $xl->load($fullPathFileName);
            */
            
            /* -- method 2 -- */
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $xl = $reader->load($createFullPath($mostRecentlyCreatedFileName));
            
            $this->fileData = $xl->getActiveSheet()->toArray(
                null, true, false, false
            );
        }
        catch(\Throwable $e) {
            echo '__>> ERROR: ' . $e->getMessage();
            $debug = 1;
        }
    }
    
    public function insertIntoStaging() {
        try {
            // build the query
            $sql = "insert into [$this->stagingTable] (
                           [Employee ID]    -- 1
                          ,[Full Name]      -- 2
                          ,[Punch In Date]  -- 3
                          ,[Punch In Time]  -- 4
                          ,[Punch Out Date] -- 5
                          ,[Punch Out Time] -- 6
                          ,[Hours Amount (Hourly value)]  -- 7
                          ,[Hours Amount (Decimal Value)] -- 8
                          ,[Pay Group]      -- 9
                          ,[Company]        -- 10
                          ,[Location]       -- 11
                          ,[Payroll Dept]   -- 12
                          ,[Category]       -- 13
                          ,[Reports To]     -- 14
                          ,[Job]            -- 15
                          ,[Pay Type]       -- 16
                  ) values";
            
            
            // get rid of the header row
            array_shift($this->fileData);
            $batches = array_chunk($this->fileData, 999);
            
            foreach($batches as $batch) {
                // build the query
                foreach($batch as $fileDatum) {
                    // wrap each value in ""
                    foreach($fileDatum as $i => $field) {
                        if(is_numeric($field)) continue;
                        $fileDatum[$i] = "'$field'";
                    }
                    $sql .= (' (' . implode(', ', $fileDatum) . '), ');
                }
                $sql = trim($sql);
                // replace trailing ','
                $sql[-1] = ';';
                $statement = $this->pdo->prepare($sql);
                $statement->execute();
            }
            
            
        }
        catch(\Throwable $e) {
            echo $e->getMessage();
            $debug = 1;
        }
    }
}






















// end of file