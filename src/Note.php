<?php
/**
 * Notes for Craft CMS
 *
 * @link      https://ethercreative.co.uk
 * @copyright Copyright (c) 2020 Ether Creative
 */

namespace ether\notes;

use craft\base\Model;
use craft\elements\User;

/**
 * Class Note
 *
 * @author  Ether Creative
 * @package ether\notes
 */
class Note extends Model
{

	public $id;
	public $note;
	public $author;
	public $userId;
	public $date;

	public function getUser ()
	{
		return User::findOne(['id' => $this->userId]);
	}

}
