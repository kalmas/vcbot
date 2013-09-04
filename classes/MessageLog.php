<?php 

class MessageLog{
	const NORMAL = 0;
	const COMMAND = 1;
	const RESPONSE = 2;
	const IMPORTANT = 3;
	
	/**
	 * Write this message
	 * @param array|string $message
	 * @param string $type
	 */
	public function write($message, $type = self::NORMAL){
		if(is_array($message)){
			foreach($message as $line){
				echo $line . "\n";
			}
		} else {
			echo $message . "\n";
		}

		echo "---\n";
	}
	
}
