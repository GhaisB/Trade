<?php

class 				Debugger {

	static public function emptyLog() {
		if (file_exists(Conf::$debugFile))
			unlink(Conf::$debugFile);
	}

	static public function debug($str) {
		$fp = fopen(Conf::$debugFile, 'a');
		if ($fp) {
			$date = date('d/m/Y \- H:i:s');
			fwrite($fp, $str . "\n");
			fclose($fp);
		}
	}

	static public function print_r($obj) {
		$fp = fopen(Conf::$debugFile, 'a');
		if ($fp) {
			ob_start();
			print_r($obj);
			fwrite($fp, ob_get_contents() . "\n");
			ob_end_clean();
		}
	}

}

?>