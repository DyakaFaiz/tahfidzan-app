<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Select extends Component
{
    public $name;
    public $id;
    public $options;
    public $selected;
    public $style;
    public $statusSantri;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($name, $id, $options = [], $selected = null, $style='', $status=1)
    {
        $this->name = $name;
        $this->id = $id;
        $this->options = $options;
        $this->selected = $selected;
        $this->style = $style;
        $this->statusSantri = $status;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.select');
    }
}