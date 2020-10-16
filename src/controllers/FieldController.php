<?php
/**
 * Notes for Craft CMS
 *
 * @link      https://ethercreative.co.uk
 * @copyright Copyright (c) 2020 Ether Creative
 */

namespace ether\notes\controllers;

use Craft;
use craft\web\Controller;
use ether\notes\Notes;

/**
 * Class FieldController
 *
 * @author  Ether Creative
 * @package ether\notes\controllers
 */
class FieldController extends Controller
{

	public function actionAdd ()
	{
		$request = Craft::$app->getRequest();

		$siteId = $request->getRequiredBodyParam('siteId');
		$elementId = $request->getRequiredBodyParam('elementId');
		$userId = $request->getRequiredBodyParam('userId');
		$note = $request->getRequiredBodyParam('note');

		$res = Notes::getInstance()->do->add(
			$elementId,
			$siteId,
			$userId,
			$note
		);

		return $this->asJson($res);
	}

	public function actionDelete ()
	{
		$id = Craft::$app->getRequest()->getRequiredBodyParam('id');
		Notes::getInstance()->do->delete($id);

		return $this->asJson(1);
	}

}
