<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Enum extends Component
{
    public $id;
    public $label;
    /**
     * @var array
     */
    public $values;

    /**
     * Enum constructor.
     *
     * @param       $id
     * @param       $label
     * @param array $values
     */
    public function __construct($id, $label, array $values)
    {
        $this->id     = $id;
        $this->label  = $label;
        $this->values = $values;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.enum');
    }
}
