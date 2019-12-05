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
        $filePath = '.\_logs';
        
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