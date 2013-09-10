<?php 

class MessageLog{
	const NORMAL = 0;
	const COMMAND = 1;
	const RESPONSE = 2;
	const IMPORTANT = 3;

	/*
	 * Pretty Print Constants
	 */
	const PP_CLEAR = "\e[0m";
	const PP_FILL_LINE = "\e[2K";
	const PP_BG_YELLOW = "\e[43m";
	const PP_GREY = "\e[1;30m";
	const PP_BOLD_RED = "\e[1;31m";

	/**
	 * @var boolean
	 */
	private $color;

	public function __construct($color = true){
		$this->color = $color;
	}

	/**
	 * Write this message
	 * @param array|string $message
	 * @param string $type
	 */	
	public function write($message, $type = self::NORMAL){
		if(is_array($message)){
			foreach($message as $line){
				$this->printLine($line, $type);
			}
		} else {
			$this->printLine($message, $type);
		}

		if($type != self::COMMAND){ $this->printLine("", self::NORMAL); }
	}

	/**
	 * @param string $line
	 * @param int $type
	 */
	private function printLine($line, $type){
		switch($type){
			case self::COMMAND:
				$line = self::PP_BG_YELLOW . self::PP_FILL_LINE . $line  . self::PP_CLEAR;
				break;
			case self::RESPONSE:
				$line = self::PP_GREY . $line . self::PP_CLEAR;
				break;
			case self::IMPORTANT:
				$line = self::PP_BOLD_RED . '*** ' . $line . ' ***' . self::PP_CLEAR;
				break;
		}
		
		echo $line . "\n";
	}

}
