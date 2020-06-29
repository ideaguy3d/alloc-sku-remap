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
    
    public $dbName = 'Arena';
    
    public $dbHost = 'juliusx';
    
    public $stagingTable = 'PTS_Data';
    
    public $fileData = [];
    
    public function __construct() {
        $this->folderPath = $this->uploadFolderPath;
        $this->initPdo();
        $this->truncateTable();
    }
    
    private function echoTime ($start, $end) {
        $totalTime = (($end - $start)/1e+6)/1000;
        $totalTimeF = $totalTime;
        $minTime = '';
        if($totalTime > 60) {
            $minTime = ', ' . round(($totalTime / 60), 5);
        }
        echo "\n\n time = $totalTimeF secs $minTime \n\n";
    }
    
    public function initPdo(): void {
        $this->pdo = new PDO(
            "sqlsrv:Database=$this->dbName;server=$this->dbHost",
            // TOTALLY NOT SECURE... but it's a private repo & a dev db
            'julius3', 'jiha1989'
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
        
        try {
            /* -- method 1 --
            $xl = new Xlsx();
            $xl = $xl->load($fullPathFileName);
            */
            
            /* -- method 2 -- */
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            
            $start = hrtime(true);
            echo "\nload()";
            $xl = $reader->load($createFullPath($mostRecentlyCreatedFileName));
            $this->echoTime($start, hrtime(true));
            
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
            // get rid of the header row
            array_shift($this->fileData);
            $batches = array_chunk($this->fileData, 999);
            
            foreach($batches as $batch) {
                $sql = "insert into [$this->stagingTable] values";
                
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
                unset($sql);
            }
        }
        catch(\Throwable $e) {
            echo $e->getMessage();
            $debug = 1;
        }
    }
}






















// end of file