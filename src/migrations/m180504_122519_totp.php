<?php

use yii\db\Migration;

/**
 * Class m180504_122519_totp
 */
class m180504_122519_totp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'totp_secret', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'totp_secret');
    }
}
