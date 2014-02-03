<?php

return array(
    'title' => 'Login to the backend',
    'elements' => array(
        'username' => array(
            'type' => 'text',
            'maxlength' => 32,
            'autofocus' => 'autofocus'
        ),
        'password' => array(
            'type' => 'password',
            'maxlength' => 32,
        ),
    ),
    'buttons' => array(
        'login' => array(
            'type' => 'submit',
            'label' => 'Log in',
            'class' => 'btn'
        ),
    ),
);