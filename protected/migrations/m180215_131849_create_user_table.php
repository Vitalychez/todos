<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m180215_131849_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string(255)->notNull(),
            'passwordHash' => $this->string(60)->notNull(),
            'authKey' => $this->string(32)->notNull(),
            'isActivate' => $this->integer(1)->notNull()->defaultValue('0'),
            'updatedAt' => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('NOW()'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
