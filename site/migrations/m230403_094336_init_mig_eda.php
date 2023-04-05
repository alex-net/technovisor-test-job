<?php

use yii\db\Migration;
use app\models\User;

/**
 * Class m230403_094336_init_mig_eda
 */
class m230403_094336_init_mig_eda extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // сотрудники
        $this->createTable('{{%employees}}', [
            'id' => $this->primaryKey()->notNull()->comment('Ключик'),
            'fio' => $this->string(100)->notNull()->comment('ФИО'),
            'role' => $this->string(10)->notNull()->defaultValue(User::ROLE_USER),
            'authKey' => $this->string(32)->notNull(),
            'pass' => $this->string(60)->notNull(),
            'active' => $this->boolean()->defaultValue(false)->notNull(),
        ]);
        $this->addCommentOnTable('{{%employees}}', 'Сотрудники');
        foreach (['fio', 'role', 'active', 'authKey'] as $key) {
            $this->createIndex("employees-$key-ind", '{{%employees}}', [$key]);
        }

        $admin = new User([
            'fio' => 'Админ',
            'role' => User::ROLE_ADMIN,
            'active' => true,
            'pass' => '1234',
        ]);
        $admin->save();

        // поставщики ...
        $this->createTable('{{%providers}}', [
            'id' => $this->primaryKey()->notNull()->comment('Ключик'),
            'name' => $this->string(100)->notNull()->comment('Наименование поставшика'),
            'active' => $this->boolean()->notNull()->defaultValue(false)->comment('Активный поставшик'),
        ]);
        $this->addCommentOnTable('{{%providers}}', 'Поставщики');
        $this->createIndex('providers-activ-ind', '{{%providers}}', ['active']);

        // разделы меню ... от поставщиков ...
        $this->createTable('{{%menu_sections}}', [
            'id' => $this->primaryKey()->comment('Ключик раздела меню'),
            'name' => $this->string(100)->notNull()->comment('Наименование раздела'),
            'pid' => $this->integer()->notNull()->comment('ссылка на поставшика'),
        ]);
        $this->addForeignKey('menu-section-providers-fk', '{{%menu_sections}}', ['pid'], '{{%providers}}', ['id'], 'cascade', 'cascade');
        $this->addCommentOnTable('{{%menu_sections}}', 'разделы меню поставшиков еды');

        // блюда ...
        $this->createTable('{{%dish}}', [
            'id' => $this->primaryKey()->comment('Ключик одной еды'),
            'name' => $this->string(100)->notNull()->comment('Наименование блюда'),
            'descr' => $this->text()->comment('Описание/состав'),
            'price' => $this->decimal(8, 2)->notNull()->check('price > 0')->comment('Ценник'),
            'mid' => $this->integer()->notNull()->comment('Раздел меню'),
            'active' => $this->boolean()->notNull()->defaultValue(false)->comment('Доступное блюдо'),
        ]);
        $this->addForeignKey('dish-menu-section-fk', '{{%dish}}', ['mid'], '{{%menu_sections}}', ['id'], 'cascade', 'cascade');
        $this->addCommentOnTable('{{%dish}}', 'Блюда');

        // заказы
        $this->createTable('{{%orders}}', [
            'id' => $this->primaryKey()->comment('Ключик заказа'),
            'eid' => $this->integer()->notNull()->comment('Ссылка на сортрудника'),
            'date' => $this->date()->notNull()->defaultExpression("current_date + '1 day'::interval ")->comment('Дата исполнения заказа'),
        ]);
        $this->addCommentOnTable('{{%orders}}', 'Заказы сотрудников');
        $this->addForeignKey('orders-eid-fk','{{%orders}}', ['eid'], '{{%employees}}', ['id'], 'cascade', 'cascade');
        foreach (['eid', 'date'] as $field) {
            $this->createIndex("orders-$field-ind", '{{%orders}}', [$field]);
        }

        // элементы заказов ..
        $this->createTable('{{%orders_items}}', [
            'oid' => $this->integer()->notNull()->comment('Ссылка на заказ'),
            'did' => $this->integer()->notNull()->comment('Ссылка на блюдо'),
            'qty' => $this->integer()->notNull()->check('qty > 0')->comment('Количество'),
        ]);
        $this->addCommentOnTable('{{%orders_items}}', 'Элементы заказов');
        $this->addForeignKey('orders-items-oid-fk', '{{%orders_items}}', ['oid'], '{{orders}}', ['id'], 'cascade', 'cascade');
        $this->addForeignKey('orders-items-did-fk', '{{%orders_items}}', ['did'], '{{dish}}', ['id'], 'cascade', 'cascade');
    }



    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        foreach (['orders_items', 'orders', 'dish', 'menu_sections', 'providers', 'employees'] as $tbl) {
            $this->dropTable("{{%$tbl}}");
        }

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230403_094336_init_mig_eda cannot be reverted.\n";

        return false;
    }
    */
}
