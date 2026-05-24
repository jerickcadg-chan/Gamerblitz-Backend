<?php

namespace App\Exports;

use App\Constants\ColumnFormat;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class UserExport implements FromView, ShouldAutoSize, WithEvents, WithColumnFormatting
{
    use Exportable;

    public function __construct($users)
    {
        $this->users = $users;
    }

    public function view(): View
    {
        return view('xls.user', [
            'users' => $this->users
        ]);
    }

    public function columnFormats(): array
    {
        return [

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

                $end = count($this->users) + 1;

                $event->sheet->getDelegate()->getStyle('A1:E'.$end)->applyFromArray($border);
                $event->sheet->getDelegate()->getStyle('A1:E1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('FFFF00');
            },
        ];
    }
}
