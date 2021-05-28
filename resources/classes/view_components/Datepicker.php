<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Datepicker extends Component
{
    /**
     * @var string
     */
    public string $id;

    /**
     * @var string
     */
    public string $label;

    /**
     * @param string $id
     * @param string $label
     */
    public function __construct(string $id, string $label)
    {
        $this->id    = $id;
        $this->label = $label;
    }

    /**
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.datepicker');
    }
}
