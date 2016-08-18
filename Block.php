<?php

Class Block extends Fillable{
	public function __construct($modid=null,$blockid=null,$metadata = null) {
		if(is_array($modid)) {
			$this->fill($modid);
		}
		else if (is_object($modid)) {
			$this->modid = $modid->modid;
			$this->blockid = $modid->blockid;
			$this->metadata = $modid->metadata;
		}
		else {
			$this->modid = $modid;
			$this->blockid = $blockid;
			$this->metadata = $metadata;
		}
	}
	public function __toString() {
		return $this->modid . ':'.$this->blockid.'('.$this->metadata.')';
	}
	public $blockid;
	public $metadata;
	public $modid;
}