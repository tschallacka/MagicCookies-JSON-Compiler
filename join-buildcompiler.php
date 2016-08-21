<?php

ini_set('memory_limit','3048M');
function __autoload($className) {
    $filename = $className . ".php";
    if (is_readable($filename)) {
        require $filename;
    }
}
require_once 'Blacklist.php';
require_once 'Block.php';
require_once 'BlockMaster.php';
require_once 'BoundingBox.php';
require_once 'Fillable.php';
require_once 'Location.php';
require_once 'RangeLocation.php';
require_once 'World.php';

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
for($c=0;$c<16;$c++) {
$blacklist->addToBlackList('minecraft','air',''.$c);
}
/*$blacklist->addToBlackList('minecraft','dirt','0'); 
$blacklist->addToBlackList('minecraft','air','0');
$blacklist->addToBlackList('minecraft','grass','0');
$blacklist->addToBlackList('minecraft','tallgrass','0');
$blacklist->addToBlackList('minecraft','tallgrass','1');*/


$master = new BlockMaster();

/**
 * Add blocks you wish to transform to other blocks here
 */
$master->addTranslation(new Block('Thaumcraft','blockWoodenDevice','0'),
                        new Block('thaumcraft','bellows','0'));

$master->addTranslation(new Block('Thaumcraft','blockMetalDevice','0'),
                        new Block('thaumcraft','crucible','0'));

$master->addTranslation(new Block('MagicCookie','blockOldDarkStoneStairs','1'),
                        new Block('magiccookies','blockstairs_darkbrick','1'));

$master->addTranslation(new Block('MagicCookie','blockOldDarkStoneStairs','2'),
                        new Block('magiccookies','blockstairs_darkbrick','2'));

$master->addTranslation(new Block('Thaumcraft','blockMetalDevice','2'),
                        new Block('thaumcraft','arcane_workbench','0'));

$master->addTranslation(new Block('Thaumcraft','blockAiry','1'),
                        new Block('thaumcraft','nitor','0'));

$master->addTranslation(new Block('Thaumcraft','blockTube','0'),
                        new Block('thaumcraft','tube','0'));

$master->addTranslation(new Block('Thaumcraft','blockTube','1'),
                        new Block('thaumcraft','tube','1'));

$master->addTranslation(new Block('Thaumcraft','blockTube','2'),
                        new Block('thaumcraft','tube','2'));

$master->addTranslation(new Block('Thaumcraft','blockTube','3'),
                        new Block('thaumcraft','tube','3'));

$master->addTranslation(new Block('Thaumcraft','blockTube','4'),
                        new Block('thaumcraft','tube','4'));

$master->addTranslation(new Block('Thaumcraft','blockTube','5'),
                        new Block('thaumcraft','tube','5'));

//$master->addTranslation(new Block('','',''),
//                        new Block('thaumcraft','crystallizer','0'));

//$master->addTranslation(new Block('Thaumcraft','','5'),
//                        new Block('thaumcraft','centrifuge','0'));

$master->addTranslation(new Block('Thaumcraft','blockMagicalLog','0'),
                        new Block('thaumcraft','log','0'));
$master->addTranslation(new Block('Thaumcraft','blockMagicalLog','1'),
                        new Block('thaumcraft','log','1'));
$master->addTranslation(new Block('Thaumcraft','blockMagicalLog','2'),
                        new Block('thaumcraft','log','0'));
$master->addTranslation(new Block('Thaumcraft','blockMagicalLog','3'),
                        new Block('thaumcraft','log','1'));
$master->addTranslation(new Block('Thaumcraft','blockMagicalLog','4'),
                        new Block('thaumcraft','log','0'));
$master->addTranslation(new Block('Thaumcraft','blockMagicalLog','5'),
                        new Block('thaumcraft','log','1'));
$master->addTranslation(new Block('Thaumcraft','blockMagicalLog','6'),
                        new Block('thaumcraft','log','0'));
$master->addTranslation(new Block('Thaumcraft','blockMagicalLog','7'),
                        new Block('thaumcraft','log','1'));
$master->addTranslation(new Block('Thaumcraft','blockMagicalLog','8'),
                        new Block('thaumcraft','log','0'));
$master->addTranslation(new Block('Thaumcraft','blockMagicalLog','9'),
                        new Block('thaumcraft','log','1'));
$master->addTranslation(new Block('Thaumcraft','blockMagicalLog','10'),
                        new Block('thaumcraft','log','0'));
$master->addTranslation(new Block('Thaumcraft','blockMagicalLog','11'),
                        new Block('thaumcraft','log','1'));
