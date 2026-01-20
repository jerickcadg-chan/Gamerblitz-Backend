<?php

namespace App\Exports;

use App\Constants\ColumnFormat;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class OrderExport implements FromView, ShouldAutoSize, WithEvents, WithColumnFormatting
{
    use Exportable;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function view(): View
    {
        return view('xls.order', [
            'orders' => $this->orders
        ]);
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $border = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ]
                ];

                $bgYellow = [
                    'font' => [
                        'bold' => true,
                    ],
                    'background' => [
                        'color'=> '#AAAAFF'
                    ]
                ];

                $end = count($this->orders) + 2;

                $event->sheet->getDelegate()->getStyle('A1:P'.$end)->applyFromArray($border);
                $event->sheet->getDelegate()->getStyle('A1:P2')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('FFFF00');
            },
        ];
    }
}
