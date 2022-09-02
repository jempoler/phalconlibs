<?php
/*
  +------------------------------------------------------------------------+
  | Jempoler Phalcon Library                                               |
  +------------------------------------------------------------------------+
  | Copyright (c) 2012-2022 jempoler (https://github.com/jempoler)         |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.                                 |
  |                                                                        |
  +------------------------------------------------------------------------+
  | Authors: Leonardus Agung <jempoler.com@gmail.com>                         |
  |                                                                        |
  +------------------------------------------------------------------------+
*/

namespace Jempoler\Phalcon\Translate;

class NativeArray extends \Phalcon\Di\Injectable
{
	protected $_translate = array();

	/**
	 * Returns the translation related to the given key
	 */
	public function _($translateKey, $defaultTranslation = "", $placeholders = null, $languageModule = null, $controllerName = '')
	{
		$translation = $translateKey;
		
		if (is_array($defaultTranslation) && $placeholders === null) {
			$placeholders = $defaultTranslation;
		} else {
			if ($defaultTranslation!="") {
				$translation = $defaultTranslation;
			}
		}
		if ($this->exists($translateKey)) {
			$translation = $this->_translate[$translateKey];
		} else {
			if (method_exists($this, 'addTranslateKey')) {
				// call_user_func_array([$this, 'addTranslateKey'], [$translateKey, $defaultTranslation]);
				$translation = call_user_func([$this, 'addTranslateKey'], $translateKey, $defaultTranslation, $languageModule, $controllerName);
			}
		}
		return $this->replacePlaceholders($translation, $placeholders);
	}

	/**
	 * Returns translations related to the given keys
	 */
	public function __($translateKeys = array(), $languageModule = null, $controllerName = '')
	{
		$ret = array();
		foreach ($translateKeys as $key) {
			$translation = $key[0];
			$defaultTranslation = "";
			if (isset($key[1])) {
				$defaultTranslation = $key[1];
			}
			$placeholders = null;
			if (isset($key[2])) {
				$placeholders = $key[2];
			}
			if (is_array($defaultTranslation) && $placeholders === null) {
				$placeholders = $defaultTranslation;
			} else {
				if ($defaultTranslation!="") {
					$translation = $defaultTranslation;
				}
			}
			if ($this->exists($key[0])) {
				$translation = $this->_translate[$key[0]];
			} else {				
				if (method_exists($this, 'addTranslateKey')) {
					// call_user_func_array([$this, 'addTranslateKey'], [$key, $defaultTranslation]);
					$translation = call_user_func([$this, 'addTranslateKey'], $key[0], $defaultTranslation, $languageModule, $controllerName);
				}
			}
			$ret[$key[0]] = $this->replacePlaceholders($translation, $placeholders);
		}
		return $ret;
	}

	/**
	 * Check whether is defined a translation key in the internal array
	 */
	public function exists($translateKey)
	{
		return isset($this->_translate[$translateKey]);
	}

	public function replacePlaceholders($translation, $placeholders)
	{
		if (is_array($placeholders) && !empty($placeholders)) {
			foreach ($placeholders as $key => $val) {
				$translation = str_replace("%" . $key . "%", $val, $translation);
			}
		}
		return $translation;
	}

	/**
	 * Set translation data
	 * 
	 * @param array $translation translation data
	 */
	public function setTranslation($translation)
	{
		if (is_array($translation)) {
			$this->_translate = array_merge($this->_translate, $translation);
		}
  }

}