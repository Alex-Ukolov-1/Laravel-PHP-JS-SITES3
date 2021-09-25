<?php

namespace App\View;

use App\View\Components\Input;

class RenderRelation
{
    public function render($field, $form_type) {
    	$class = new $field['source'];

    	if ($form_type === 'create') $fields = $class->getCreateFields();
    	if ($form_type === 'edit') $fields = $class->getEditFields();

    	$result = '';

    	foreach ($fields as $field) {
    		$component = new Input($field, null);

	    	$component->withName('input');
    		$component->withAttributes([]);

    		$result .= $component->renderToString();
    	}

    	return $result;
    }
}
