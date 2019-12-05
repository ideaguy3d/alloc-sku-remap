<?php
/**
 * Created by PhpStorm.
 * User: julius
 */
declare(strict_types=1);

namespace Redstone\Auto;

class AppGlobals
{
    //-------------------------------------------------------------
    // TURN DEBUG MODE on OR off
    //-------------------------------------------------------------
    public static $REDSTONE_AUTO_DEBUG = true;
    
    //-------------------------------------------------------------
    // TURN TEST MODE on OR off, this is set for unit testing
    //-------------------------------------------------------------
    public static $REDSTONE_AUTO_TEST = false;
    
    /**
     * local path, NOT the production path = C:\xampp\htdocs\alloc-sku-remap\_logs
     * new path for ad hoc network setup = C:\Users\julius\Documents\rsapp\rs-comauto\app\logs
     *
     * @var string
     */
    private static $localLogFolderPath = '.\_logs';
    
    /**
     * C:\inetpub\wwwroot\rs-comauto\logs
     *
     * @var string
     */
    private static $proLogFolderPath = '.\_logs';
    
    /**
     * @var string
     */
    public static $accounting_csv = 'accounting.csv';
    
    /**
     * @return string
     */
    public static function PathToCommissionCsvFolder() {
        $hostName = gethostname();
        // C:\inetpub\wwwroot\rs-comauto\app\commission-csv
        return ($hostName === 'Julius1')
            ? 'C:\xampp\htdocs\rs-comauto\app\commission-csv'
            : '.\app\commission-csv';
    }
    
    /**
     * @return bool
     */
    public static function isLocalHost(): bool {
        return (gethostname() === 'Julius1');
    }
    
    /**
     * @param string $info
     *
     * @return string
     */
    public static function LogComAutoInfo(string $info): string {
        // fopen(), fwrite(), fclose()
        $handle = null;
        $newLines = "\n\r\n\r";
        $localHost = self::isLocalHost();
        $info = substr_replace($info, $newLines, 0, 0);
        $info = substr_replace($info, $newLines, strlen($info), 0);
        
        // append all logs by day to the same file
        $date = getdate();
        $logDay = "\COM_AUTO_LOG - $date[month] $date[mday], $date[year].txt";
        
        if($localHost) {
            $filePath = self::$localLogFolderPath . $logDay;
        }
        else {
            $filePath = self::$proLogFolderPath . $logDay;
        }
        
        try {
            $handle = fopen($filePath, 'a') or false;
            if($handle === false) {
                throw new \Exception("file $filePath FAILED to open >:(");
            }
            fwrite($handle, $info);
        }
        catch(\Exception $e) {
            // $m for message
            $m = $e->getMessage() . " | ";
            $m .= "File $filePath could not be created ~";
            $m .= __METHOD__ . " Line " . __LINE__;
            return $m;
        }
        finally {
            $handle = $handle ?? null;
            if($handle) fclose($handle);
        }
        
        return 'success';
        
    } // END OF: LogComAutoInfo()
}