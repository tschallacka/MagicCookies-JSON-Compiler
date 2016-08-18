<?php
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
							$this->deleteBlock($x,$y,$z);
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
								$this->deleteBlock($x,$y,$z);
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
					/**
					 * Need null blocks for the check in all the same!
					 */
					$list[] = $this->getBlock($x,$y,$z);
				}
			}
		}
		return $list;
	}
	public function deleteBlock($x,$y,$z) {
		$this->blockslist[$x][$y][$z] = null;
	}
	public function deleteBlocks(BoundingBox $range) {
		for($x=$range->startx;$x<=$range->endx;$x++) {
			for($y=$range->starty;$y<=$range->endy;$y++) {
				for($z=$range->startz;$z<=$range->endz;$z++) {
					$this->deleteBlock($x,$y,$z);
					//$this->getBlock($x,$y,$z)->setDeleted();
				}
			}
		}
		 
	}
	public function getFirstAvailableRange() {
		static $ranges;
		static $rangeCount;
		if(!isset($ranges)) {
			$ranges = [];
			echo "Building available range list\n";
			foreach($this->blockslist as $x => $ylist) {
				foreach($ylist as $y => $zlist) {
					foreach($zlist as $z => $block) {
						$block = $this->getBlock($x,$y,$z);
						if($block && !$block->isCheckedOut()) {
							$ranges[] = ['x'=>$x,'y'=>$y,'z'=>$z];
						}
					}
				}
			}
			$rangeCount = count($ranges);
			echo "Available ranges: $rangeCount\n";
		}
		
		if($rangeCount > 0) {
			while($rangeCount > 0) {
				$elem = $ranges[--$rangeCount];
				$block = $this->getBlock($elem['x'],$elem['y'],$elem['z']);
				if($block && !$block->isCheckedOut()) {
					$block->checkout();
					echo "Remaining ranges: $rangeCount\n";
					return $block;
				}
			}
		}
	}
	public function getRemainingBlocks() {
		$ret = [];
		foreach($this->blockslist as $ylist) {
			foreach($ylist as $zlist) {
				foreach($zlist as $block) {
					//$block = $this->getBlock($x,$y,$z);
					if($block) {
						$ret[] = $block;
					}
				}
			}
		}
		return $ret;
	}
}