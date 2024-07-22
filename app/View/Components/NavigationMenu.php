<?php

namespace App\View\Components;

use Illuminate\View\Component;

class NavigationMenu extends Component
{
    public $totalMutasiPending;
    public $totalMutasiOnProcess;
    public function __construct($totalMutasiPending, $totalMutasiOnProcess)
    {
        $this->totalMutasiPending = $totalMutasiPending;
        $this->totalMutasiOnProcess = $totalMutasiOnProcess;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.navigation-menu');
    }
}
