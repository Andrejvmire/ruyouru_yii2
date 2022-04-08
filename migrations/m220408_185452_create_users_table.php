<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m220408_185452_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string(45)->notNull()->comment('Е почта'),
            'first_name' => $this->string(20)->null()->comment('Имя'),
            'last_name' => $this->string(20)->null()->comment('Фамилия'),
            'phone_number' => $this->string(20)->null()->comment('Номер телефона'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
