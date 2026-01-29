<?php

namespace App\View\Components\partials;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Izin;
use App\Models\Cuti;

class sidebar extends Component
{
    public $pendingIzinCount;
    public $pendingCutiCount;
    public $totalPendingCount;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // Hitung jumlah izin dengan status pending
        $this->pendingIzinCount = Izin::where('status', 'pending')->count();
        
        // Hitung jumlah cuti dengan status pending
        $this->pendingCutiCount = Cuti::where('status', 'pending')->count();
        
        // Total pengajuan pending
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