$master->addTranslation(new Block('Thaumcraft','blockMagicalLog','12'),
                        new Block('thaumcraft','log','0'));
$master->addTranslation(new Block('Thaumcraft','blockMagicalLog','13'),
                        new Block('thaumcraft','log','1'));
$master->addTranslation(new Block('Thaumcraft','blockMagicalLog','14'),
                        new Block('thaumcraft','log','0'));
$master->addTranslation(new Block('Thaumcraft','blockMagicalLog','15'),
                        new Block('thaumcraft','log','1'));


$master->addTranslation(new Block('Thaumcraft','blockMetalDevice','8'),
                        new Block('thaumcraft','lamp_growth','0'));

$master->addTranslation(new Block('Thaumcraft','blockTable','15'),
                        new Block('thaumcraft','node_stabilizer','0'));

$master->addTranslation(new Block('Thaumcraft','blockMirror','0'),
                        new Block('thaumcraft','mirror','0'));
$master->addTranslation(new Block('Thaumcraft','blockMirror','1'),
                        new Block('thaumcraft','mirror','1'));
$master->addTranslation(new Block('Thaumcraft','blockMirror','2'),
                        new Block('thaumcraft','mirror','2'));
$master->addTranslation(new Block('Thaumcraft','blockMirror','3'),
                        new Block('thaumcraft','mirror','3'));
$master->addTranslation(new Block('Thaumcraft','blockMirror','4'),
                        new Block('thaumcraft','mirror','4'));
$master->addTranslation(new Block('Thaumcraft','blockMirror','5'),
                        new Block('thaumcraft','mirror','5'));

$master->addTranslation(new Block('Thaumcraft','blockMirror','6'),
                        new Block('thaumcraft','mirror_essentia','0'));
$master->addTranslation(new Block('Thaumcraft','blockMirror','7'),
                        new Block('thaumcraft','mirror_essentia','1'));
$master->addTranslation(new Block('Thaumcraft','blockMirror','8'),
                        new Block('thaumcraft','mirror_essentia','2'));
$master->addTranslation(new Block('Thaumcraft','blockMirror','9'),
                        new Block('thaumcraft','mirror_essentia','3'));
$master->addTranslation(new Block('Thaumcraft','blockMirror','10'),
                        new Block('thaumcraft','mirror_essentia','4'));
$master->addTranslation(new Block('Thaumcraft','blockMirror','11'),
                        new Block('thaumcraft','mirror_essentia','5'));


$master->addTranslation(new Block('Thaumcraft','blockMetalDevice','8'),
                        new Block('thaumcraft','lamp_fertility','0'));


$master->addTranslation(new Block('Thaumcraft','blockMetalDevice','2'),
                        new Block('thaumcraft','arcane_workbench_charger','0'));

$master->addTranslation(new Block('Thaumcraft','blockTable','0'),
                        new Block('thaumcraft','research_table','0'));
$master->addTranslation(new Block('Thaumcraft','blockTable','1'),
                        new Block('thaumcraft','research_table','1'));
$master->addTranslation(new Block('Thaumcraft','blockTable','2'),
                        new Block('thaumcraft','research_table','2'));
$master->addTranslation(new Block('Thaumcraft','blockTable','3' ),
                        new Block('thaumcraft','research_table','3'));
$master->addTranslation(new Block('Thaumcraft','blockTable','4' ),
                        new Block('thaumcraft','research_table','4'));
$master->addTranslation(new Block('Thaumcraft','blockTable','5' ),
                        new Block('thaumcraft','research_table','5'));

for($c=0;$c<16;$c++) {
    $master->addTranslation(new Block('magiccookies','blockDarkStone',"$c"),
                        new Block('magiccookies','block_darkstone',"$c"));
}
$master->addTranslation(new Block('Thaumcraft','blockJar','0' ),
                        new Block('thaumcraft','jar','0'));
$master->addTranslation(new Block('Thaumcraft','blockJar','1' ),
                        new Block('thaumcraft','jar','1'));
$master->addTranslation(new Block('Thaumcraft','blockJar','2' ),
                        new Block('thaumcraft','jar','2'));
$master->addTranslation(new Block('Thaumcraft','blockJar','3' ),
                        new Block('thaumcraft','jar','3'));

//$master->addTranslation(new Block('Thaumcraft','blockMagicPlank','9'),
//                        new Block('thaumcraft','plank','0'));

//$master->addTranslation(new Block('Thaumcraft','blockMagicPlank','9'),
//                        new Block('thaumcraft','plank','1'));

$master->addTranslation(new Block('','',''),
                        new Block('','',''));
/*
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
*/
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
