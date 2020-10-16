<?php
/**
 * Notes for Craft CMS
 *
 * @link      https://ethercreative.co.uk
 * @copyright Copyright (c) 2020 Ether Creative
 */

namespace ether\notes;

use craft\base\Element;
use craft\base\Model;
use craft\elements\User;
use craft\helpers\ArrayHelper;
use craft\records\Site;

/**
 * Class Note
 *
 * @author  Ether Creative
 * @package ether\notes
 */
class Note extends Model
{

	private static $_eagerIds = [];
	private static $_eagerElements = [
		'elements' => [],
		'users' => [],
		'sites' => [],
	];

	public $id;

	public $note;
	public $author;
	public $date;

	public $elementId;
	public $siteId;
	public $userId;

	public $dateCreated;
	public $dateUpdated;

	public function __construct ($config = [])
	{
		parent::__construct($config);

		self::setEagerId('elements', $config['elementId']);
		self::setEagerId('users', $config['userId']);
		self::setEagerId('sites', $config['siteId']);
	}

	// Getters
	// =========================================================================

	/**
	 * @return Element|null
	 */
	public function getElement ()
	{
		self::cacheEagerElements('elements', Element::class);

		return @self::$_eagerElements['elements'][$this->elementId];
	}

	/**
	 * @return Site|null
	 */
	public function getSite ()
	{
		self::cacheEagerElements('sites', Site::class);

		return @self::$_eagerElements['sites'][$this->siteId];
	}

	/**
	 * @return User|null
	 */
	public function getUser ()
	{
		self::cacheEagerElements('users', User::class);

		return @self::$_eagerElements['users'][$this->userId];
	}

	// Lite eager loading
	// =========================================================================

	/**
	 * Will eager load the required elements
	 *
	 * @param string $key
	 * @param        $cls
	 */
	private static function cacheEagerElements (string $key, $cls)
	{
		if (!empty(self::$_eagerElements[$key])) return;

		self::$_eagerElements[$key] = ArrayHelper::index(
			$cls::findAll(['id' => self::$_eagerIds[$key]]),
			'id'
		);
	}

	/**
	 * Stores an array of IDs to eager load against the given key
	 *
	 * @param string $key
	 * @param int    $id
	 */
	private static function setEagerId (string $key, int $id)
	{
		if (empty(self::$_eagerIds[$key]))
			self::$_eagerIds[$key] = [$id];
		else
		{
			self::$_eagerIds[$key][] = $id;
			self::$_eagerIds[$key] = array_unique(self::$_eagerIds[$key]);
		}
	}

}
