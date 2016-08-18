<?php
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
				$fill = '000';
			}
			if(strlen($key) == 2) {
				$fill = '00';
			}
			if(strlen($key) == 3) {
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
		foreach($this->seedLocations as $list) {
			foreach($list as $value) {
				$this->locations[] = $value;
			}
		}
	}
	
	public function addTranslation(Block $orig,Block $to) {
		
		$this->translation[$orig->modid][$orig->blockid][$orig->metadata] = $to;
	}
	
	public function getTranslation(Block $input) {
		
		if(isset( $this->translation[$input->modid][$input->blockid][$input->metadata])) {
			echo " |-> Transforming $input to ".$this->translation[$input->modid][$input->blockid][$input->metadata]."\n";
			return  $this->translation[$input->modid][$input->blockid][$input->metadata];
		}
		
		else {
			return $input;
		}
	}
	
	private function transform(Block $item) {
		return $this->getTranslation($item);

	}
	public function getBlock($id) {
		if(!array_key_exists($id,$this->blocklist)) {
			return null;
		}
		else {
			return $this->blocklist[$id];
		}
	}
	public function addToBlockList($id, Block $item) {
		if(!isset($this->blocklist[$id])) {
			echo "Adding ".$id." $item\n";
			$this->blocklist[$id] = $this->transform($item);
		}
	}
}