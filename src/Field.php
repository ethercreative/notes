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
use ether\notes\web\NotesAsset;

/**
 * Class Field
 *
 * @author  Ether Creative
 * @package ether\notes
 */
class Field extends \craft\base\Field
{

	const ADD_ANY = 'addAny';
	const ADD_PERMISSION = 'addPermission';
	const ADD_NONE = 'addNone';
	const DELETE_ANY = 'deleteAny';
	const DELETE_PERMISSION = 'deletePermission';
	const DELETE_NONE = 'deleteNone';

	public static $table = '{{%notes}}';
	public static $dateFormat = 'M j, Y g:ia';

	/** @var bool|string Manage who can add notes */
	public $allowAdding = self::ADD_PERMISSION;

	/** @var bool|string Manage who can delete notes */
	public $allowDeleting = self::DELETE_PERMISSION;

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

		return Notes::getInstance()->do->getNotesByElement(
			$element,
			$this->getIsTranslatable($element)
		);
	}

	public function getSettingsHtml ()
	{
		// 1.0.2 back-compat
		$allowDeleting = $this->allowDeleting;
		if ($allowDeleting === true) $allowDeleting = self::DELETE_ANY;
		elseif ($allowDeleting === false) $allowDeleting = self::DELETE_NONE;

		return Craft::$app->getView()->renderTemplate('notes/settings', [
			'allowAdding' => $this->allowAdding,
			'allowDeleting' => $allowDeleting,
			'addOpts' => self::_addOpts(),
			'deleteOpts' => self::_deleteOpts(),
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
			'allowAdding' => $this->_canAdd(),
			'allowDeleting' => $this->_canDelete(),
		]);
	}

	// Helpers
	// =========================================================================

	private static function _addOpts ()
	{
		return [
			self::ADD_ANY => Craft::t('notes', 'All users'),
			self::ADD_PERMISSION => Craft::t('notes', 'User permission'),
			self::ADD_NONE => Craft::t('notes', 'Disabled'),
		];
	}

	private static function _deleteOpts ()
	{
		return [
			self::DELETE_ANY => Craft::t('notes', 'All users'),
			self::DELETE_PERMISSION => Craft::t('notes', 'User permission'),
			self::DELETE_NONE => Craft::t('notes', 'Disabled'),
		];
	}

	private function _canAdd ()
	{
		switch ($this->allowAdding)
		{
			case self::ADD_PERMISSION:
				return Craft::$app->user->checkPermission('addNotes');
			case self::ADD_NONE:
				return false;
			default:
			case self::ADD_ANY:
				return true;
		}
	}

	private function _canDelete ()
	{
		$user = Craft::$app->getUser();

		switch ($this->allowDeleting)
		{
			case self::DELETE_PERMISSION:
				return $user->checkPermission('deleteAllNotes')
					? true
					: $user->checkPermission('deleteOwnNotes')
						? 'own'
						: false;
			case self::DELETE_NONE:
				return false;
			default:
			case self::DELETE_ANY:
				return true;
		}
	}

}
