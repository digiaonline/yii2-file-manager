<?php

use yii\db\Schema;
use yii\db\Migration;

class m141202_230426_file_create extends Migration
{
    public function up()
    {
        $this->createTable(
            'file',
            [
                'id' => Schema::TYPE_PK,
                'name' => Schema::TYPE_STRING . ' NOT NULL',
                'extension' => Schema::TYPE_STRING . ' NOT NULL',
                'type' => Schema::TYPE_STRING . ' NOT NULL',
                'size' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
                'hash' => Schema::TYPE_STRING . ' NOT NULL',
                'storage' => Schema::TYPE_STRING . ' NOT NULL',
                'createdAt' => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL',
                'status' => Schema::TYPE_INTEGER . " NOT NULL DEFAULT '0'",
            ]
        );
    }

    public function down()
    {
        $this->dropTable('file');
    }
}
