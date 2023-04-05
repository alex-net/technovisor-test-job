<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Трейт для осуществления стандартных действий с модельками ...
 * по сути можно было обойти абстрактным классом .. но ...
 */
trait CrudDictTrait
{
    /**
     * загрузка
     *
     * @param      <type>  $id     The identifier
     */
    public static function getById($id)
    {
        if (!$id) {
            return;
        }
        $q = new Query();
        $q->from(static::TBL);
        $q->where(['id' => $id]);
        $q->limit(1);
        $res = $q->one();
        if ($res) {
            return new static($res);
        }
    }

    /**
     * удаление
     */
    public function kill()
    {
        if (!$this->id) {
            return false;
        }

        Yii::$app->db->createCommand()->delete(static::TBL, ['id' => $this->id])->execute();
        return true;
    }

    /**
     * сохранение уровня объекта в базу  ...
     */
    private function saveObj()
    {
        $attrs = $this->getAttributes($this->activeAttributes(), ['id']);
        if ($this->id) {
            Yii::$app->db->createCommand()->update(static::TBL, $attrs, ['id' => $this->id])->execute();
        } else {
            Yii::$app->db->createCommand()->insert(static::TBL, $attrs, )->execute();
            $this->id = Yii::$app->db->lastInsertID;
        }
    }

    /**
     * обычное схраненик с валидацией ..и загрузкой из данных формы
     *
     * @return     bool   ( description_of_the_return_value )
     */
    public function save($data = [])
    {
        if ($data && !$this->load($data) || !$this->validate()) {
            return false;
        }

        $this->saveObj();
        return true;
    }

    /**
     * Генерация выпадающего  списка из элементов сущности
     *
     * @param      string  $nameField  основное отображаемое поле ...
     * @param      array   $filtred    доп. фильтьрация .. массив where ...
     *
     * @return     <type>  The option list.
     */
    public static function getOptionList($nameField = 'name', $filtred = [])
    {
        static $list = [];
        $key = $nameField . json_encode($filtred);
        if (!isset($list[$key])) {
            $q = new Query();
            $q->select(['id', $nameField]);
            if ($filtred) {
                $q->where($filtred);
            }
            $q->from(static::TBL);
            $all = $q->all();
            $list[$key] = ArrayHelper::map($all, 'id', $nameField);
        }
        return $list[$key];
    }
}