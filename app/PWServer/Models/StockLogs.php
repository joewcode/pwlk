<?php

namespace App\PWServer\Models;

class StockLogs {
	public $count = 0;		// Byte
	public $stocklog;		// Array StockLog

	public function __construct() {
		$this->stocklog = array();
	}
}
