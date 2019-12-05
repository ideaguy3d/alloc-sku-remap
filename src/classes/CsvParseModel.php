<?php
/**
 * Created by PhpStorm.
 * User: julius
 */
declare(strict_types=1);

namespace Redstone\Auto;

use Redstone\Auto\Interfaces\ICsvParseModel;

class CsvParseModel implements ICsvParseModel
{
    public static function csv2array(string $path): array {
        $csvFileFolder = glob("$path\*.csv");
        $csvFile = $csvFileFolder[0];
        $csv = [];
        $count = 0;
        
        if(($handle = fopen($csvFile, 'r')) !== false) {
            while(($data = fgetcsv($handle, 8096, ",")) !== false) {
                $csv[$count] = $data;
                ++$count;
            }
            fclose($handle);
        }
        
        return $csv;
    }
    
    public static function specificCsv2array(string $path2folder, string $csvName): array {
        if(strpos($csvName, '.csv') === false) {
            $csvName = $csvName . '.csv';
        }
        
        $csvFile = "$path2folder\\$csvName";
        
        if(strpos(strtolower($csvFile), 'ninja') !== false) {
            $break = 'point';
        }
        
        $csv = [];
        $count = 0;
        
        if(($handle = @fopen($csvFile, 'r')) !== false) {
            while(($data = fgetcsv($handle, 8096, ",")) !== false) {
                $csv[$count] = $data;
                ++$count;
            }
            fclose($handle);
        }
        
        return $csv;
    }
    
    public static function export2csv(array $dataSet, string $exportPath, string $name2giveFile): string {
        $name2giveFile = str_replace(' ', '-', strtolower($name2giveFile));
        $csvName = 'rs_' . $name2giveFile . '.csv';
        $exportPath = $exportPath . DIRECTORY_SEPARATOR . $csvName;
        $outputFile = fopen($exportPath, 'w') or exit("unable to open $exportPath");
        
        foreach($dataSet as $value) {
            if(is_array($value)) {
                fputcsv($outputFile, $value);
            }
            else {
                // it's MOST likely a Vector
                $value = $value->toArray();
                fputcsv($outputFile, $value);
                unset($value);
            }
        }
        
        fclose($outputFile);
        
        return $csvName;
        
    } // END OF: export2csv
    
    public static function export2csvNoRename(string $path, array $arr2export): string {
        if(($handle = fopen($path, 'w')) !== false) {
            foreach($arr2export as $row) {
                fputcsv($handle, $row);
            }
            
            // REMEMBER: close the file stream (:
            fclose($handle);
            return "\n\n__>> SUCCESS - File has finished processing.\n\n";
        }
        else {
            return "\n\n__>> ERROR - File didn't process. CsvParseModel.php line 25 ish\n\n";
        }
    }
    
} // END OF: class CsvParseModel