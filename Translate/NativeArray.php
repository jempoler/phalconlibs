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
  | Authors: Jempoler <jempoler.com@gmail.com>                         |
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
				// $translation = call_user_func([$this, 'addTranslateKey'], $translateKey, $defaultTranslation, $languageModule, $controllerName);
				$translation = $this->addTranslateKey($translateKey, $defaultTranslation, $languageModule, $controllerName);
			}
			$this->_translate[$translateKey] = $translation;
		}
		return $this->replacePlaceholders($translation, $placeholders);
	}

	/**
	 * Returns translations related to the given keys
	 */
	public function __($translations = array(), $languageModule = null, $controllerName = '')
	{
		$ret = array();
		$newTrans = array();
		foreach ($translations as $index => $trans) {
			$translateKey = $trans[0];
			$translation = $translateKey;
			$defaultTranslation = "";
			if (isset($trans[1])) {
				$defaultTranslation = $trans[1];
			}
			$placeholders = null;
			if (isset($trans[2])) {
				$placeholders = $trans[2];
			}
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
				$newTrans[$translateKey] = $translation;
				$this->_translate[$translateKey] = $translation;
			}
			$ret[$translateKey] = $this->replacePlaceholders($translation, $placeholders);
		}
		if (!empty($newTrans)) {
			if (method_exists($this, 'addTranslateKeys')) {
				$this->addTranslateKeys($newTrans, $languageModule, $controllerName);
			}
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