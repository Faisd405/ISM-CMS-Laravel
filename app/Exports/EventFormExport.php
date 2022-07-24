<?php

namespace App\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class EventFormExport implements 
    FromView,
    WithEvents,
    ShouldQueue
{

    use Exportable;

    protected $event, $field, $data;

    public function __construct($event, $field, $data)
    {
        $this->event = $event;
        $this->field = $field;
        $this->data = $data;
    }

    public function view(): View
    {
        $event = $this->event;

        return view('backend.events.form.export', [
            'event' => $event,
            'fields' => $this->field,
            'data' => $this->data
        ]);
    }

    /**
     * menambahkan style untuk excel
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $styleHeader = [
                    'font' => [
                        'bold' => true,
                        'size' => 12
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ];
                $styleContent = [
                    'borders' => [
                        'font' => [
                            'size' => 12
                        ],
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ];
                $styleTotalSistem = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ],
                ];
            },
        ];
    }
}
