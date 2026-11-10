<?php

namespace App\Exports;

use App\Models\PatrolLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PatrolHistoryExport implements FromQuery, WithEvents, WithHeadings, WithMapping, WithStyles
{
    private int $rowNumber = 0;

    public function __construct(private readonly Builder $query) {}

    public function query(): Builder
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'No',
            'Waktu Scan',
            'Petugas',
            'Checkpoint',
            'Rute',
            'Area',
            'Shift',
            'Status',
            'Catatan',
            'Link Foto',
        ];
    }

    public function map($log): array
    {
        /** @var PatrolLog $log */
        $this->rowNumber++;

        $photoPaths = $log->photos->pluck('photo_path')->filter()->all();

        if ($photoPaths === [] && $log->photo_path) {
            $photoPaths = [$log->photo_path];
        }

        $photoLinks = implode("\n", array_map(
            fn (string $path) => Storage::disk('public')->url($path),
            $photoPaths
        ));

        return [
            $this->rowNumber,
            $log->scanned_at?->format('d-m-Y H:i'),
            $this->safe($log->user?->name ?? '-'),
            $this->safe($log->checkpoint?->checkpoint_name ?? '-'),
            $this->safe($log->patrolSchedule?->schedule_name ?? '-'),
            $this->safe($log->patrolSchedule?->area?->area_name ?? '-'),
            $this->safe($log->patrolSchedule?->shiftMaster?->shift_name ?? '-'),
            $log->status === 'late' ? 'Terlambat' : 'Tepat Waktu',
            $this->safe($log->notes ?? ''),
            $photoLinks,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A5F'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = max($sheet->getHighestRow(), 2);

                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:J'.$highestRow);
                $sheet->getRowDimension(1)->setRowHeight(24);

                $widths = [
                    'A' => 6,
                    'B' => 20,
                    'C' => 24,
                    'D' => 28,
                    'E' => 28,
                    'F' => 24,
                    'G' => 16,
                    'H' => 16,
                    'I' => 45,
                    'J' => 55,
                ];

                foreach ($widths as $column => $width) {
                    $sheet->getColumnDimension($column)->setWidth($width);
                }

                $sheet->getStyle('A1:J'.$highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFD1D5DB'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_TOP,
                    ],
                ]);

                $sheet->getStyle('A2:A'.$highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H2:H'.$highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I2:I'.$highestRow)->getAlignment()->setWrapText(true);
                $sheet->getStyle('J2:J'.$highestRow)->getAlignment()->setWrapText(true);

                for ($row = 2; $row <= $highestRow; $row++) {
                    $url = $sheet->getCell('J'.$row)->getValue();

                    if (is_string($url) && $url !== '' && ! str_contains($url, "\n")) {
                        $sheet->getCell('J'.$row)->getHyperlink()->setUrl($url);
                        $sheet->getStyle('J'.$row)->getFont()
                            ->setColor(new Color('FF0563C1'))
                            ->setUnderline(true);
                    }
                }
            },
        ];
    }

    /**
     * Cegah formula injection saat nilai dibuka di Excel.
     */
    private function safe(?string $value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@\t\r]/', $value) === 1
            ? "'".$value
            : $value;
    }
}
