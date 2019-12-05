<?php
/**
 * Created by PhpStorm.
 * User: julius
 */
declare(strict_types=1);

namespace Redstone\Auto;


class DataJoin
{
    /**
     * @var string
     */
    private $allocOrderFileName;
    /**
     * @var string
     */
    private $allocFolder;
    /**
     * @var int
     */
    private $allocJobCount = 0;
    /**
     * @var array
     */
    private $headerRow;
    /**
     * @var array
     */
    private $indexFor;
    
    /**
     * The constructor will determine if the app is in a local env and
     * set the folder paths
     *
     * @param string $allocFolder
     * @param string $allocOrderFileName
     */
    public function __construct(string $allocFolder, string $allocOrderFileName) {
        $this->allocFolder = $allocFolder;
        $this->allocOrderFileName = $allocOrderFileName;
    }
    
    /**
     * This function will join allocadence orders to the job board csv data
     *
     * @param $jobBoardData array - the data from ComAutoComplex
     *
     * @return array - it'll return the joined CSV for Accounting & Allocadence data
     */
    public function allocadenceJoin(array $jobBoardData): array {
        $f = $this->findColumnOrder($jobBoardData[0]);
        $joinedData = [];
        
        //TODO: _HARD CODED Alloc Orders CSV download INDEXES needs to change !!!!!!!!!
        $i_ordSku = 3;
        $i_ordDes = 20;
        $i_ordJobId = 17;
        $i_ordSpecial = 69;
        
        //TODO: set some exception handlers to check hardcoded indexes
        $allocOrdersData = CsvParseModel::specificCsv2array($this->allocFolder, $this->allocOrderFileName);
        $allocOrdersDataHash = [];
        
        // OUTER-LOOP_1 hashes the allocadence order data by job board id
        foreach($allocOrdersData as $i => $order) {
            $_jobId = $order[$i_ordJobId];
          
            if(isset($allocOrdersDataHash[$_jobId])) {
                $specs1 = $allocOrdersDataHash[$_jobId];
                $specs2 = [
                    $order[$i_ordSku],
                    $order[$i_ordDes],
                    $order[$i_ordSpecial],
                ];
                $orderMerge = array_merge($specs1, $specs2);
                $allocOrdersDataHash[$_jobId] = $orderMerge;
            }
            else {
                $specs = [
                    $order[$i_ordJobId],
                    $order[$i_ordSku],
                    $order[$i_ordDes],
                    $order[$i_ordSpecial],
                ];
                $allocOrdersDataHash[$_jobId] = $specs;
            }
        }
        
        unset($allocOrdersData);
        unset($order);
        
        // OUTER-LOOP_2 join the Allocadence data to the Job Board data
        foreach($jobBoardData as $i => $job) {
            $_jobId = $job[$f['job_id']];
            
            // Even if the needed fields are not empty the SKU is more accurate
            // so append SKU info to the field.
            $allocInfo = $allocOrdersDataHash[$_jobId] ?? null;
            if($allocInfo) {
                $allocInfoCount = count($allocInfo);
                $allocItems = ["alloc_job_id" => $allocInfo[0]];
                
                /** START THE ALLOC RE-MAP **/
                
                // INNER-LOOP: dynamically create 1 to N groups of 3 to be
                //  appended as the last field for the CSV for Accounting
                for($i = 1, $c = 1; $i < $allocInfoCount; $i += 3, $c++) {
                    $sku = $allocInfo[$i];
                    $description = $allocInfo[$i + 1];
                    $specials = $allocInfo[$i + 2];
                    $allocAssoc = [
                        ('sku_' . $c) => $sku,
                        ('description_' . $c) => $description,
                        ('specials_' . $c) => $specials,
                    ];
                    $allocItems = array_merge($allocItems, $allocAssoc);
                    
                    // while debugging having all these vars defined clutters the debug panel
                    unset($sku);
                    unset($description);
                    unset($specials);
                    unset($allocAssoc);
                }
                
                // IMPORTANT - integrate Alloc data to CSV for Accounting Job Record
                $job = AllocParser::allocRemap($job, $f, $allocItems);
                $this->allocJobCount++;
            }
            // ELSE "it's not an allocadence job"
            else {
                unset($sku);
                unset($description);
                unset($specials);
                unset($allocAssoc);
                $job [] = 'Job did\'t use the inventory module.';
            }
            
            $joinedData [] = $job;
            
            // clear vars from prior iteration
            unset($allocInfo);
            unset($allocItems);
            
        } // end of OUTER-LOOPING over csv for accounting data
        
        $idxAlloc = (count($joinedData[0]) - 1);
        
        // add the field title for the appended alloc info
        $joinedData[0][$idxAlloc] = 'allocadence_php_info';
        
        // log the prior op
        AppGlobals::rsLogInfo('Allocadence Orders and Inventory array created ~DataJoin.php');
        
        return $joinedData;
    
    } // END OF: allocadenceJoin()
    
    /**
     * Sometimes the order of columns change, so this function will dynamically
     * find the column order for all fields
     *
     * @var $headerRow array - every time the program finds the columns order the
     *              most recent header to scan must also be passed
     *
     * @return array
     */
    private function findColumnOrder(array $headerRow): array {
        // header row must be initialized before findColumnOrder
        $this->headerRow = $headerRow;
        
        // variable initialization
        $indexesFound /** assoc.arr **/ = [];
        $justFindParticularIndexes = false;
        
        // the else statement will probably never happen
        if(!$justFindParticularIndexes) {
            // Dynamically find the column order
            //TODO: make an exception handler in case any field indexes can't be found !! SERIOUSLY !!!!!!!!
            for($i = 0; $i < count($this->headerRow); $i++) {
                $field = $this->headerRow[$i];
                $indexesFound[$field] = array_search($field, $this->headerRow);
            }
        }
        
        $this->indexFor = $indexesFound;
        
        return $indexesFound;
        
    } // END OF: findColumnOrder()
    
}