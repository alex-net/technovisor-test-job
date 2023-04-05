<?php

namespace app\models;

use yii\base\Model;
use yii\data\SqlDataProvider;
use yii\db\Query;
use yii\db\Expression;
use Yii;

use app\models\Provider;

/**
 * модель меню ...
 */
class Menu extends Model implements DictInterface
{
    use CrudDictTrait;

    const TBL = '{{%menu_sections}}';

    public $id, $name, $pid;

    public function rules()
    {
        return [
            ['name', 'trim'],
            ['name', 'string', 'max' => 100],
            ['name', 'required'],
            ['pid', 'required'],
            ['pid', 'in', 'range' => array_keys(Provider::getOptionList())]
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Наименование раздела меню',
            'pid' => 'Поставщик',
        ];
    }

    public static function getForList($filter = [])
    {
        $q = new Query();
        $q->from(['m' => static::TBL]);
        $q->select(['m.id', 'm.name', 'dsco' => new Expression('count(ds.*)')]);
        if ($filter) {
            $q->where($filter);
        }
        $q->leftJoin(['ds' => '{{%dish}}'], 'ds.mid = m.id');
        $q->groupBy('m.id');
        $cmd = $q->createCommand();

        return new SqlDataProvider([
            'sql' => $cmd->sql,
            'params' => $cmd->params,
            'sort' => [
                'attributes' => ['name', 'id'],
                'defaultOrder' => ['name' => SORT_ASC],
            ],
        ]);
    }
}