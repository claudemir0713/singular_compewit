<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\{
    Exportable,
    FromView,
    WithTitle
};

class vendasExcel implements FromView
{
    use Exportable;
    private $vendas;
    private $dateForm;

    public function __construct($vendas,$dateForm)
    {
        $this->vendas = $vendas;
        $this->dateForm = $dateForm;
    }

    public function view(): View
    {
        $vendas          = $this->vendas;
        $dateForm       = $this->dateForm;
        return view('vendas.vendasExcel', compact('vendas','dateForm'));
    }
}
