<?php
namespace bright\core\utils;

/**
 * Logging utility. Call Logger::log(args). Non scalar args will be printed using var_export
 * @package bright\core\utils
 * @author ids
 * @version 5.0
 */
class Logger {
	
	public static function log() {
		$logdir = BASEPATH . 'bright/logs/';
		$logfile = 'bright.log';
		
		if(!is_dir($logdir))
			mkdir($logdir, 0777);
		
		$statements = func_get_args();
		try {
			$fname = BASEPATH . 'bright/logs/bright.log';
			if(file_exists($fname) && filesize($fname) > 1048576) {
				// Max 1 mb
				self::clear();
			}
			
			$handle = @fopen($fname, 'a');
			
			if(!$handle)
				return;
			
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
	
	public static function clear() {
		try {
			$fname = BASEPATH . 'bright/logs/bright.log';
			if(!file_exists($fname))
				return;
			
			file_put_contents($fname, '');
		} catch(Exception $ex) {
			error_log("Cannot clear log, \r\n" . $ex -> getMessage() . "\r\n------------\r\n" . $ex->getTraceAsString());
		}
	}
}