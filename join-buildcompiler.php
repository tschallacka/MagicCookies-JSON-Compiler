<?php
ini_set('memory_limit','3048M');
if(!isset($files)) {
    $files = scandir(__DIR__);
}
$outputfile = 'output.json';

$filelist = [];
foreach($files as $file) {
    if(strpos($file,'.json') && $file != $outputfile) {
       $filelist[] = $file;
    }
}
Class Block {
   public $blockid;
   public $metadata;
   public $modid;
}
Class BlackList {
    private $blacklist = [[[]]];
    public function isInBlackList($modid,$blockid,$metadata) {
        return isset($this->blacklist[$modid][$blockid][$metadata]) && $this->blacklist[$modid][$blockid][$metadata];
    }
    public function addToBlackList($modid,$blockid,$metadata) {
        if(!$this->isInBlackList($modid,$blockid,$metadata)) {
            $this->blacklist[$modid][$blockid][$metadata] = true;
        }
    }
}

Class BlockMaster {
    public $blocklist = []; 
    private $seedLocations = [];
    public  $locations = [];
    private $blacklist;
	private $translation = [[[]]];
    public function __construct() {
        $this->blacklist = new BlackList();
    }
		
    public function setBlackList(BlackList $list) {
        $this->blacklist = $list;
    }
    public function isBlackListed($location) {
        $block = $this->blocklist[$location->block];
        return $this->blacklist->isInBlackList($block->modid,$block->blockid,$block->metadata);
    }
    
    public function addToLocationList($location) {
        if(!$this->isBlackListed($location)) {
            if($location instanceof Location) {
                 $key = ''.$location->y;
             }
             else {

                $key = ''.$location->starty;
             }
             $fill = '';
            
            if(strlen($key) == 1) {
                  $fill = '00';
                }
            if(strlen($key) == 2) {
                  $fill = '0';
                }

            $key = $fill.$key;

                if(!isset($this->seedLocations[$key])) {
    	            $this->seedLocations[$key] = [];
                }
            $this->seedLocations[$key][] = $location;
        
    	}
    }
    public function compileLocations() {
       ksort($this->seedLocations);
       foreach($this->seedLocations as $key => $list) {
           
	   foreach($list as $key=>$value) {
              $this->locations[] = $value;
           }
       }
    }
    public function addTranslation($modid,$blockid,$metadata,$to) {
        $this->translation[$modid][$blockid][$metadata] = $to;
    }
    public function getTranslation($modid,$blockid,$metadata) {
        if(isset( $this->translation[$modid][$blockid][$metadata])) {
            return  $this->translation[$modid][$blockid][$metadata];
        }
        else {
            return ['modid'=>$modid,'blockid'=>$blockid,'metadata'=>$metadata];
        }
    }
    private function transform($item) {
        $to = $this->getTranslation($item->modid,$item->blockid,$item->metadata);
        $item->modid = $to['modid'];
        $item->blockid = $to['blockid'];
        $item->metadata = $to['metadata'];
        return $item;
        
    }
    public function getBlock($id) {
        if(!array_key_exists($id,$this->blocklist)) {
            return null;
        }
        else {
            return $this->blocklist[$id];
        }
    }
    public function addToBlockList($id, $item) { 
        if(!isset($this->blocklist[$id])) {
            echo "Adding ".$id."\n";
            $this->blocklist[$id] = $this->transform($item);
        }
    }
}

class Fillable {
  public function __construct($value = null) {
       if(!is_null($value)) {
           $this->fill($value);
       }
    }
  public function fill($args) {
      foreach($args as $key=>$value) {
              $this->$key = $value;
      }
      return $this;
  }
}
class Location extends Fillable {
   public function __construct($what) {
        parent::__construct($what);
        return $this;
   }
   
