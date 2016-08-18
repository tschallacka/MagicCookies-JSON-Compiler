<?php
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