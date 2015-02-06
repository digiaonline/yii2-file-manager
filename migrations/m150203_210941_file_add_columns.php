<?php

use yii\db\Schema;
use yii\db\Migration;

class m150203_210941_file_add_columns extends Migration
{
    public function up()
    {
        $this->addColumn('file', 'folder', Schema::TYPE_STRING . ' NULL DEFAULT NULL AFTER `extension`');
    }

    public function down()
    {
        $this->dropColumn('file', 'folder');
    }
}
