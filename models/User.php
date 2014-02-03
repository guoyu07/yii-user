<?php

class User extends CActiveRecord
{

    /**
     * @var string
     */
    public static $usernameRegex='/^[A-Za-z0-9-_]+$/';

    /**
     * @var string
     */
    private $currentPassword;

    /**
     * @param string $className
     * @return User
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Initializer
     */
    public function init()
    {
        $this->attachEventHandler('onAfterFind', array($this, 'afterFindHandler'));
    }

    /**
     * @return string
     */
    public function tableName()
    {
        return 'user';
    }

    /**
     * @return array
     */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
            'username' => 'Benutzername',
			'password' => 'Passwort',
            'salt' => 'Salt',
			'email' => 'E-Mail'
		);
	}

    /**
     * @return array
     */
    public function rules()
    {
        // Die Regeln sind pro Attribut definiert
        return array(
            // username
            array('username', 'required'),
            array('username', 'length', 'min' => 4, 'max' => 32, 'encoding' => 'utf-8'),
            array('username', 'match', 'pattern'=>User::$usernameRegex),
            array('username', 'unique', 'className' => 'User'),
            // password
            array('password', 'required', 'on' => 'insert'),
            array('password', 'length', 'min' => 6, 'encoding' => 'utf-8'),
            array('password', 'passwordValidator'),
            // email
            array('email', 'required'),
            array('email', 'length', 'max' => 50, 'encoding' => 'utf-8'),
            array('email', 'email'),
            // SEARCH
            array('id,username,email', 'safe', 'on' => 'search')
        );
    }

    /**
     * @return boolean
     */
    protected function beforeSave()
    {
        if(parent::beforeSave()) {
            if($this->isNewRecord) {
                if(empty($this->created)) $this->created=new CDbExpression('NOW()');
                if(empty($this->modified)) $this->modified=new CDbExpression('NOW()');
            } else {
                if(empty($this->modified)) $this->modified=new CDbExpression('NOW()');
            }
            return true;
        }
        return false;
    }

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return ActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id,false);
        $criteria->compare('username',$this->username,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('deleted',0,false);
		return new ActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'sort'=>array(
                'defaultOrder'=>'username ASC',
            )
		));
	}

    /**
     * @param string $attribute
     * @param array $params
     */
    public function passwordValidator($attribute, $params)
    {
        if (empty($this->password)) {
            $this->password = $this->currentPassword;
        } else {
            $this->salt = $this->generateSalt();
            $this->password = $this->hashPassword($this->password, $this->salt);
        }
    }

	/**
	 * Checks if the given password is correct.
	 * @param string the password to be validated
	 * @return boolean whether the password is valid
	 */
	public function validatePassword($password)
	{
		return $this->hashPassword($password,$this->salt)===$this->password;
	}

	/**
	 * Generates the password hash.
	 * @param string password
	 * @param string salt
	 * @return string hash
	 */
	public function hashPassword($password,$salt)
	{
		return md5($salt.$password);
	}

	/**
	 * Generates a salt that can be used to generate a password hash.
	 * @return string the salt
	 */
	public function generateSalt()
	{
		return uniqid('',true);
	}

    /**
     * @param CEvent $event
     */
    protected function afterFindHandler($event)
    {
        // Über $event->sender hat man Zugriff auf das aktuelle Model
        $this->currentPassword = $event->sender->password;
    }

}