<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Select2ModalForm extends Component
{
    private $controller;
    public $name;
    public $source_route;

    public function __construct($controller, $name, $sourceroute)
    {
        $this->controller = $controller;
        $this->name = $name;
        $this->source_route = $sourceroute;
    }

    public function render()
    {
        $controller = new $this->controller;
        $controller->init();

        $form = view('crud._form', [
            'route' => $controller->getRoute(),
            'fields' => $controller->getCreateFields(),
            'type' => 'create',
            'form_buttons' => [],
            'item' => null,
        ]);

        return view('partials.modal', [
            'title' => $controller->getTitle(),
            'form' => $form,
        ]);
    }
}
