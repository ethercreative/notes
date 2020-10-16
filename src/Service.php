<?php
/**
 * Notes for Craft CMS
 *
 * @link      https://ethercreative.co.uk
 * @copyright Copyright (c) 2020 Ether Creative
 */

namespace ether\notes;

use Craft;
use craft\base\Component;
use craft\base\ElementInterface;
use craft\db\Query;
use craft\elements\User;
use craft\helpers\ArrayHelper;
use craft\helpers\DateTimeHelper;
use DateTime;
use yii\db\Expression;

/**
 * Class Service
 *
 * @author  Ether Creative
 * @package ether\notes
 */
class Service extends Component
{

	public function add ($elementId, $siteId, $userId, $note)
	{
		$data = compact('siteId', 'elementId', 'userId', 'note');

		Craft::$app->getDb()->createCommand()
			->insert(Field::$table, $data)
			->execute();

		$id = (new Query())
			->select('id')
			->from(Field::$table)
			->where($data)
			->scalar();

		$user = User::findOne(['id' => $userId]);
		$meta = $user->fullName ?: $user->username . ' &bull; ' . (new DateTime())->format(Field::$dateFormat);

		return compact('meta', 'id');
	}

	public function delete ($id)
	{
		Craft::$app->getDb()->createCommand()
			->delete(Field::$table, compact('id'))
			->execute();
	}

	public function getNotesByElement (ElementInterface $element, $translatable = false)
	{
		$where = [
			'elementId' => $element->id,
		];

		if ($translatable)
			$where['siteId'] = $element->siteId;

		$rawNotes = (new Query())
			->select('*')
			->from(Field::$table)
			->where($where)
			->orderBy('dateCreated desc')
			->all();

		$userIds = ArrayHelper::getColumn($rawNotes, 'userId');

		$select = <<<SQL
[[elements]].[[id]],
CASE WHEN NULLIF([[users.firstName]], '') is null THEN [[users.username]] ELSE CONCAT([[users.firstName]], " ", [[users.lastName]]) END
SQL;

		$users = User::find()
			->select(new Expression($select))
			->id($userIds)
			->pairs();

		$notes = [];

		foreach ($rawNotes as $note)
		{
			$notes[] = new Note([
				'id' => $note['id'],
				'note' => $note['note'],
				'author' => $users[$note['userId']],
				'date' => DateTimeHelper::toDateTime($note['dateCreated'])->format(Field::$dateFormat),

				'elementId' => $note['elementId'],
				'siteId' => $note['siteId'],
				'userId' => $note['userId'],

				'dateCreated' => $note['dateCreated'],
				'dateUpdated' => $note['dateUpdated'],
			]);
		}

		return $notes;
	}

}
