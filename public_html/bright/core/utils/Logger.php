<?php
namespace bright\core\utils;

class Logger {
	
	public static function log() {
		$logdir = BASEPATH . 'bright/logs/';
		$logfile = 'bright.log';
		
		if(!is_dir($logdir))
			mkdir($logdir, 0777);
		
		$statements = func_get_args();
		try {
			$fname = BASEPATH . 'bright/logs/bright.log';
			if(file_exists($fname)) {
				// Max 1 mb
				if(filesize($fname) > 1048576)
					file_put_contents($fname, '');
			}
			$handle = fopen($fname, 'a');
			foreach($statements as $statement) {
				if(!is_scalar($statement))
					$statement = var_export($statement, true);
	
				fwrite($handle, $statement . "\n");
			}
			fclose($handle);
		} catch(Exception $ex) {
			error_log("Cannot log, \r\n" . $ex -> getMessage() . "\r\n------------\r\n" . $ex->getTraceAsString());
		}
	}
}