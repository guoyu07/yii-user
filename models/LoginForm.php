<?php

class LoginForm extends CFormModel
{

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var UserIdentity
     */
    private $_identity;

    /**
     * @return array
     */
    public function rules()
    {
        return array(
            array('username, password', 'required'),
        );
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array(
            'username' => 'Username',
            'password' => 'Password'
        );
    }

    /**
     * @return boolean
     */
    public function login()
    {
        if ($this->_identity === null) {
            $this->_identity = new UserIdentity($this->username, $this->password);
            $this->_identity->authenticate();
        }

        if ($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
            return Yii::app()->user->login($this->_identity, 0);
        }
        else
            return false;
    }

}
