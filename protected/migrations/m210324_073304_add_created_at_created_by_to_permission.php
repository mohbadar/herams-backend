<?php
declare(strict_types=1);

use yii\db\Migration;

/**
 * Class m210324_073304_add_created_at_created_by_to_permission
 */
class m210324_073304_add_created_at_created_by_to_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%permission}}', 'created_at', $this->integer());
        $this->addColumn('{{%permission}}', 'created_by', $this->integer());

        $this->addForeignKey('fk-permission-created_by-user-id', '{{%permission}}', ['created_by'], '{{%user}}', ['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-permission-created_by-user-id', '{{%permission}}');
        $this->dropColumn('{{%permission}}', 'created_by');
        $this->dropColumn('{{%permission}}', 'created_at');
    }
}
