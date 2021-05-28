<?php declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

/**
 * Class LookupSelectbox
 * @package App\View\Components
 */
class LookupSelectbox extends Component
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
     * @var Collection
     */
    public Collection $lookup;

    /**
     * Create a new component instance.
     *
     * @param string     $id
     * @param string     $label
     * @param Collection $lookup
     */
    public function __construct(string $id, string $label, Collection $lookup)
    {
        $this->id     = $id;
        $this->label  = $label;
        $this->lookup = $lookup;
    }

    /**
     * @return View|string
     */
    public function render()
    {
        return view('components.lookup-selectbox');
    }
}
