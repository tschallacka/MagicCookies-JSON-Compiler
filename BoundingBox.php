<?php
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
	public function __toString() {
		return json_encode($this);
	}
	public $startx;
	public $starty;
	public $startz;
	public $endx;
	public $endy;
	public $endz;
}