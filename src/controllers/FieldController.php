<?php
/**
 * Notes for Craft CMS
 *
 * @link      https://ethercreative.co.uk
 * @copyright Copyright (c) 2020 Ether Creative
 */

namespace ether\notes\controllers;

use Craft;
use craft\elements\User;
use craft\web\Controller;
use DateTime;
use ether\notes\Field;

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

		Craft::$app->getDb()->createCommand()
			->insert(
				Field::$table,
				compact('siteId', 'elementId', 'userId', 'note')
			)
			->execute();

		$user = User::findOne(['id' => $userId]);
		$meta = $user->fullName ?: $user->username . ' &bull; ' . (new DateTime())->format(Field::$dateFormat);

		return $this->asJson(compact('meta'));
	}

}
