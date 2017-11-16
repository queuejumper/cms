<?php

use yii\db\Migration;

/**
 * Handles the creation of table `notification`.
 * Has foreign keys to the tables:
 *
 * - `user`
 */
class m171104_224354_create_notification_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('notification', [
            'id' => $this->primaryKey(16),
            'user_id' => $this->integer(16)->notNull(),
            'type' => $this->integer(2)->notNull(),
            'type_id' => $this->integer(2)->notNull(),
            'message' => $this->integer(2)->notNull(),
            'ref' => $this->string(64)->defaultValue(null),
            'seen' => $this->integer(2)->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->notNull(32)
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-notification-user_id',
            'notification',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-notification-user_id',
            'notification',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-notification-user_id',
            'notification'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-notification-user_id',
            'notification'
        );

        $this->dropTable('notification');
    }
}
