<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Slug extends Component
{
    /**
     * @var string
     */
    public string $id;

    /**
     * @var string
     */
    public string  $label;

    /**
     * @var string
     */
    public string $foreignId;

    /**
     * @param string $id
     * @param string $label
     * @param string $foreignId
     */
    public function __construct(string $id, string $label, string $foreignId)
    {
        $this->id        = $id;
        $this->label     = $label;
        $this->foreignId = $foreignId;
    }

    /**
     * @return View|string
     */
    public function render()
    {
        return view('components.slug');
    }
}
