<?php
/**
 * Notes for Craft CMS
 *
 * @link      https://ethercreative.co.uk
 * @copyright Copyright (c) 2020 Ether Creative
 */

namespace ether\notes\web;

use Craft;
use craft\web\AssetBundle;

/**
 * Class NotesAsset
 *
 * @author  Ether Creative
 * @package ether\notes\web
 */
class NotesAsset extends AssetBundle
{

	public function init ()
	{
		$this->sourcePath = __DIR__;
		$this->css = ['notes.css'];
		$this->js = ['notes.js'];

		Craft::$app->getView()->registerTranslations('app', ['Delete']);

		parent::init();
	}

}
