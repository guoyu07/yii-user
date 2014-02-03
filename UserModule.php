<?php

class UserModule extends CWebModule
{

    public function init()
	{
		// import the module-level models and components
		$this->setImport(array(
			'user.components.*',
			'user.models.*'
		));

	}

}
