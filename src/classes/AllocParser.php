<?php
/**
 * Created by PhpStorm.
 * User: julius
 */
declare(strict_types=1);

namespace Redstone\Auto;


/**
 * Class AllocParser will convert Allocadence info & calculate allocadence costs
 *
 * @package Redstone\Auto
 */
class AllocParser
{
    /**
     * Manually check every sku and return The Redstone Master Price Matrix costs
     *
     * Envelope SKU form usually:
     * [JobType][Size][Color] - [Window] - [Class] - [Indicia]
     *
     * @param string $sku
     *
     * @return array
     */
    private function skuMap(string $sku): array {
        // normalize and sanitize a bit the sku
        $sku = trim(strtoupper($sku));

        // E* = envelope
        // P* = paper
        // PS* = snap pack

        // if the 1st letter is an 'E' it is probably an envelope
        if ($sku[0] === 'E') {
            switch ($sku) {
                case 'E10BK-0W':
                    $info = '#10 Envelope, Brown Kraft, No window.';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10BK-1W':
                    $info = '#10 Envelope, Brown Kraft, single window';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10BK-1W-FC-BOX':
                    $info = 'first class box indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10BK-1W-FC-EAG':
                    $info = 'first class eagle indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10BK-1W-KC WORLD/RELIEF':
                    // What is "KC WORLD/RELIEF"
                    $i = 'single window, special indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10BK-1W-RCDL':
                    $i = 'special RCDL indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10BK-1W-SC':
                    $i = 'E10BK-1W-SC';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10BK-1W-SC- BOX':
                    $i = 'E10BK-1W-SC- BOX';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10BK-1W-SC-EAG':
                    $i = 'E10BK-1W-SC-EAG';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10BK-2W':
                    $i = 'E10BK-2W';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10BK-HW-FC1935':
                    $i = 'E10BK-HW-FC1935';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10BK-PW':
                    $i = 'E10BK-PW';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10GR-1W':
                    $i = 'E10GR-1W';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10GR-1W-FC-BOX':
                    $i = 'E10GR-1W-FC-BOX';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10GR-1W-SC-BOX':
                    $i = 'standard class? E10GR-1W-SC-BOX';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10GR-1W-SEMPER FC BOX 1935':
                    $i = 'indicia Semper? E10GR-1W-SEMPER FC BOX 1935';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10GR-PW':
                    $i = 'green envelope, E10GR-PW';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10GR-PW-FC-BOX':
                    $i = 'E10GR-PW-FC-BOX';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10GR-PW-SC-BOX':
                    $i = 'standard class box indicia, E10GR-PW-SC-BOX';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-0W':
                    $i = 'no window, E10WH-0W';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-1W':
                    $i = 'single window, E10WH-1W';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-1W-FC - BOX':
                    $i = 'single window first class box indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-1W-FC â€“ EAG':
                    // supposed to be: E10WH-1W-FC – EAG
                    $i = 'first class single window eagle indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-1W-FC – EAG':
                    $i = 'unencoded version of E10WH-1W-FC â€“ EAG';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-1W-FC1946':
                    $i = 'single window first class 1946 indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-1W-PaulPistey':
                    $i = 'Paul Pitstey indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-1W-SC-BOX':
                    $i = 'basic envelope with a box indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-1W-SC-EAG':
                    $i = 'basic envelope with an eagle indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-1W-SC1908 LKYDUCHESS':
                    $i = 'I think a standard class 1908 LKYDUCHESS indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-2W':
                    $i = 'a basic envelope w/ double window I believe';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-2W-FC-BOX':
                    $i = 'same as above but a first class box indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-2W-FC-EAG':
                    $i = 'same as above but with an eagle indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-2W-FC1946':
                    $i = 'double window first class 1946 indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-2W-MonsterTFSB':
                    $i = 'a special envelope for monster';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-2W-NP1935-EAG':
                    $i = 'non profit 1935 eagle indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-2W-SC-BOX':
                    $i = 'double window standard box indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-2W-SC-EAG':
                    $i = 'same as above but an eagle indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-2W-SC1935 Smartbiz DW Cust':
                    $i = 'custom envelope for Smartbiz';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-2W-YCRMichigan':
                    $i = 'custom envelope for YCRMichigan';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-2WCSB-FC1946':
                    $i = 'a double window center side by'; // ?
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-FULLW':
                    $i = 'a full window envelope';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-HW':
                    $i = 'a half window = single window that takes up 1/2 the envelope';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-HW-FC-BOX':
                    $i = 'same as above but with a box indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-HW-FC-EAG':
                    $i = 'same as above but w/ an eagle indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-HW-FC1946':
                    $i = 'same as above but with a 1946 indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-HW-SC-BOX':
                    $i = 'same as above but standard class box indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-HW-SC-EAG':
                    $i = 'half window standard class eagle indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-OSHW-FC1946':
                    $i = 'oversized half window first class 1946 indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-PW':
                    $i = 'a pistol window';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-PW-FC-BOX':
                    $i = 'a pistol window first class box indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-PW-FC-EAG':
                    $i = 'a pistol window first class eagle indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-PW-FC1946':
                    $i = 'a pistal window first class 1946 indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-PW-SC-BOX':
                    $i = 'a standard class';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E10WH-PW-SC-EAG':
                    $i = 'pistal window standard class eagle indicia';
                    return $this->EnvelopeSkuAnalyzer();
                /*
                 * All 6x9 envelopes probably require the "Inserting Oversized envelopes" cost
                 * i.e. cost_id = 9
                 */
                case 'E695BK-0W':
                    // will probably require a precanceled stamp to be applied
                    $i = '6x9.5 brown kraft no window';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E69BK-1WBBL-FC1946':
                    $i = 'a 6x9 monster bubble brown kraft first class 1946 indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E69BK-2WSB-FC1946':
                    $i = 'a 6x9 brown kraft double window side-by';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E69WH-0W':
                    $i = 'a white 6x9 with no window';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E69WH-1W':
                    // probably requires "Apply Pre-Canceled Stamps" added, cost_id = 25 & 26
                    $i = 'a white 6x9 single window, no indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E69WH-2W':
                    // probably requires "Apply Pre-Canceled Stamps" added, cost_id = 25 & 26
                    $i = 'a white 6x9 double window, no indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E69WH-2W-FC1946':
                    $i = 'a white 6x9 double window first class 1946 indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E69WH-2W-SC1935-Box':
                    $i = 'a 6x9 white double window standard class 1935 box indicia';
                    return $this->EnvelopeSkuAnalyzer();
                /*
                 * What does #9 mean exactly? An envelope with less length?
                 * Maybe a business reply envelope for the most part.
                 */
                case 'E9WH-0W':
                    // probably requires cost_id = 25 & 26
                    $i = 'a white #9 blank envelope, no indicia';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E9WH-0W-BRM-CW7-AIM-AULTIUM':
                    $i = 'a regular business reply mail envelope';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E9WH-1W':
                    $i = 'white #9 single window envelope';
                    return $this->EnvelopeSkuAnalyzer();
                case 'E9WH-2WCSB-FC1946':
                    $i = 'a confidential double window center';
                    return $this->EnvelopeSkuAnalyzer();
                case 'EA7WH-0W':
                    // no clue what this is, but it probably requires cost_id = 25 & 26
                    $i = 'non window white envelope probably a special envelope';
                    return $this->EnvelopeSkuAnalyzer();
                default:
                    return ['error' => "ERROR - could not map envelope SKU: $sku"];
            }
        }

        // if the 1st 2 letters are 'PS'
        else if (($sku[0] . $sku[1]) === 'PS') {
            $break = 'point'; // manually check this
            switch ($sku) {
                case 'PS8511- WHT- HF':
                    $i = '8.5x11 white half fold, pressure sealed?';
                    return $this->SnapPackSkuAnalyzer();
                case 'PS8511-BLKWHT- CF':
                    $i = '8.5x11 black & white C-fold';
                    return $this->SnapPackSkuAnalyzer();
                case 'PS8511-BLKWHT-HF':
                    $i = '8.5x11 black and white half fold';
                    return $this->SnapPackSkuAnalyzer();
                case 'PS8511-BLKWHT-ZF':
                    $i = '8.5x11 black&white Z-fold, pressure sealed?';
                    return $this->SnapPackSkuAnalyzer();
                case 'PS8511-BLWDO':
                    // I think this is a Z-fold or C-fold
                    $i = '8.5x11 blue window, pressure sealed?';
                    return $this->SnapPackSkuAnalyzer();
                case 'PS8511-GRWDO':
                    // I think it's a Z-fold or C-fold
                    $i = '8.5x11 green window, pressure sealed?';
                    return $this->SnapPackSkuAnalyzer();
                case 'PS8511-WH-ZF':
                    $i = '8.5x11 white over white Z-fold, pressure sealed?';
                    return $this->SnapPackSkuAnalyzer();
                case 'PS8514- GRCK- CAPITOL':
                    $i = '8.5x14 green check capitol';
                    return $this->SnapPackSkuAnalyzer();
                case 'PS8514-BL-ZF':
                    $i = '8.5x14 blue Z-fold, pressure sealed?';
                    return $this->SnapPackSkuAnalyzer();
                case 'PS8514-WH-ZF':
                    $i = '8.5x14 white Z-fold, pressure sealed?';
                    return $this->SnapPackSkuAnalyzer();
                default:
                    return ['error' => "ERROR - could not map Snap Pack SKU $sku"];
            }
        }

        // if app gets to this else-if block it should not be paper
        else if ($sku[0] === 'P') {
            $break = 'point'; // manually check this
            switch ($sku) {
                /*
                 * I think all 11x17, 13x19, 25.5x35.5, etc paper are postcard jobs
                 */
                case 'P1117-60WH':
                    $i = 'white 11x17 paper';
                    return $this->PaperSkuAnalyzer();
                case 'P1117-70CA-V':
                    // does vellum cost more?
                    $i = 'canary vellum paper';
                    return $this->PaperSkuAnalyzer();
                case 'P1117-70WH':
                    $i = 'white paper';
                    return $this->PaperSkuAnalyzer();
                case 'P1117-70WH-V':
                    $i = 'white vellum paper';
                    return $this->PaperSkuAnalyzer();
                case 'P1117-80SO-C':
                    $i = 'smooth opaque cover';
                    return $this->PaperSkuAnalyzer();
                case 'P1218-100WHGL-C':
                    $i = 'white gloss cover';
                    return $this->PaperSkuAnalyzer();
                case 'P1218-100WHGL-T':
                    $i = 'white gloss text';
                    return $this->PaperSkuAnalyzer();
                case 'P1218-60WH':
                    $i = 'basic 12x18 white paper';
                    return $this->PaperSkuAnalyzer();
                case 'P1218-67CA-V-CT':
                    $i = '67# canary vellum cut';
                    return $this->PaperSkuAnalyzer();
                case 'P1218-67GR-V-CT':
                    $i = '67# green vellum cut';
                    return $this->PaperSkuAnalyzer();
                case 'P1218-67WH-V-CT':
                    $i = 'white vellum cut';
                    return $this->PaperSkuAnalyzer();
                case 'P1218-80WHGL-C':
                    $i = '80# white gloss cover';
                    return $this->PaperSkuAnalyzer();
                case 'P1218-80WHGL-T':
                    $i = '80# white gloss text';
                    return $this->PaperSkuAnalyzer();
                case 'P1319-100WHGL-C':
                    $i = '13x19 white gloss cover';
                    return $this->PaperSkuAnalyzer();
                case 'P1319-100WHGL-T':
                    $i = '13x19 white gloss text';
                    return $this->PaperSkuAnalyzer();
                case 'P1319-80WH-SO-C':
                    $i = '13x19 80# smooth opaque cover';
                    return $this->PaperSkuAnalyzer();
                case 'P1319-80WHGL-C':
                    $i = '13x19 80# white gloss cover';
                    return $this->PaperSkuAnalyzer();
                case 'P1319-80WHGL-T':
                    $i = '13x19 80# white gloss text';
                    return $this->PaperSkuAnalyzer();
                case 'P2335-65MGR-C':
                    $i = '23x35 65# martian green dover';
                    return $this->PaperSkuAnalyzer();
                case 'P2335-80WH-OS':
                    // I'm not sure what offset means?
                    $i = '23x35 80# white offset';
                    return $this->PaperSkuAnalyzer();
                case 'P255355-110BF-C':
                    $i = '25.5x35.5 100# exact buff cover/canary';
                    return $this->PaperSkuAnalyzer();
                case 'P2640-67CA-V':
                    $i = '26x40 67# canary vellum';
                    return $this->PaperSkuAnalyzer();
                case 'P2640-67GR-V':
                    $i = '26x40 67# green vellum';
                    return $this->PaperSkuAnalyzer();
                case 'P2640-67WH-V':
                    $i = '26x40 67# white vellum';
                    return $this->PaperSkuAnalyzer();
                case 'P2649-65OR-C':
                    $i = '26x49 65# cosmic orange cover';
                    return $this->PaperSkuAnalyzer();
                case 'P475-80GL-T-Smartbiz BT Inst ':
                    $i = '4x7.5 Smartbiz BankTerm insert';
                    return $this->PaperSkuAnalyzer();
                case 'P67WH2640':
                    $i = '67# white vellum 26x40';
                    return $this->PaperSkuAnalyzer();
                case 'P8511-100WH-T':
                    $i = '8.5x11 100# white text';
                    return $this->PaperSkuAnalyzer();
                /*
                 * Standard Letter sizes:
                 */
                case 'P8511-20BL':
                    $i = '8.5x11 20# blue';
                    return $this->PaperSkuAnalyzer();
                case 'P8511-20CA':
                    $i = '8.5x11 20# canary';
                    return $this->PaperSkuAnalyzer();
                case 'P8511-20GR':
                    $i = '8.5x11 20# green';
                    return $this->PaperSkuAnalyzer();
                case 'P8511-20PI':
                    $i = '8.5x11 20# pink';
                    return $this->PaperSkuAnalyzer();
                case 'P8511-20WH':
                    $i = '8.5x11 20# white';
                    return $this->PaperSkuAnalyzer();
                case 'P8511-60-YCMHeartland-V2':
                    $i = '8.5x11 60# YMCHeartland shell version 2';
                    return $this->PaperSkuAnalyzer();
                case 'P8511-60CR':
                    $i = '8.5x11 60# cream';
                    return $this->PaperSkuAnalyzer();
                case 'P8511-60GR':
                    $i = '8.5x11 60# green';
                    return $this->PaperSkuAnalyzer();
                case 'P8511-60WH':
                    $i = '8.5x11 60# white';
                    return $this->PaperSkuAnalyzer();
                case 'P8511-65NAT- C':
                    $i = '8.5x11 65# natural cover';
                    return $this->PaperSkuAnalyzer();
                case 'P8511-70WH':
                    $i = '8.5x11 67.5#(70#) white';
                    return $this->PaperSkuAnalyzer();
                case 'P8511-80GL-T PERFECT STAR NOV':
                    // pre-printed shell
                    $i = '8.5x11 80# gloss text Perfect Start November letter?';
                    return $this->PaperSkuAnalyzer();
                case 'P8511-80GL-T-Perfect Star Inst':
                    // pre-printed shell
                    $i = '8.5x11 80# gloss text Perfect Start Insert';
                    return $this->PaperSkuAnalyzer();
                case 'P8511-WH60-YCR HEA-CTY FOLLOW':
                    $i = '8.5x11 white 60# YCR Heartland/City follow up shells';
                    return $this->PaperSkuAnalyzer();
                case 'P8511-WH60-YCR HEA-CTY V2':
                    $i = '8.5x11 white 60# YCR Heartland City Square shell version2';
                    return $this->PaperSkuAnalyzer();
                case 'P8514-20WH':
                    $i = '8.5x14 20# white';
                    return $this->PaperSkuAnalyzer();
                case 'P8514-60-Smartbiz Shell':
                    $i = '8.5x14 60# SmartBiz shell';
                    return $this->PaperSkuAnalyzer();
                case 'P8514-60GR':
                    $i = '8.5x14 60# green';
                    return $this->PaperSkuAnalyzer();
                case 'P8514-60WH':
                    $i = '8.5x14 60# white';
                    return $this->PaperSkuAnalyzer();
                default:
                    return ['error' => "ERROR - count not map Paper SKU: $sku"];
            }
        }
        else {
            // something went really wrong
            return ['error' => "ERROR - program does not recognize SKU: $sku"];
        }
    }

