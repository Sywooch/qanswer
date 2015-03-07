<?php

use yii\db\Schema;
use yii\db\Migration;

class m150307_113818_init_user extends Migration
{
    public function up()
    {
        $this->createTable('{{%token}}', [
            'id'        => Schema::TYPE_INTEGER . ' NOT NULL',
            'code'      => Schema::TYPE_STRING . '(32) NOT NULL',
            'created_at'=> Schema::TYPE_INTEGER . ' NOT NULL',
            'type'      => Schema::TYPE_SMALLINT . ' NOT NULL'
        ]);

        $this->createIndex('token_unique', '{{%token}}', ['id', 'code', 'type'], true);
    }

    public function down()
    {
        $this->dropTable('{{%token}}');
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}
