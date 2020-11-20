<?php
/**
 * Notes for Craft CMS
 *
 * @link      https://ethercreative.co.uk
 * @copyright Copyright (c) 2020 Ether Creative
 */

namespace ether\notes;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\services\Fields;
use craft\services\UserPermissions;
use yii\base\Event;

/**
 * Class Notes
 *
 * @author  Ether Creative
 * @package ether\notes
 * @property Service $do
 */
class Notes extends Plugin
{

	public function init ()
	{
		parent::init();

		$this->setComponents([
			'do' => Service::class,
		]);

		Event::on(
			Fields::class,
			Fields::EVENT_REGISTER_FIELD_TYPES,
			[$this, 'onRegisterFieldTypes']
		);

		Event::on(
			UserPermissions::class,
			UserPermissions::EVENT_REGISTER_PERMISSIONS,
			[$this, 'onRegisterPermissions']
		);
	}

	public function onRegisterFieldTypes (RegisterComponentTypesEvent $event)
	{
		$event->types[] = Field::class;
	}

	public function onRegisterPermissions (RegisterUserPermissionsEvent $event)
	{
		$event->permissions['Notes'] = [
			'addNotes' => [
				'label' => Craft::t('notes', 'Add notes'),
			],
			'deleteOwnNotes' => [
				'label' => Craft::t('notes', 'Delete own notes'),
			],
			'deleteAllNotes' => [
				'label' => Craft::t('notes', 'Delete all notes'),
			],
		];
	}

}
