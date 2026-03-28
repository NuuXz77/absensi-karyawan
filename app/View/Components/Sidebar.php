<?php

namespace App\View\Components;

use App\Models\Cuti;
use App\Models\Izin;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Sidebar extends Component
{
    public int $pendingIzinCount;
    public int $pendingCutiCount;
    public int $totalPendingCount;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->pendingIzinCount = Izin::where('status', 'pending')->count();
        $this->pendingCutiCount = Cuti::where('status', 'pending')->count();
        $this->totalPendingCount = $this->pendingIzinCount + $this->pendingCutiCount;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.partials.sidebar');
    }
}
