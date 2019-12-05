<?php
/**
 * Created by PhpStorm.
 * User: julius
 */
declare(strict_types=1);

namespace Redstone\Auto;

class AppGlobals
{
    /**
     * @param string $info
     *
     * @return string
     */
    public static function rsLogInfo(string $info): string {
        
    
        $handle = null;
        $newLines = "\n\r\n\r";
        $info = substr_replace($info, $newLines, 0, 0);
        $info = substr_replace($info, $newLines, strlen($info), 0);
    
        // append all logs by day to the same file
        $date = getdate();
        $logDay = "\COM_AUTO_LOG - $date[month] $date[mday], $date[year].txt";
        $filePath = "\_logs" . $logDay;
        
        // method & line
        $ml = __METHOD__ . " Line " . __LINE__;
        
        try {
            if(!is_writable($filePath) || !is_readable($filePath)) {
                throw new \Exception("PHP does not have permission to $filePath. ~$ml");
            }
            $handle = fopen($filePath, 'a') or false;
            if($handle === false) {
                throw new \Exception("file $filePath FAILED to open");
            }
            fwrite($handle, $info);
        }
        catch(\Exception $e) {
            // $m for message
            $m = $e->getMessage() . " | ";
            $m .= "File $filePath could not be created ~";
            $m .= $ml;
            echo $m;
            return $m;
        }
        finally {
            $handle = $handle ?? null;
            if($handle) fclose($handle);
        }
        
        return 'success';
        
    } // END OF: LogComAutoInfo()
    
}