<?php

/**
 * NameAbbreviationGenerator
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author SHJingYe
 * @link https://github.com/SHJingYe
 */

namespace SHJingYe\NameAbbreviationGenerator;

use iTXTech\SimpleFramework\Console\Logger;
use iTXTech\SimpleFramework\Console\TextFormat;
use iTXTech\SimpleFramework\Module\Module;

class NameAbbreviationGenerator extends Module{
	public static $dataPath;
	private static $instance;

	private $database;

	public function load(){
		self::$instance = $this;
		self::$dataPath = $this->getDataFolder();

		@mkdir($this->getDataFolder());
		Logger::info(TextFormat::LIGHT_PURPLE . "正在读取数据库，请稍后。");
		$this->initDatabase();
		Logger::info(TextFormat::GREEN . "数据库初始化完成。");
		$this->getFramework()->getCommandProcessor()->register(new NAGenerateCommand(), "nagenerate");
	}

	public function unload(){
	}

	public static function getInstance(): NameAbbreviationGenerator{
		return self::$instance;
	}

	private function initDatabase(){
		$this->database = [];
		$raw = file_get_contents($this->getFile() . "resources/database");
		foreach(explode("\n", $raw) as $line){//UNIX FORMAT ONLY
			$arr = explode("|", $line);
			$pinyin = $arr[0];
			if(isset($arr[1])){
				for($i = 0; $i < mb_strlen($arr[1]); $i++){
					$single = mb_substr($arr[1], $i, 1);
					if(strlen($single) > 0){
						$this->database[$single] = $pinyin;
					}
				}
			}
		}
	}

	public function getPinyin(string $chineseName): string{
		$result = "";
		for($i = 0; $i < mb_strlen($chineseName); $i++){
			$single = mb_substr($chineseName, $i, 1);
			if(isset($this->database[$single])){
				$result .= $this->database[$single]{0};
			}else{
				$result .= "?";
			}
		}
		return $result;
	}
}