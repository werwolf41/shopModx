<?php

class msVendorRemoveProcessor extends modObjectRemoveProcessor  {
	public $classKey = 'msVendor';
	public $languageTopics = array('minishop2');
	public $permission = 'mssetting_save';


	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}

}
return 'msVendorRemoveProcessor';