    //TODO: implement app logic
    private function EnvelopeSkuAnalyzer(): array {
        $rsPriceMatrixCosts = [];

        //-- 1st simple logic:

        return $rsPriceMatrixCosts;
    }
    
    //TODO: implement app logic
    private function PaperSkuAnalyzer(): array {
        $rsPriceMatrixCosts = [];

        //-- 1st simple logic:

        return $rsPriceMatrixCosts;
    }
    
    //TODO: implement app logic
    private function SnapPackSkuAnalyzer(): array {
        $rsPriceMatrixCosts = [];

        //-- 1st simple logic:

        return $rsPriceMatrixCosts;
    }

    /**
     * Fill the missing Job Board fields for:
     * [papertype,paper_color,envelopepaer,envelopetype,]
     *
     * @param array $jobRec - the current rec being scanned from the csv for accounting
     * @param array $f - the fields for the current job record
     * @param array $allocItems - the group of 3 alloc array
     *
     * @return array - the re-mapped job board record from the CSV for Accounting
     */
    public static function allocRemap(array $jobRec, array $f, array $allocItems): array {
        // ENUM for the name of needed field titles (enables better code completion)
        $t = new class () // t = title
        {
            public $paperColor = 'paper_color';
            public $paperType = 'papertype';
            public $envelopeType = 'envelopetype';
            public $envelopePaper = 'envelopepaper';
            public $snapPaper = 'snap_paper';
            public $snapSeal = 'snap_seal';
        };

        // sort fields by alphabetical order to find keys faster while debugging
        ksort($f);

        // function field initializations
        $job = $jobRec;
        $job [] = json_encode($allocItems);
        $jobPaperColor = &$job[$f[$t->paperColor]];
        $jobPaperType = &$job[$f[$t->paperType]];
        $jobEnvelopeType = &$job[$f[$t->envelopeType]];
        $jobEnvelopePaper = &$job[$f[$t->envelopePaper]];

        // alloc info & simple utility scalars
        $allocJobId = array_shift($allocItems);
        if($allocJobId == '80236' || $allocJobId == '80235') {
            $break = 'point';
        }
        $allocGroup = array_chunk($allocItems, 3, true);
        $ee = ' | Expensive Envelope';
        $ep = ' | Expensive Paper';
        $c = 1;
        $w = 'white';
        $sw = 'single window';
        $p20 = '20#';
        $p60 = '60#';
        $n10 = '10';

        // OUTER-LOOP over the grouped alloc items
        foreach ($allocGroup as $item) {
            $sku = $item["sku_$c"];

            $E = ($sku[0] == 'E') ? 'E' : null;
            $PS = $E ? null : (($sku[0] . $sku[1]) === 'PS') ? 'PS' : null;
            $P = $PS ? null : $sku[0] === 'P' ? 'P' : null;

            //-- Envelope STATE --\\
            // E[envelope_number][envelope_color]-[window_type]-[postage_class]-[indicia_type]
            if ($E) {
                $part = explode('-', $sku);
                // color = 'envelope type'
                $skuEnvelopeColor = ($sku[3] . $sku[4]);
                // number = 'envelope type'
                $skuEnvelopeNum = ($sku[1] . $sku[2]);
                // window = 'envelope type'
                $skuEnvelopeWindow = $part[1];

                // re-map 'envelope type'
                if ($skuEnvelopeColor == 'WH') {
                    // the comma "," will indicate multi-insert in the calc* functions
                    $jobEnvelopePaper = $jobEnvelopePaper ? "$jobEnvelopePaper | $w" : $w;
                }
                else {
                    $jobEnvelopePaper .= "$ee ($sku)";
                }

                // re-map 'envelope paper'
                if ($skuEnvelopeNum == '10') {
                    // the comma "," will indicate multi-insert in the calc* functions
                    $jobEnvelopeType = $jobEnvelopeType ? "$jobEnvelopeType | $n10" : $n10;
                }
                else if($skuEnvelopeWindow == '1W') {
                    $jobEnvelopeType = $jobEnvelopeType ? "$jobEnvelopeType | $sw": $sw;
                }
                else {
                    $jobEnvelopeType .= "$ee ($sku)";
                }

                // free memory from buffer
                unset($skuEnvelopeColor);
                unset($skuEnvelopeNum);
                unset($skuEnvelopeWindow);
                unset($part);
            }

            //-- Paper STATE --\\
            // SKU form: P[paper_size]-[paper_thickness][paper_color]-[paper_type]
            else if ($P) {
                $part = explode('-', $sku);
                $skuPaperType = ($part[1][0] . $part[1][1]);
                $skuPaperColor = ($part[1][-2] . $part[1][-1]);

                // re-map 'paper color'
                if ($skuPaperColor === 'WH') {
                    // the comma "," will indicate multi-insert in the calc* functions
                    $jobPaperColor = $jobPaperColor ? "$jobPaperColor | $w" : $w;
                }
                else {
                    $jobPaperColor .= "$ep ($sku)";
                }

                // re-map 'paper type'
                if ($skuPaperType === '20') {
                    // the comma "," will indicate multi-insert in the calc* functions
                    $jobPaperType = $jobPaperType ? "$jobPaperType | $p20" : $p20;
                }
                else if ($skuPaperType === '60') {
                    // the comma "," will indicate multi-insert in the calc* functions
                    $jobPaperType = $jobPaperType ? "$jobPaperType | $p60" : $p60;
                }
                else {
                    $jobPaperType .= "$ep ($sku)";
                }

                // free memory from buffer
                unset($skuPaperColor);
                unset($skuPaperType);
                unset($part);
            }

            //-- Pressure Seal STATE --\\
            // PS8511-BLWDO
            else if ($PS) {
                $break = 'point';
            }

            //-- BROKEN STATE --\\
            else {
                echo "Something went MASSIVELY wrong, the program should NOT be here!!!";
                AppGlobals::LogComAutoInfo('MULTI-INSERT logic broke >:( ~ctrl-f __DAMN in AllocadenceAnalyzer.php');
            }

            $c++;

        } // end of outer-looping over alloc groups

        return $job;

    } // END OF: static allocRemap()
}