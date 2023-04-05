<?php

namespace app\models;

use yii\base\Model;
use yii\data\SqlDataProvider;
use yii\db\Query;
use yii\db\Expression;
use Yii;

/**
 * модель поставщика ..
 */
class Provider extends Model implements DictInterface
{
    use CrudDictTrait;

    const TBL = '{{%providers}}';

    public $id, $name, $active;

    public function rules()
    {
        return [
            ['name', 'trim'],
            ['name', 'string', 'max' => 100],
            ['name', 'required'],
            ['active', 'boolean'],
            ['active', 'downOther', 'when' => function (){
                return $this->active;
            }],
        ];
    }

    public function downOther()
    {
        Yii::$app->db->createCommand()->update('{{%providers}}', ['active' => false])->execute();
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Наименование поставщика',
            'active' => 'Активный',
        ];
    }

    public static function getForList()
    {
        $q = new Query();
        $q->from(['p' => '{{%providers}}']);
        $q->select(['p.id', 'p.name', 'p.active', 'msco' => new Expression('count(ms.*)')]);
        $q->leftJoin(['ms' => '{{%menu_sections}}'], 'ms.pid = p.id');
        $q->groupBy('p.id');
        $cmd = $q->createCommand();

        return new SqlDataProvider([
            'sql' => $cmd->sql,
            'params' => $cmd->params,
            'sort' => [
                'attributes' => ['name', 'active', 'id'],
                'defaultOrder' => ['name' => SORT_ASC],
            ],
        ]);
    }
}