<?php

class generatorVB
{
	private $privPrefix = '_';
	private $classPrefix = 'cls';
	private $classbuilder;
	private $upper;
	private $newBit;
	private $classname;
	private $publics;
	private $privates;


	public function __construct($dump, $usercode, $comment ) {
		$this->UpperBlock($usercode, $comment);
		$this->newBlock();
		$splitted = $this->splitMe($dump);
		$this->multipass($splitted);

		$this->classbuilder = <<<SQL
{$this->upper}<br><br>
{$this->classname}
{$this->privates}<br><br><br>
{$this->newBit}<br><br><br>
{$this->publics}

End Class<br><br>
SQL;
	}

	private function upperBlock($usercode, $comment) {
		$date = date('d/m/Y');
		$class = <<<CLASS
'******************************************************************
'   {$usercode}      {$date}  {$comment}
'******************************************************************

Imports Microsoft.VisualBasic
Imports System.Configuration
Imports System.Data
Imports System.Data.SqlClient
Imports System.Collections.Generic
Imports System

CLASS;
		$this->upper = $class;
	}

	private function newBlock() {
		$newb = <<<NEW
	Public Sub New()
	
	End Sub
NEW;
		$this->newBit = $newb;
	}

	private function splitMe($tableconstruct) {
		$split = explode("\n", $tableconstruct);
		$pass1 = [];
		for($i=0; $i<=count($split)-1; $i++) {
			$pass1[$i] = trim($split[$i]);
		}

		return $pass1;
	}

	private function multipass($data) {
		foreach ($data as $line) {
			if (strtoupper(substr($line,0,6))==='CREATE') {
				$tname = substr($line,23);
				$tname = substr($tname,0,strlen($tname)-3);
				$classname = <<<CLASSNAME
<br><br>Public Class {$this->classPrefix}{$tname}<br><br>
CLASSNAME;
				$this->classname = $classname;
			} else {
				$linebits = explode(' ', $line);
				if (count($linebits) > 1) {
					$removals = array("]", "[");
					$entity = str_replace($removals,'',$linebits[0]);
					$rawtype = str_replace($removals,'',$linebits[1]);
					$private = $this->getPrivate($entity, $rawtype);
					$public = $this->getPublic($entity, $rawtype);
					$this->privates.= $private.'<br>';
					$this->publics .= $public. '<br><br>';
				}

			}
		}
	}


	private function GetPublic($entity, $rawtype) {
		$e_type = $this->types($rawtype);
		$pref = $this->privPrefix;
		$public = <<<PUBLIC
	Public Property {$entity}() As {$e_type}
		Get
			Return {$pref}{$entity}
		End Get
		Set(ByVal value as {$e_type})
			{$pref}{$entity} = value
		End Set
	End Property
	
PUBLIC;
		return $public;
	}

	private function getPrivate($entity, $rawtype) {
		$e_type = $this->types($rawtype);
		$pref = $this->privPrefix;
		$private = <<<PRIVATE
	Private {$pref}{$entity} As {$e_type}
PRIVATE;

		return $private;
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

	public function generate() {
		return $this->classbuilder;
	}

	public function className() {
		return $this->classname;
	}
}

