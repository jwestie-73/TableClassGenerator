<?php

class generatorC
{
	private $classbuilder = '';
	private $raw_data;
	private $classheader;
	private $elements;
	private $UseBlock;
	private $commentblock;
	private $nsBlock;
	private $classPrefix = 'cls';


	public function __construct($dump, $namespace, $usercode, $comment) {
		$this->raw_data = $dump;
		$stripped = $this->stripCrap();
		$this->UseMe();
		$this->ns($namespace);
		$this->comments($usercode, $comment);
		$this->lineBuild($stripped);
		$this->classbuilder = <<<CB
{$this->UseBlock}<br><br>
{$this->nsBlock}<br><br>
{$this->commentblock}<br><br>
{$this->classheader}<br>
{$this->elements}<br><br><br>
		}
	}<br><br>
CB;
	}

	private function UseMe() {
		$using = <<<USING
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
USING;
		$this->UseBlock = $using;
	}

	Private function stripCrap() {
		$pass1=[];
		$split = explode("\n", $this->raw_data);
		for($i=0; $i<=count($split)-1; $i++) {
			$pass1[$i] = trim($split[$i]);
		}

		//echo "<pre style='background-color:white;'>", print_r($pass1, 1), "</pre>";
		//die();

		return $pass1;
	}

	private function ns($ns) {
		$my_NS = <<<NS
namespace {$ns}.AppCode
	{
NS;
		$this->nsBlock = $my_NS;
	}

	private function comments($uc, $comment) {
		$date = date('d/m/Y');
		$commentblok = <<<COMMENT
	/*
	* {$comment}
	* Class By {$uc} {$date}
	*/
COMMENT;
	$this->commentblock = $commentblok;
	}
	private function lineBuild($data) {
		foreach ($data as $line) {
			//echo "<pre style='background-color:white;'>", print_r($line, 1), "</pre>";
			//echo "<pre style='background-color:white;'>", print_r($data, 1), "</pre>";

			//die();
			if (strtoupper(substr($line,0,6))==='CREATE') {
				$classname = substr($line,23);
				$classname = substr($classname,0,strlen($classname)-3);
				$pref = $this->classPrefix;
				$classline = <<<CL
	class {$pref}{$classname}
		{
CL;
				$this->classheader = $classline;
			} else {
				$linebits = explode(' ', $line);
				if (count($linebits) >2 ) {
					$removals = array("]", "[");
					$entity = str_replace($removals,'',$linebits[0]);
					$rawtype = str_replace($removals,'',$linebits[1]);
					$element = $this->Entities($entity, $rawtype);
					$this->elements .= $element;
				}

			}
		}
	}

	private function Entities($entity, $rawtype) {
		$e_type = self::types($rawtype);
		$line = <<<CODE
		public {$e_type} {$entity} {get; set;}<br>
CODE;
		return $line;
	}

	private function types($rawtype) {
		switch($rawtype) {
			case 'int':
				$e_type='Integer';
				break;
			case 'nvarchar':
			case 'uniqueidentifier':
				$e_type='String';
				break;
			case 'decimal':
				$e_type='Double';
				break;
			case 'datetime':
				$e_type='DateTime';
				break;
			case 'date':
				$e_type='Date';
				break;
			Case 'bit':
				$e_type='Boolean';
				break;
			default:
				$e_type='String';
				break;
		}
		return $e_type;
	}

	public function Generate() {
		return $this->classbuilder;
	}
	public function className() {
		return $this->classname;
	}

}