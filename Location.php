<?php
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
	public function getPermutations() {
		$options = ['startx','starty','startz','endx','endy','endz'];
		$permutations =  $this->pc_permute($options);
		$tounset = [];
			
		foreach($permutations as $index=>$permutation) {
		
			$pos1 = strpos($permutation[0],'start');
			$pos2 = strpos($permutation[1],'start');
			$pos3 = strpos($permutation[2],'start');
			$check = (($pos1 === 0 && $pos2 === 0 && $pos3 === 0) ||($pos1 === false && $pos2 === false && $pos3 === false));
			if(!$check) {
				$tounset[] = $index;
			}
			unset($check);
			unset($pos1);
			unset($pos2);
			unset($pos3);
		
		
		}
			
		foreach($tounset as $index) {
			unset($permutations[$index]);
		}
		unset($tounset);
		$list = [];
		foreach($permutations as $per) {
			$list[] = $per;
		}
		unset($permutations);
		$permutations = [];
		foreach($list as $item) {
			$permutations[] = $item;
		}
		unset($list);
		gc_collect_cycles();
		return $permutations;
	}
	public function findBiggestRange(World $world) {
		 
		
		static $permutations;
		
		if(is_null($permutations)) {
			$permutations = $this->getPermutations();
		}
		
		$toparse = $permutations;
		
		
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