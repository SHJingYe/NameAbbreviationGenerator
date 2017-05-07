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

use iTXTech\SimpleFramework\Console\Command\Command;
use iTXTech\SimpleFramework\Console\Logger;
use iTXTech\SimpleFramework\Console\TextFormat;
use iTXTech\SimpleFramework\Util\Config;

class NAGenerateCommand implements Command{
	public function getDescription(): string{
		return "自动生成学生姓名缩写列表。";
	}

	public function getName(): string{
		return "NAGenerate";
	}

	public function getUsage(): string{
		return "nagenerate <文件名>";
	}

	public function execute(string $command, array $args): bool{
		if(!isset($args[0])){
			return false;
		}
		$fileName = NameAbbreviationGenerator::$dataPath . $args[0] . ".yml";
		$output = NameAbbreviationGenerator::$dataPath . $args[0] . "_pinyin.yml";
		if(!file_exists($fileName)){
			Logger::error("找不到文件：$fileName");
			return false;
		}
		$baseData = new Config($fileName, Config::YAML);
		$convertResult = [];
		Logger::info(TextFormat::GOLD . "开始转换。");
		foreach($baseData->getAll() as $className => $class){
			foreach($class as $id => $student){
				$convertResult[$className][$id] = [
					"name" => $student,
					"abbreviation" => NameAbbreviationGenerator::getInstance()->getPinyin($student)
				];
			}
		}
		Logger::info(TextFormat::AQUA . "正在写出文件到：$output");
		$storage = new Config($output, Config::YAML);
		$storage->setAll($convertResult);
		$storage->save();
		Logger::info(TextFormat::GREEN . "所有操作已完成。");
		return true;
	}
}