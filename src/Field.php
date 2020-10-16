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

		return Notes::getInstance()->do->getNotesByElement(
			$element,
			$this->getIsTranslatable($element)
		);
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
