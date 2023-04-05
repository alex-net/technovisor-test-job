<?php

namespace app\models;

/**
 * Интерфейс для моделек сущностей.. .
 */
interface DictInterface
{
    /**
     * таблица сущности ..
     *
     * @var        string
     */
    const TBL = '';
    /**
     * запрос элемента по ключу ..
     *
     * @param      <type>  $id     The identifier
     */
    public static function getById($id);

    /**
     * Запрос списка для виджета GridView
     */
    public static function getForList();

    /**
     *  Сохранение ...
     *
     * @param      array  $data   Post данные ... пришедшие от клиента ...
     */
    public function save($data = []);

    /**
     * удаление элемента ...
     */
    public function kill();
}