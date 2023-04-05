<?php

namespace app\models;

use yii\base\Model;
use yii\data\SqlDataProvider;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\StringHelper;
use yii\validators\DateValidator;
use Yii;

use app\models\User;
use app\models\OrderItem;

/**
 * модель заказа ...
 */
class Order extends Model implements DictInterface
{
    use CrudDictTrait;

    const TBL = '{{%orders}}';

    /**
     * аремя переноса заказов на следующий день ..
     */
    const TIME_LIMIT = [12, 30];

    public $id, $eid, $date;

    public $items;

    public function init()
    {
        parent::init();
        if (!$this->date) {
            $this->date = date('Y-m-d', $this->getMinTime());
        }
        $this->items = [];
        // загрузка данных ..
        if ($this->id) {
            $list = Yii::$app->db->createCommand('select * from {{%orders_items}} where oid = :id', [':id' => $this->id])->queryAll();
            foreach ($list as $item) {
                unset($item['oid']);
                $this->items[] = new OrderItem($item);
            }
        }
        if (empty($this->items)) {
            $this->items = [new OrderItem()];
        }
    }

    /**
     * вернуть ограничение по времени ...
     *
     * @return     int   The minimum time.
     */
    private function getMinTime()
    {
        $minTime = time();
        // заказы после 12:30 переносим на послезавтра ..
        if ($minTime >= call_user_func_array('mktime', static::TIME_LIMIT)) {
            $minTime = strtotime('+1 day', $minTime);
        }
        $minTime = strtotime('+1 day', $minTime);
        return $minTime;
    }

    public function rules()
    {
        $minTime = $this->getMinTime();
        return [
            ['eid', 'required'],
            ['eid', 'in', 'range' => array_keys(User::getOptionList('fio', ['active' => true]))],
            ['date', 'default', 'value' => date('Y-m-d')],
            ['date', 'date', 'format' => 'php:Y-m-d', 'min' => strtotime('-1 day', $minTime), 'minString' => date('Y-m-d', $minTime)],
        ];
    }

    public function attributeLabels()
    {
        return [
            'eid' => 'Сотрудник',
            'date' => 'Дата испольнения заказа',
        ];
    }

    public function attributeHints()
    {
        $time = date('H:i', call_user_func_array('mktime', static::TIME_LIMIT));
        return [
            'date' => "Заказы после $time переносятся на послезавтра ",
        ];
    }

    /**
     * Загрузка заказа ... из данных форм
     */
    public function load($data, $fn = null)
    {
        $ret = parent::load($data, $fn);
        $oiKey = StringHelper::basename(OrderItem::class);
        if (!empty($data[$oiKey])) {
            $this->items = [];
            for ($i = 0; $i < count($data[$oiKey]); $i++) {
                $this->items[] = new OrderItem();
            }
        }
        $ret = $ret && Model::loadMultiple($this->items, $data);

        return $ret;
    }

    /**
     * Валидация заказа ..
     */
    public function validate($attsNames = null, $clearErr = true)
    {
        $ret = parent::validate($attsNames, $clearErr);
        $ret = $ret && Model::validateMultiple($this->items);
        return $ret;
    }

    /**
     * сохранение заказа
     *
     * @return     bool   ( description_of_the_return_value )
     */
    public function save($data = [])
    {
        if (!$this->validate()) {
            return false;
        }

        $this->saveObj();

        $items = [];
        $dishs = [];
        foreach ($this->items as $item) {
            if (!isset($dishs[$item->did])) {
                $dishs[$item->did] = 0;
            }
            $dishs[$item->did] += $item->qty;
        }
        foreach ($dishs as $did => $qty) {
            $items[] = [
                'did' => $did,
                'qty' => $qty,
                'oid' => $this->id,
            ];
        }
        // удаляем то что было
        Yii::$app->db->createCommand()->delete('{{%orders_items}}', ['oid' => $this->id])->execute();
        Yii::$app->db->createCommand()->batchInsert('{{%orders_items}}', array_keys($items[0]), $items)->execute();
        return true;
    }

