<?php
namespace bright\core\model\vo;

class Marker extends Content {

	public $markerId;
	
	public $lat;
	public $lng;
	
	public $ispoly = false;
	public $coords;
	
	public $layerId;
	public $color;
	public $inheritcolor = false;

	public $iconsize;
	
	public $street;
	public $number;
	public $zip;
	public $city;
	public $county;
}