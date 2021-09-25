<?php

namespace App\Http\Controllers\Traits;

trait FieldsGetters {

	public function getTableFields() {
		return $this->tableFields;
	}

	public function getCreateFields() {
		return $this->createFields;
	}

	public function getEditFields() {
		return $this->editFields;
	}

	public function getShowFields() {
		return $this->showFields;
	}

	public function getExportFields() {
		return $this->exportFields;
	}

}
