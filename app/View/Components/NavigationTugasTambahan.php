<?php

namespace App\View\Components;

use Illuminate\View\Component;

class NavigationTugasTambahan extends Component
{
    public $totalTugasTambahanPending;
    public $totalTugasTambahanOnProcess;
    public function __construct($totalTugasTambahanPending, $totalTugasTambahanOnProcess)
    {
        $this->totalTugasTambahanPending = $totalTugasTambahanPending;
        $this->totalTugasTambahanOnProcess = $totalTugasTambahanOnProcess;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.navigation-tugas-tambahan');
    }
}
