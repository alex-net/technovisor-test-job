<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\Security;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;
use yii\data\SqlDataProvider;

/**
 * Модель пользователя ..
 */
class User extends Model implements IdentityInterface, DictInterface
{
    use CrudDictTrait;

    const TBL = '{{%employees}}';

    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    const PERM_ALLRULE = 'all-rules';

    const ROLES = [
        self::ROLE_ADMIN => 'Администратор',
        self::ROLE_USER => 'Пользователь',
    ];

    const SCENARIO_LOGIN = 'user-login';

    public $id, $fio, $role, $active, $pass, $authKey;
    private $oldPass;

    public function init()
    {
        parent::init();
        $this->oldPass = $this->pass;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::getById($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public static function getIdsPerRole($role)
    {
        return Yii::$app->db->createCommand('select id from {{%employees}} where role = :role', [':role' => $role])->queryColumn();
    }


    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->pass && Yii::$app->security->validatePassword($password, $this->pass);
    }

    public function rules()
    {
        $rules = [
            [['fio', 'pass'], 'trim', 'on' => static::SCENARIO_DEFAULT],
            ['pass', 'string'],
            ['role', 'default', 'value' => static::ROLE_USER],
            ['fio', 'string', 'max' => 100, 'on' => static::SCENARIO_DEFAULT],
            ['role', 'in', 'range' => array_keys(static::ROLES), 'on' => static::SCENARIO_DEFAULT],
            [['fio', 'role'], 'required', 'on' => static::SCENARIO_DEFAULT],
            ['active', 'boolean', 'on' => static::SCENARIO_DEFAULT],
            [['id', 'pass'], 'required', 'on' => static::SCENARIO_LOGIN, 'message' => 'Поле обязательно для заполнения'],
            ['id', 'in', 'range' => array_keys($this->getOptionList('fio', ['active' => true])), 'on' => static::SCENARIO_LOGIN],
            ['pass', 'passValidator', 'on' => static::SCENARIO_LOGIN],
            ['authKey', 'default', 'value' => Yii::$app->security->generateRandomString(), 'on' => static::SCENARIO_DEFAULT],
        ];
        if (!$this->id) {
            $rules[] = ['pass', 'required', 'on' => static::SCENARIO_DEFAULT];
        }

        return $rules;
    }

    public function passValidator($attr)
    {
        if ($this->id) {
            $u = static::findIdentity($this->id);

            if (!$u->validatePassword($this->pass)) {
                $this->addError($attr, 'Неверный пароль');
            }
            if (!$u->active) {
                $this->addError($attr, 'Доступ запрещён');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'fio' => 'ФИО',
            'role' => 'Роль',
            'active' => 'Активный',
            'pass' => 'Пароль',
        ];
    }

    public function save($data = [])
    {
        if ($data && !$this->load($data) || !$this->validate()) {
            return false;
        }

        if ($this->pass && ($this->oldPass != $this->pass || !$this->id)) {
            $this->pass = Yii::$app->security->generatePasswordHash($this->pass);
        }

        $this->saveObj();

        return true;
    }


    public static function getForList()
    {
        return new SqlDataProvider([
            'sql' => 'select id, fio, active from {{%employees}}',
            'sort' => [
                'attributes' => ['fio', 'active', 'id'],
                'defaultOrder' => ['fio' => SORT_ASC],
            ],
        ]);
    }
}
