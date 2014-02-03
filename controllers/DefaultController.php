<?php

class DefaultController extends CController
{

    public function actionIndex()
    {
        if(Yii::app()->user->isGuest) {
            $this->redirect(array('login'));
        } else {
            $this->redirect(array('status'));
        }
    }
    
    /**
     * Login Action
     */
    public function actionLogin() {
        $model = new LoginForm();
        $form = new CForm('user.views.default.loginForm', $model);

        if ($form->submitted('login') && $form->validate() && $model->login())
            $this->redirect(Yii::app()->user->returnUrl);
        else
            $this->render('login', array('form' => $form));
    }

    /**
     * Logout Action
     */
    public function actionLogout() {
        $assigned_roles = Yii::app()->authManager->getRoles(Yii::app()->user->id); //obtains all assigned roles for this user id
        if (!empty($assigned_roles)) { //checks that there are assigned roles
            $auth = Yii::app()->authManager; //initializes the authManager
            foreach ($assigned_roles as $n => $role) {
                if ($auth->revoke($n, Yii::app()->user->id)) //remove each assigned role for this user
                    Yii::app()->authManager->save(); //again always save the result
            }
        }
        Yii::app()->user->logout(false);
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionStatus()
    {
        $this->render('status');
    }

}