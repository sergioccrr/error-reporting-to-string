<?php

class ErrorReportingToString {

	private $maxLevel  = 0;
	private $eAllLevel = 0;
	private $verConstants = [];
	private $verEAll = [];
	private static $constants = [
		 'E_ERROR'             => 1
		,'E_WARNING'           => 2
		,'E_PARSE'             => 4
		,'E_NOTICE'            => 8
		,'E_CORE_ERROR'        => 16
		,'E_CORE_WARNING'      => 32
		,'E_COMPILE_ERROR'     => 64
		,'E_COMPILE_WARNING'   => 128
		,'E_USER_ERROR'        => 256
		,'E_USER_WARNING'      => 512
		,'E_USER_NOTICE'       => 1024
		,'E_STRICT'            => 2048
		,'E_RECOVERABLE_ERROR' => 4096
		,'E_DEPRECATED'        => 8192
		,'E_USER_DEPRECATED'   => 16384
	];


	public function __construct($version) {
		$this->verConstants['pre_5'] = array_diff(array_keys(self::$constants), ['E_STRICT','E_RECOVERABLE_ERROR','E_DEPRECATED','E_USER_DEPRECATED']);
		$this->verConstants['5.0'] = array_merge($this->verConstants['pre_5'], ['E_STRICT']);
		$this->verConstants['5.2'] = array_merge($this->verConstants['5.0'], ['E_RECOVERABLE_ERROR']);
		$this->verConstants['5.3'] = array_merge($this->verConstants['5.2'], ['E_DEPRECATED', 'E_USER_DEPRECATED']);
		$this->verConstants['5.4'] = $this->verConstants['5.3'];

		foreach ($this->verConstants[$version] as $constant) {
			$this->maxLevel = $this->maxLevel | self::$constants[$constant];
		}

		$this->verEAll['pre_5'] = $this->verConstants['pre_5'];
		$this->verEAll['5.0'] = $this->verConstants['5.0'];
		$this->verEAll['5.0'] = array_diff($this->verEAll['5.0'], ['E_STRICT']);
		$this->verEAll['5.2'] = $this->verConstants['5.2'];
		$this->verEAll['5.2'] = array_diff($this->verEAll['5.2'], ['E_STRICT']);
		$this->verEAll['5.3'] = $this->verConstants['5.3'];
		$this->verEAll['5.3'] = array_diff($this->verEAll['5.3'], ['E_STRICT']);
		$this->verEAll['5.4'] = $this->verConstants['5.4'];

		foreach ($this->verEAll[$version] as $constant) {
			$this->eAllLevel = $this->eAllLevel | self::$constants[$constant];
		}
	}


	private static function in_level($constant, $level) {
		$value = self::$constants[$constant];
		return ($level & $value) === $value;
	}


	private static function get_constants($level) {
		return array_filter(array_keys(self::$constants), function($constant) use ($level) {
			return self::in_level($constant, $level);
		});
	}


	public function convert($level) {
		$constants = self::get_constants($level);

		if (!(count($constants) > (count(self::$constants) / 2))) {
			return implode(' | ', $constants);
		}

		$constantsString = 'E_ALL';
		$maxLevelConstants = self::get_constants($this->maxLevel);

		foreach ($maxLevelConstants as $constant) {
			$inLevel = self::in_level($constant, $this->eAllLevel);
			$inConstants = in_array($constant, $constants);

			if ($inConstants && !$inLevel) {
				$constantsString .= ' | '. $constant;
			} elseif (!$inConstants && $inLevel) {
				$constantsString .= ' & ~'. $constant;
			}
		}

		return $constantsString;
	}

}
