<?php
/**
 * Notes for Craft CMS
 *
 * @link      https://ethercreative.co.uk
 * @copyright Copyright (c) 2020 Ether Creative
 */

namespace ether\notes;

use Craft;
use craft\base\ElementInterface;
use craft\db\Query;
use craft\elements\User;
use craft\helpers\ArrayHelper;
use craft\helpers\DateTimeHelper;
use ether\notes\web\NotesAsset;
use yii\db\Expression;

/**
 * Class Field
 *
 * @author  Ether Creative
 * @package ether\notes
 */
class Field extends \craft\base\Field
{

	public static $table = '{{%notes}}';
	public static $dateFormat = 'M j, Y g:ia';

	/** @var bool Will allow the deleting of notes if true */
	public $allowDeleting = false;

	public static function displayName (): string
	{
		return Craft::t('notes', 'Notes');
	}

	public static function hasContentColumn (): bool
	{
		return false;
	}

	public static function supportedTranslationMethods (): array
	{
		return [
			self::TRANSLATION_METHOD_NONE,
			self::TRANSLATION_METHOD_SITE,
		];
	}

	public function normalizeValue ($value, ElementInterface $element = null)
	{
		if (!$element)
			return [];

		$where = [
			'elementId' => $element->id,
		];

		if ($this->getIsTranslatable($element))
			$where['siteId'] = $element->siteId;

		$rawNotes = (new Query())
			->select('id, note, userId, dateCreated')
			->from(self::$table)
			->where($where)
			->orderBy('dateCreated desc')
			->all();

		$select = <<<SQL
[[elements]].[[id]],
CASE WHEN NULLIF([[users.firstName]], '') is null THEN [[users.username]] ELSE CONCAT([[users.firstName]], " ", [[users.lastName]]) END
SQL;

		$users = User::find()
			->select(new Expression($select))
			->id(ArrayHelper::getColumn($rawNotes, 'userId'))
			->pairs();

		$notes = [];

		foreach ($rawNotes as $note)
		{
			$notes[] = new Note([
				'id' => $note['id'],
				'note' => $note['note'],
				'author' => $users[$note['userId']],
				'userId' => $note['userId'],
				'date' => DateTimeHelper::toDateTime($note['dateCreated'])->format(self::$dateFormat),
			]);
		}

		return $notes;
	}

	public function getSettingsHtml ()
	{
		return Craft::$app->getView()->renderTemplate('notes/settings', [
			'allowDeleting' => $this->allowDeleting,
		]);
	}

	protected function inputHtml ($value, ElementInterface $element = null): string
	{
		if (!$element || !$element->id)
			return Craft::t('notes', 'You must save the element before you can add notes!');

		$view =  Craft::$app->getView();
		$view->registerAssetBundle(NotesAsset::class);

		return $view->renderTemplate('notes/field', [
			'ns' => $this->handle,
			'element' => $element,
			'notes' => $value,
			'allowDeleting' => $this->allowDeleting,
		]);
	}

}
