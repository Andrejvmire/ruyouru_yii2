<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $email Е почта
 * @property string|null $first_name Имя
 * @property string|null $last_name Фамилия
 * @property string|null $phone_number Номер телефона
 * @property string $password Пароль
 */
class User extends ActiveRecord implements IdentityInterface
{

    const SCENARIO_ADD_DATA = 'ADD_DATA';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'users';
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        $uid = (string)$token->getClaim('uid');
        return static::findOne(['id' => $uid]);
    }

    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_ADD_DATA] = ['first_name', 'last_name', 'phone_number'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['email', 'password'], 'required'],
            [['email', 'password'], 'string', 'max' => 45],
            [['first_name', 'last_name', 'phone_number'], 'string', 'max' => 20],
            [['password'], 'string', 'min' => 6],
            ['email', 'unique']
        ];
    }

    /**
     * @throws Exception
     */
    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->password = Yii::$app->security->generatePasswordHash($this->password);
            }
            return true;
        }
        return false;
    }

    public function fields(): array
    {
        return [
            'id', 'email', 'first_name', 'last_name', 'phone_number'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'phone_number' => 'Phone Number',
            'password' => 'Password',
        ];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        //
    }

    public function validateAuthKey($authKey)
    {
        //
    }
}