    /**
     * формирование списка заказов ..с филльтрацией по сотруднику ..
     *
     * @param      integer           $eId    Id соткудника .. .
     * @param       string           $date   дата для фильтра заказов
     *
     * @return     SqlDataProvider  For list.
     */
    public static function getForList($eId = null, $date = null)
    {
        $q = new Query();

        $q->from(['o' => '{{%orders}}']);
        $q->leftJoin(['e' => '{{%employees}}'], 'e.id = o.eid');
        $q->leftJoin(['oi' => '{{%orders_items}}'], 'o.id = oi.oid');
        $q->leftJoin(['d' => '{{%dish}}'], 'oi.did = d.id');

        $q->select([
            'o.*',
            'e.fio',
            'list' => new Expression("string_agg(d.name||'|'||d.price||'|'||oi.qty , '<>')"),
            'sum' => new Expression('sum(d.price*oi.qty)'),
        ]);
        $where = [];
        if ($eId) {
            $where['o.eid'] =  $eId;
        }

        if ($date) {
            $dateValid = new DateValidator(['format' => 'php:Y-m-d']);
            if ($dateValid->validate($date)) {
                $where['o.date'] = $date;
            }

        }
        if ($where) {
            $q->where($where);
        }
        $q->groupBy(['o.id', 'e.fio']);
        $cmd = $q->createCommand();

        return new SqlDataProvider([
            'sql' => $cmd->sql,
            'params' => $cmd->params,
            'sort' => [
                'attributes' => ['id', 'eid', 'date', 'fio'],
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);
    }

    /**
     * отчёт по заказу поставщику на дату ...
     *
     * @param string $data  дата фильрации
     */
    public static function getReportForProvider($date)
    {
        if (!$date) {
            return;
        }
        $q = new Query();
        $q->from(['o' => '{{%orders}}']);
        $q->leftJoin(['oi' => '{{%orders_items}}'], 'o.id = oi.oid');
        $q->leftJoin(['d' => '{{%dish}}'], 'oi.did = d.id');
        $q->select([
            'd.name',
            'd.price',
            'qty' => new Expression('sum(oi.qty)'),
        ]);
        $q->groupBy(['d.id']);

        $q->where(['o.date' => $date]);
        $q->orderBy('d.name asc');
        $cmd = $q->createCommand();

        return new SqlDataProvider([
            'sql' => $cmd->sql,
            'params' => $cmd->params,
            'pagination' => false,
        ]);
    }

    /**
     * Отчёт по заказанным блюдам по сотрудникам...
     *
     * @param      <type>           $date      The date
     * @param      bool             $perMonth  The per month
     *
     * @return     SqlDataProvider  The report for employees.
     */
    public static function getReportForEmployees($date, $perMonth = false)
    {
        if (!$date){
            return;
        }
        // собираем сотрудников ..с заказов на оопределнённую дату
        $q = new Query();
        $q->from(['e' => 'employees']);
        $q->leftJoin(['o' => '{{%orders}}'], 'o.eid = e.id');
        $q->leftJoin(['oi' => '{{%orders_items}}'], 'o.id = oi.oid');
        $q->leftJoin(['d' => '{{%dish}}'], 'oi.did = d.id');
        $q->groupBy('e.id');
        $q->orderBy(['e.fio' => SORT_ASC] );
        $q->select([
            'e.fio',
            'list' => new Expression("string_agg(d.id||'|'||d.name||'|'||d.price||'|'||oi.qty , '<>' order by d.name )"),
            'sum' => new Expression('sum(d.price*oi.qty)'),
        ]);


        if ($perMonth) {
            $q->where(new Expression("date_part('month', o.date) = :mon", [':mon' => date('n', strtotime($date))]) );
        } else {
            $q->where(['o.date' => $date]);
        }

        $cmd = $q->createCommand();

        return new SqlDataProvider([
            'sql' => $cmd->sql,
            'params' => $cmd->params,
            'pagination' => false,
        ]);
    }


    /**
     * запрос  владельца заказа ..
     *
     * @param      <type>  $id     Номер заказаа r
     */
    public static function getOwner($id)
    {
        return Yii::$app->db->createCommand('select eid from {{%orders}} where id = :id', [':id' => $id])->queryScalar();
    }
}