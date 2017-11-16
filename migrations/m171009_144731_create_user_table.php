<?php
Code
Issues 68
Pull requests 44
Projects 0
Insights


use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m171009_144731_create_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(16)->notNull(),
            'last_name' => $this->string(16)->notNull(),
            'username' => $this->string(16)->notNull()->unique(),
            'email' => $this->string(32)->notNull()->unique(),
            'password' => $this->string(32)->notNull(),
            'country' => $this->string()->notNull(),
            'created_at' => $this->datetime()->notNull(),
            'isVerified' => $this->integer()->defaultValue(0),
            'isActive' => $this->integer()->defaultValue(0),
            'isBanned' => $this->integer()->defaultValue(0),
            'token' => $this->string(255)->notNull()->unique(),
            'authKey' => $this->string(255)->notNull()->unique(),
            'gender' => $this->integer()->defaultValue(1),
            'role' => $this->integer()->defaultValue(2),
            'pic' => $this->string(255)->defaultValue(null),

        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user');
    }
}
