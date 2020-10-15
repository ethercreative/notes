<?php
/**
 * Notes for Craft CMS
 *
 * @link      https://ethercreative.co.uk
 * @copyright Copyright (c) 2020 Ether Creative
 */

namespace ether\notes\migrations;

use craft\db\Migration;
use craft\db\Table;
use ether\notes\Field;

/**
 * Class Install
 *
 * @author  Ether Creative
 * @package ether\notes\migrations
 */
class Install extends Migration
{

	public function safeUp ()
	{
		$this->createTable(Field::$table, [
			'id' => $this->primaryKey(),

			'elementId' => $this->integer(11)->notNull(),
			'siteId'    => $this->integer(11)->notNull(),
			'userId'    => $this->integer(11)->notNull(),
			'note'      => $this->text()->notNull(),

			'dateCreated' => $this->dateTime()->notNull(),
			'dateUpdated' => $this->dateTime()->notNull(),
			'uid'         => $this->uid(),
		]);

		$this->addForeignKey(
			null,
			Field::$table,
			'elementId',
			Table::ELEMENTS,
			'id',
			'CASCADE'
		);

		$this->addForeignKey(
			null,
			Field::$table,
			'siteId',
			Table::SITES,
			'id',
			'CASCADE'
		);

		$this->addForeignKey(
			null,
			Field::$table,
			'userId',
			Table::USERS,
			'id',
			'CASCADE'
		);
	}

	public function safeDown ()
	{
		$this->dropTableIfExists(Field::$table);
	}

}
