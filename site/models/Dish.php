<?php

namespace app\models;

use yii\base\Model;
use yii\data\SqlDataProvider;
use yii\db\Query;
use yii\db\Expression;
use Yii;

use app\models\Provider;

/**
 * модель блюда ...
 */
class Dish extends Model implements DictInterface
{
    use CrudDictTrait;

    const TBL = '{{%dish}}';

    public $id, $name, $mid, $descr, $price, $active;

    private $pid;

    public function setProvider ($val)
    {
        $this->pid = $val;
    }

    public function rules()
    {
        return [
            [['name', 'descr'], 'trim'],
            ['descr', 'string'],
            ['price', 'double', 'min' => 0],
            ['name', 'string', 'max' => 100],
            [['name', 'price'], 'required'],
            ['mid', 'required'],
            ['mid', 'in', 'range' => array_keys(Menu::getOptionList('name', ['pid' => $this->pid]))],
            ['active', 'boolean'],

        ];
    }

    public function downOther()
    {
        Yii::$app->db->createCommand()->update('{{%providers}}', ['active' => false])->execute();
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Наименование блюда',
            'price' => 'Ценник за единицу',
            'active' => 'Активный',
            'descr' => 'Состав, описание',
            'mid' => 'Раздел меню',
        ];
    }

    public static function getForList($filter = [])
    {
        $q = new Query();
        $q->from(static::TBL);
        $q->select(['id', 'name', 'active', 'price']);
        if ($filter) {
            $q->where($filter);
        }
        $cmd = $q->createCommand();

        return new SqlDataProvider([
            'sql' => $cmd->sql,
            'params' => $cmd->params,
            'sort' => [
                'attributes' => ['name', 'id', 'price'],
                'defaultOrder' => ['name' => SORT_ASC],
            ],
        ]);
    }
}