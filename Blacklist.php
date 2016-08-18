<?php
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