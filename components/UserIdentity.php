<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{

    /**
     * Die ID des angemeldeten Benutzers
     *
     * @var integer
     */
    public $id;

    /**
     * Authentifiziert den Benutzer
     *
     * @return integer
     */
    public function authenticate()
    {
        $user = User::model()->find('deleted=0 AND LOWER(username)=?', array(strtolower($this->username)));
        if ($user === null)
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        else if (!$user->validatePassword($this->password))
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        else {
            $this->id = $user->id;
            $this->username = $user->username;
            $this->errorCode = self::ERROR_NONE;
            $user->lastLogin = new CDbExpression('NOW()');
            $user->save(false, array('lastLogin'));
            
            $auth=Yii::app()->authManager;
            if(!$auth->isAssigned($user->role,$this->id))
            {
                if($auth->assign($user->role,$this->id))
                {
                    Yii::app()->authManager->save();
                }
            }            
            
        }
        
        #print_r($this);exit;
        
        return $this->errorCode == self::ERROR_NONE;
    }

    /**
     * Gibt die ID des Benutzers zurück. Übersteuert CUserIdentity::getId(),
     * welche nur den Benutzernamen zurückgibt.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

}