<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Input extends Component
{

    public $xAttributes;

    public $required;
    public $list;
    public $selected_value;
    public $name;
    public $value;
    public $date;
    public $time;
    public $title;
    public $checked;
    public $source_route;
    public $controller;
    public $class;
    public $select2_enable_create;
    public $boolean_turn_on;
    public $boolean_turn_off;

    private function addAttribute($name, $value) {
        $this->xAttributes[$name] = $value;
    }

    private function getListFrom($source, $scope) {
        if (empty($source)) return [];

        $model = new $source;

        if ($scope) {
            return $model->{$scope}()->list();
        } else {
            return $model->list();
        }
    }

    public function __construct($field, $item)
    {
        list('type' => $type,
            'class' => $class,
            'name' => $name,
            'title' => $title,
            'required' => $required,
            'onchange' => $onchange,
            'source' => $source,
            'scope' => $scope,
            'boolean_turn_on' => $boolean_turn_on,
            'boolean_turn_off' => $boolean_turn_off,
            'default_value' => $default_value) = $field;

        $this->title = $title;
        $this->class = $class;
        $this->item = $item;
        $this->name = $name;
        $this->required = $required;
        $this->default_value = $default_value;
        $this->boolean_turn_on = $boolean_turn_on;
        $this->boolean_turn_off = $boolean_turn_off;

        $this->type = $type;

        if ($item) $this->item_value = $item->{$name};
        else $this->item_value = null;

        if ($required) $this->addAttribute('required', 'required');
        if ($name) $this->addAttribute('name', $name);
        if ($onchange) $this->addAttribute('onchange', $onchange);

        if ($type === 'select') $this->select($source, $scope);
        else if ($type === 'select2') $this->select2($field);
        else if ($type === 'float') $this->float();
        else if ($type === 'boolean') $this->boolean();
        else if ($type === 'boolean:checkbox') $this->checkbox();
        else if ($type === 'textarea') $this->textarea();
        else if ($type === 'datetime') $this->datetime();
        else if ($type === 'multiselect-labels') $this->multiselectLabels($source, $scope);
        else if ($type === 'file_table') $this->file_table($source, $scope);
        else $this->input();
    }

    private function checkbox() {
        if ($this->item && (string)$this->item_value === '1') {
            $this->addAttribute('checked', 'checked');
        }

        $this->view = 'components.inputs.checkbox';
    }

    private function input() {
        $this->addAttribute('type', $this->type);

        if ($this->item) $value = $this->item_value;
        else if ($this->default_value) $value = $this->default_value;
        else if ($this->type === 'date') $value = date('Y-m-d');
        else if ($this->type === 'time') $value = date('H:i');
        else $value = '';

        $this->addAttribute('value', $value);

        $this->view = 'components.inputs.text';
    }

    private function multiselectLabels($source, $scope) {
        if ($this->item) {
            $this->value = $this->item_value->implode('id', ',');
        } else {
            $this->value = '';
        }

        $this->list = $this->getListFrom($source, $scope);

        $this->view = 'components.inputs.multiselect-labels';
    }

    private function datetime() {
        if ($this->item) {
            $this->date = explode(' ', $this->item_value)[0];
            $this->time = explode(' ', $this->item_value)[1];
            $this->value = $this->item_value;
        } else {
            $this->date = date('Y-m-d');
            $this->time = date('H:i');
            $this->value = date('Y-m-d H:i');
        }

        $this->view = 'components.inputs.datetime';
    }

    private function textarea() {
        $this->value = $this->item_value ?? '';

        $this->view = 'components.inputs.textarea';
    }

    private function select($source, $scope) {
        $this->list = $this->getListFrom($source, $scope);

        if ($this->item) {
            $this->selected_value = (string)$this->item_value;
        } else if ($this->default_value) {
            $this->selected_value = (string)$this->default_value;
        } else {
            $model = new $source;
            $this->selected_value = (string)$model->getDefaultId();
        }

        $this->view = 'components.inputs.select';
    }

    private function select2($field) {
        $this->select($field['source'], $field['scope']);

        $this->source_route = $field['source_route'];
        $this->controller = $field['controller'];
        $this->select2_enable_create = $field['select2_enable_create'] ?? false;

        $this->view = 'components.inputs.select2';
    }

    private function float() {
        if ($this->item) {
            $this->addAttribute('value', $this->item_value);
        } else if ($this->default_value) {
            $this->addAttribute('value', $this->default_value);
        }

        $this->addAttribute('type', 'text');
        $this->addAttribute('placeholder', '0.00');
        $this->addAttribute('onkeyup', 'Mask.float(this);');
        $this->addAttribute('onchange', 'Mask.float(this);');

        $this->view = 'components.inputs.text';
    }

    private function boolean() {
        if ($this->item) {
            $this->selected_value = (string)$this->item_value;
        } else if (!is_null($this->default_value)) {
            $this->selected_value = (string)$this->default_value;
        } else {
            $this->selected_value = null;
        }

        $this->view = 'components.inputs.boolean';
    }

    private function file_table($source, $scope) {
        if(isset($this->item->documents)) {
            $this->list = $this->item->documents;
        }

        $this->view = 'components.inputs.file_table';
    }

    public function render()
    {
        return view($this->view);
    }

    public function renderToString()
    {
        return view($this->view, $this->data())->render();
    }

}
