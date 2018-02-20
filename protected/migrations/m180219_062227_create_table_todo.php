<?php

use yii\db\Migration;

/**
 * Class m180219_062227_create_table_todo
 */
class m180219_062227_create_table_todo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%todo}}', [
            'id' => $this->primaryKey(),
            'text' => $this->string(255)->notNull(),
            'userId' => $this->integer()->notNull(),
            'status' => $this->integer(1)->notNull()->defaultValue('0'),
            'updatedAt' => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('NOW()'),
        ]);

        $this->createIndex('index-todo_user_id', '{{%todo}}', 'userId');

        $this->addForeignKey(
            'fk-todo_user_id',
            '{{%todo}}',
            'userId',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-todo_user_id', '{{%todo}}');
        $this->dropTable('{{%todo}}');
    }
}