   public $x;
   public $y;
   public $z;
   private $deleted = false;
   private $hasBeenCheckOut = false;
   public $block = null;
   public $isRange=false;
   public $nbtcontents="";
   public function setDeleted() {
       $this->deleted = true;
   }
   public function isDeleted() {
       return $this->deleted;
   }
   public function checkout() {
	$this->hasBeenCheckOut = true;
   }
   public function isCheckedOut() {
       return $this->hasBeenCheckOut;
   }
   public function checkIfAllTheSame($list) {
       if(!count($list)) {
           return false;
       }
       foreach($list as $block) {
           if(is_null($block) || $block->block != $this->block) {
               
               return false;
           }
       }
       return true;
   }
   private $permutations = null;
   public function findBiggestRange(World $world) {
       
       $options = ['startx','starty','startz','endx','endy','endz'];
       $arr = [];
       if(is_null($this->permutations)) {
           $this->permutations =  $this->pc_permute($options);
       }
       $toparse = $this->permutations;
       
       $boundingboxes = [];
       foreach($toparse as $instructions) {
           $box = $this->findBiggestRangeAgnostic($world,$instructions[0],$instructions[1],$instructions[2],$instructions[3],$instructions[4],$instructions[5]);
           $boundingboxes[] = $box;
       }
       $biggest = 0;
       $bb = null;
       
       foreach($boundingboxes as $box) {
           if($box->getCubicSize() > $biggest) {
               $bb = $box;
               $biggest = $box->getCubicSize();
               
           }
       }
       if($bb && $bb->getCubicSize() > 1) {
           return $bb;
        }
       else {
           return null;
       }


   }
   public function pc_permute($items, $perms = [], &$ret = []) {
       if (empty($items)) {
           $ret[] = $perms;
       } else {
           for ($i = count($items) - 1; $i >= 0; --$i) {
               $newitems = $items;
               $newperms = $perms;
               list($foo) = array_splice($newitems, $i, 1);
               array_unshift($newperms, $foo);
               $this->pc_permute($newitems, $newperms,$ret);
           }
       }
       return $ret;
   }

