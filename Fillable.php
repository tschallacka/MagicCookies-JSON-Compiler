<?php
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