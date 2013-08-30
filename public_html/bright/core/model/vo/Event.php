<?php
namespace bright\core\model\vo;

class Event extends Content {
	public $contentId;
	public $eventId;
	
	public $dates;
	public $recur;
	public $until;
	public $rawdates;
	
	public $locationId;
}