   public function findBiggestRangeAgnostic(World $world,$startx='startx',$starty='starty',$startz='startz',$endx='endx',$endy='endy',$endz='endz') {
       $bb = new BoundingBox($this->x,$this->y,$this->z,$this->x,$this->y,$this->z);
       $check = true;

       $ix=true;
       $iy=true;
       $iz=true;
       $dx=true;
       $dy=true;
       $dz=true;
       
       while($check) {
           
           $bb->$startx += $ix ? 1:0;
           
           if($ix && !$this->checkIfAllTheSame($world->getBlocksInBB($bb))) {
               $bb->$startx -= 1;
               
               $ix = false;
           }
           
           $bb->$starty += $iy ? 1:0;
           if($iy && !$this->checkIfAllTheSame($world->getBlocksInBB($bb))) {
               $bb->$starty -= 1;
               
               $iy = false;
           }
           $bb->$startz += $iz ? 1:0;
           if($iz && !$this->checkIfAllTheSame($world->getBlocksInBB($bb))) {
               $bb->$startz -= 1;
               
               $iz = false;
           }
           $bb->$endx -= $dx ? 1:0;
           if($dx && !$this->checkIfAllTheSame($world->getBlocksInBB($bb))) {
               $bb->$endx += 1;
               $dx = false;
           }
           $bb->$endy -= $dy ? 1:0;
           if($dy && !$this->checkIfAllTheSame($world->getBlocksInBB($bb))) {
               $bb->$endy += 1;
               $dy = false;
           }
           $bb->$endz -= $dz ? 1:0;
           if($dz && !$this->checkIfAllTheSame($world->getBlocksInBB($bb))) {
               $bb->$endz += 1;
               $dz = false;
           }
           
           if(!$ix && !$iy && !$iz && !$dx && !$dy && !$dz) {
               $check = false;
               break;
           }

       }
       return $bb;

   }
}
class BoundingBox {
    public function __construct($startx,$starty,$startz,$endx,$endy,$endz) {
        $this->startx = $startx > $endx ? $endx : $startx;
        $this->starty = $starty > $endy ? $endy : $starty;
        $this->startz = $startz > $endz ? $endz : $startz;
        $this->endx = $startx < $endx ? $endx : $startx;
        $this->endy = $starty < $endy ? $endy : $starty;
        $this->endz = $startz < $endz ? $endz : $startz;
    }
    public function getCubicSize() {
        
        $xsize = $this->startx - $this->endx;
        $xsize += $xsize < 0 ? -1 : 1;
        $ysize = $this->starty - $this->endy;
        $ysize += $ysize < 0 ? -1 : 1;
        $zsize = $this->startz - $this->endz;        
        $zsize += $zsize < 0 ? -1 : 1;
        return abs($xsize * $ysize * $zsize);
    }
    public $startx;
    public $starty;
    public $startz;
    public $endx;
    public $endy;
    public $endz;
}
class RangeLocation extends Fillable {
   public function __construct($what=[]) {
        parent::__construct($what); 
        return $this;
   }
   public $startx;
   public $starty;
   public $startz;
   public $endx;
   public $endy;
   public $endz;
   public function isAir() {
      return   $this->block->modid == 'minecraft' && $this->block->blockid == 'air';
   }
   public $block;
   public $isRange=true;
   public $nbtcontents="";

}
class World {
   public function __construct() {
       $this->master = new BlockMaster();
       $this->blacklist = new BlackList();
   }
   private $master;
   private $blacklist ;
   public function setMaster(BlockMaster $master) {
       $this->master = $master;
   }
   public function setBlackList(BlackList $list) {
        $this->blacklist = $list;
    }
   public function isBlackListed($location) {
       /**
        * If it doesnt exist return true, but also output a message
        *
        */
       if($block = $this->master->getBlock($location->block)){
            return $this->blacklist->isInBlackList($block->modid,$block->blockid,$block->metadata);
       }


                

       var_dump($location);
       echo 'cant get block from master!'."\n";
       return true;
   }
   public function isAir($loc) {
      if($block = $loc->block) {
          $block = $this->master->getBlock($block);
      }
      return $block->modid == 'minecraft' && $block->blockid == 'air';
   }
   public function checkAllAirBlocksForSkyAccess() {
        $airblockcount = 0;
        foreach($this->blockslist as $x => $arrX) {
            foreach($arrX as $y => $arrY) {
                foreach($arrY as $z => $location) {
                    if($this->isAir($this->getBlock($x,$y,$z))) {
                        $foundother = false;
                        /** Check all blocks above if its sky access */
                        for($c=$y;$c<256;$c++) {
                            if($loc = $this->getBlock($x,$c,$z)) {
                                if(!$this->isAir($loc)) {
                                    $foundother = true;
                                }
                            }
                        }
                        /** Not found another, it's a sky access air block, remove it **/
                        if(!$foundother) {
                            $this->getBlock($x,$y,$z)->setDeleted();
                            $airblockcount++;
                        }
                        /** Found a block above this one. Check if it's an air block above void **/
                        else {
                            for($c=$y;$c>-256;$c--) {
                                if($loc = $this->getBlock($x,$c,$z)) {
                                    if(!$this->isAir($loc)) {
                                        $foundother = true;
                                    }
                                }
                            }
                            /** Verily, its an airblock above the void, begone! **/
                            if(!$foundother) {
                                $this->getBlock($x,$y,$z)->setDeleted();
                                $airblockcount++;
                            }
                        }
                    }
                }
            }
        }
    return $airblockcount;
    }
   private $blockslist = [[[]]];
   public function addLocation($location) {
      if(!$this->isBlackListed($location)) {
        $this->blockslist[$location->x][$location->y][$location->z] = $location;
      }
   }
   public function getBlock($x,$y,$z) {
      if($this->hasBlockAt($x,$y,$z)) {
        return $this->blockslist[$x][$y][$z];
      }
      
      return null;
   }
   public function hasBlockAt($x,$y,$z) {
       return isset($this->blockslist[$x][$y][$z]) && !is_null($this->blockslist[$x][$y][$z]) && !$this->blockslist[$x][$y][$z]->isDeleted();
   }
   public function getBlocksInBB(BoundingBox $range) {
       $list = [];
       for($x=$range->startx;$x<=$range->endx;$x++) {
           for($y=$range->starty;$y<=$range->endy;$y++) {
               for($z=$range->startz;$z<=$range->endz;$z++) {
                   $list[] = $this->getBlock($x,$y,$z);
               }
           }
       }
       return $list;
   }
   public function deleteBlocks(BoundingBox $range) {
       for($x=$range->startx;$x<=$range->endx;$x++) {
           for($y=$range->starty;$y<=$range->endy;$y++) {
               for($z=$range->startz;$z<=$range->endz;$z++) {
                   $this->getBlock($x,$y,$z)->setDeleted();
               }
           }
       }
       
   }
   public function getFirstAvailableRange() {
       foreach($this->blockslist as $x => $ylist) {
           foreach($ylist as $y => $zlist) {
               foreach($zlist as $z => $block) {
                   $block = $this->getBlock($x,$y,$z);
                   if($block && !$block->isCheckedOut()) {
                       $block->checkout();
                       return $block;
                   }
               }
           }
       }
   }
   public function getRemainingBlocks() {
        $ret = [];
        foreach($this->blockslist as $x => $ylist) {
           foreach($ylist as $y => $zlist) {
               foreach($zlist as $z => $block) {
                   $block = $this->getBlock($x,$y,$z);
                   if($block) {
                       $ret[] = $block;
                   }
               }
           }
       }
       return $ret;
   }
}
$blacklist = new BlackList();
$blacklist->addToBlackList('minecraft','dirt','0'); 
$blacklist->addToBlackList('minecraft','air','0');
$blacklist->addToBlackList('minecraft','grass','0');
$blacklist->addToBlackList('minecraft','tallgrass','0');
$blacklist->addToBlackList('minecraft','tallgrass','1');

