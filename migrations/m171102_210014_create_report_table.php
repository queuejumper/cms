<?php

use yii\db\Migration;

/**
 * Handles the creation of table `report`.
 * Has foreign keys to the tables:
 *
 * - `user`
 * - `post`
 */
class m171102_210014_create_report_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('report', [
            'created_at' => $this->dateTime(),
            'user_id' => $this->integer()->notNull(),
            'post_id' => $this->integer()->notNull(),
        ]);

        $this->addPrimaryKey(
            'report_pk', 
            'report', 
            ['user_id', 'post_id']
        );

        // creates index for column `user_id`
        $this->createIndex(
            'idx-report-user_id',
            'report',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-report-user_id',
            'report',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        // creates index for column `post_id`
        $this->createIndex(
            'idx-report-post_id',
            'report',
            'post_id'
        );

        // add foreign key for table `post`
        $this->addForeignKey(
            'fk-report-post_id',
            'report',
            'post_id',
            'post',
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
            'fk-report-user_id',
            'report'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-report-user_id',
            'report'
        );

        // drops foreign key for table `post`
        $this->dropForeignKey(
            'fk-report-post_id',
            'report'
        );

        // drops index for column `post_id`
        $this->dropIndex(
            'idx-report-post_id',
            'report'
        );

        $this->dropTable('report');
    }
}
