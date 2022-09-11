<?php
use wcf\system\database\table\DatabaseTableChangeProcessor;
use wcf\system\database\table\PartialDatabaseTable;
use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\index\DatabaseTableForeignKey;
use wcf\system\WCF;

$tables = [
		// add new column absentRepID in wcf1_user1 with foreign key
		PartialDatabaseTable::create('wcf1_user')
			->columns([
					IntDatabaseTableColumn::create('absentRepID')
						->length(10)
						->defaultValue(null),
			])
			->foreignKeys([
					DatabaseTableForeignKey::create()
						->columns(['absentRepID'])
						->referencedTable('wcf1_user')
						->referencedColumns(['userID'])
						->onDelete('SET NULL'),
			])
];

(new DatabaseTableChangeProcessor(
		$this->installation->getPackage(),
		$tables,
		WCF::getDB()->getEditor())
)->process();
