<?php

ini_set('memory_limit','3048M');
function __autoload($className) {
    $filename = $className . ".php";
    if (is_readable($filename)) {
        require $filename;
    }
}
use Blacklist;
use Block;
use BlockMaster;
use BoundingBox;
use Fillable;
use Location;
use RangeLocation;
use World;

/**
 * Set to false to disable speed test to see how fast it compiles
 * @var int set to how many times your files need to be duplicated
 */
$SPEEDTEST = 0;
/**
 * By how much should the blocks be offset for the speedtest to avoid merging
 * @var unknown
 */
$SPEEDTESTOFFSET = 1;

$INDIVIDUALRANGETEST = true;
/**
 * Add blocks you wish to exclude to the blacklist
 * @var BlackList
 */
$blacklist = new BlackList();
$blacklist->addToBlackList('minecraft','dirt','0'); 
$blacklist->addToBlackList('minecraft','air','0');
$blacklist->addToBlackList('minecraft','grass','0');
$blacklist->addToBlackList('minecraft','tallgrass','0');
$blacklist->addToBlackList('minecraft','tallgrass','1');


$master = new BlockMaster();

/**
 * Add blocks you wish to transform to other blocks here
 */
$master->addTranslation(new Block('minecraft',"leaves","12"),
                new Block(["blockid"=> "block_magicleaves",
	            "metadata"=> 0,
        	    "modid"=> "magiccookies"])
                );
$master->addTranslation(new Block("minecraft",'leaves','4'),              
	            new Block(["blockid"=> "block_magicleaves",
	            "metadata"=> 0,
        	    "modid"=> "magiccookies"])
                );
$master->addTranslation(new Block('minecraft','log','0'),
	            new Block(["blockid"=> "block_magicwood",
	            "metadata"=> 1,
        	    "modid"=> "magiccookies"])
                );
$master->addTranslation(new Block('minecraft','log','8'),
	            new Block(["blockid"=> "block_magicwood",
	            "metadata"=> 2,
        	    "modid"=> "magiccookies"])
                );
$master->addTranslation(new Block('minecraft','log','4'),
	            new Block([
		            "blockid"=> "block_magicwood",
		            "metadata"=> 0,
	        	    "modid"=> "magiccookies"
	            ])
                );

/**
 * ============================================================================================
 * END OF USER INPUT! NO TOUCHY BELOW!
 * ============================================================================================
 */

function printRAMUsage() {
	static $lastusage;
	$usage = round(memory_get_usage() / 1024 / 1024,2);
	if($usage != $lastusage) {
		echo '                                                                '.($usage > $lastusage?'+':'-')." ". $usage.  'MB is used'."\n";
		$lastusage = $usage;
	}
}
$basedir = __DIR__.DIRECTORY_SEPARATOR;
$input = $basedir.'input';
$output = $basedir.'output';
$files = scandir($input);
if(!is_dir($output)) {
	mkdir($output);
}
$outputfile = $output.DIRECTORY_SEPARATOR.'output.json';

$filelist = [];
foreach($files as $file) {
	if(strpos($file,'.json')) {
		$filelist[] = $input.DIRECTORY_SEPARATOR.$file;
	}
}
if($SPEEDTEST) {
	for($c=0;$c<$SPEEDTEST;$c++) {
		foreach($filelist as $file) {
			$filelist[] = $file;
		}
	}
}
unset($files);


$master->setBlackList($blacklist);

$worldsList = [];
$ranges = [];

$seen = [];
$testoffset = 0;
$count = 0;
$length = count($filelist);
foreach($filelist as $file) {
   $world = new World();
   $world->setMaster($master);
   $filecontents = file_get_contents($file);
   $content = json_decode($filecontents);
   unset($filecontents);
   foreach($content->blocklist as $id => $item) {
   	   if(!in_array($id, $seen)) {
	   		$master->addToBlockList($id,new Block($item));
	   		$seen[] = $id;
   	   }
   }
   if(isset($content->locations)) {
       foreach($content->locations as $item) {
       		$loc = new Location($item);
       		$loc->x += $SPEEDTEST ? $testoffset:0;
           $world->addLocation($loc);
       }
    }
   unset($content);
   printRAMUsage();
   
   echo "Removing surplus airblocks with direct sky access\n";
   $cnt = $world->checkAllAirBlocksForSkyAccess();
   echo "Removed $cnt airblocks with direct sky or void access\n";
   if($INDIVIDUALRANGETEST) {
	   	$totalBlocksHarvested = 0;
	   	while($loc = $world->getFirstAvailableRange()) {
	   	
	   		if($range = $loc->findBiggestRange($world)) {
	   	
	   			$totalBlocksHarvested += $range->getCubicSize();
	   			$rangeloc = new RangeLocation();
	   			$rangeloc->fill(['startx'=>$range->startx,'starty'=>$range->starty,'startz'=>$range->startz,'endx'=>$range->endx,'endy'=>$range->endy,'endz'=>$range->endz,'block'=>$loc->block]);
	   			$world->deleteBlocks($range);
	   			$ranges[] = $rangeloc;
	   		}
	   		gc_collect_cycles();
	   		printRAMUsage();
	   	
	   	}
	   	
	   	echo "Range checks completed, total of $totalBlocksHarvested blocks were put into ranges\n";
	   	printRAMUsage();
   }
   $worldsList[] = $world;
   gc_collect_cycles();
   printRAMUsage();
   $testoffset += $SPEEDTESTOFFSET;
   $count++;
   echo "Finished file $count of $length\n";
}
echo "===========================================================================";
echo "                         MERGING ALL FILES";
echo "===========================================================================";
$world = new World();
$world->setMaster($master);
foreach($worldsList as $item) {
	$list =  $item->getRemainingBlocks();
	foreach($list as $location) {
		$world->addLocation($location);
	}
}
unset($worldsList);
gc_collect_cycles();
printRAMUsage();
echo "";
echo "===========================================================================";
echo "                         CHECKING RANGES ON MAIN";
echo "===========================================================================";

$totalBlocksHarvested = 0;
while($loc = $world->getFirstAvailableRange()) {
	
	if($range = $loc->findBiggestRange($world)) {
		
		$totalBlocksHarvested += $range->getCubicSize();
		$rangeloc = new RangeLocation();
		$rangeloc->fill(['startx'=>$range->startx,'starty'=>$range->starty,'startz'=>$range->startz,'endx'=>$range->endx,'endy'=>$range->endy,'endz'=>$range->endz,'block'=>$loc->block]);
		$world->deleteBlocks($range);
		$ranges[] = $rangeloc;
	}
	gc_collect_cycles();
	printRAMUsage();
	
}

echo "Range checks completed, total of $totalBlocksHarvested blocks were put into ranges\n";
printRAMUsage();

$locations = $world->getRemainingBlocks();
echo "Preparing final save\n";
unset($world);
gc_collect_cycles();
printRAMUsage();
echo "Adding total of ".count($ranges)." block ranges\n";
foreach($ranges as $range) {
$master->addToLocationList($range);
}
// Add all locations
foreach($locations as $location) {
   $master->addToLocationList($location);
}
unset($locations);
unset($ranges);
echo "All data inserted into master\n";
gc_collect_cycles();
printRAMUsage();
$master->compileLocations();

file_put_contents($outputfile,json_encode($master,JSON_PRETTY_PRINT));
echo "File conversion completed";
//echo ;
