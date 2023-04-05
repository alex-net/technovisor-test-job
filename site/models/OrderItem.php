<?php

namespace app\models;

use yii\base\Model;
use Yii;
use yii\db\Query;

/**
 * 'элемент одного заказанного блюда ...
 */
class OrderItem extends Model
{
    public $did, $qty = 1;

    public function rules()
    {
        return [
            ['qty', 'integer', 'min' => 1],
            ['qty', 'default', 'value' => 1],
            [['qty', 'did'], 'required'],
            ['did', 'in', 'range' => $this->didOptionsList(false)],
        ];
    }

    public function attributeLabels()
    {
        return [
            'qty' => 'Количество',
            'did' => 'Блюдо',
        ];
    }

    public function didOptionsList($forSelect = true)
    {
        static $list;
        if (!isset($list)) {
            $q = new Query();
            $q->from(['d' => '{{%dish}}']);
            $q->select(['d.id', 'd.price', 'dname' => 'd.name', 'mname' => 'm.name']);
            $q->leftJoin(['m' => '{{%menu_sections}}'], 'm.id = d.mid');
            $q->leftJoin(['p' => '{{%providers}}'], 'p.id = m.pid');
            $q->where([
                'd.active' => true,
                'p.active' => true,
            ]);
            $q->orderBy([
                'mname' => SORT_ASC,
                'dname' => SORT_ASC,
            ]);
            $q->indexBy('id');
            $list = $q->all();
        }

        $items = [];
        if ($forSelect) {
            foreach ($list as $item) {
                $items[$item['mname']][$item['id']] = "{$item['dname']} ({$item['price']} р.)";
            }
        } else {
            $items = array_keys($list);
        }
        return $items;

    }
}