$world = new World();
$master = new BlockMaster();


$master->addTranslation('minecraft',"leaves","12",
                ["blockid"=> "block_magicleaves",
	            "metadata"=> 0,
        	    "modid"=> "magiccookies"]
                );
$master->addTranslation(  "minecraft",'leaves','4',              
	            ["blockid"=> "block_magicleaves",
	            "metadata"=> 0,
        	    "modid"=> "magiccookies"]
                );
$master->addTranslation('minecraft','log','0',
	            ["blockid"=> "block_magicwood",
	            "metadata"=> 1,
        	    "modid"=> "magiccookies"]
                );
$master->addTranslation('minecraft','log','8',
	            ["blockid"=> "block_magicwood",
	            "metadata"=> 2,
        	    "modid"=> "magiccookies"]
                );
$master->addTranslation('minecraft','log','4',
	            ["blockid"=> "block_magicwood",
	            "metadata"=> 0,
        	    "modid"=> "magiccookies"]
                );



$master->setBlackList($blacklist);
$world->setMaster($master);

$contents = [];
foreach($filelist as $file) {
   $filecontents = file_get_contents($file);
   $content = json_decode($filecontents);
   unset($filecontents);
   foreach($content->blocklist as $id =>$item) {
	   $master->addToBlockList($id,$item);
   }
   foreach($content->locations as $item) {
       $world->addLocation(new Location($item));
   }
   unset($content);
}

$ranges = [];
echo "Removing surplus airblocks with direct sky access\n";
$cnt = $world->checkAllAirBlocksForSkyAccess();
echo "Removed $cnt airblocks with direct sky or void access\n";
echo "Checking ranges\n";
while($loc = $world->getFirstAvailableRange()) {
    if($range = $loc->findBiggestRange($world)) {
        echo "Found range size ".($range ? $range->getCubicSize():'').":".json_encode($range)."\n";
        $rangeloc = new RangeLocation();
        $rangeloc->fill(['startx'=>$range->startx,'starty'=>$range->starty,'startz'=>$range->startz,'endx'=>$range->endx,'endy'=>$range->endy,'endz'=>$range->endz,'block'=>$loc->block]);
        $world->deleteBlocks($range);
        $ranges[] = $rangeloc;
    }
}
$locations = $world->getRemainingBlocks();
foreach($ranges as $range) {
$master->addToLocationList($range);
}
// Add all locations
foreach($locations as $location) {
   $master->addToLocationList($location);
}
$master->compileLocations();

file_put_contents($outputfile,json_encode($master,JSON_PRETTY_PRINT));
//echo ;
