<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class PartnerSheet implements FromCollection, WithHeadings, WithColumnWidths, WithCustomStartCell, WithStyles
{
    protected $partners;

    public function __construct(Collection $partners)
    {
        $this->partners = $partners;
    }

    public function collection()
    {
        $collection = [];

        foreach ($this->partners as $key => $partner) {
            foreach ($partner->projects as $project) {
                $collection[] = [
                    'No' => $key + 1,
                    'Nama Partner' => $partner->name,
                    'Deskripsi' => $partner->description,
                    'Project' => $project->name,
                    'Total' => $partner->total,
                ];
            }
        }

        return collect($collection);
    }


    public function startCell(): string
    {
        return 'B2';
    }

    public function headings(): array
    {
        Carbon::setLocale('id');

        $formattedDate = strtoupper(Carbon::now()->isoFormat('DD MMMM YYYY'));

        return [
            ['REKAP PARTNER'],
            [$formattedDate],
            [
                'No',
                'Nama Partner',
                'Deskripsi',
                'Project',
                'Total',
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'B' => 10,
            'C' => 20,
            'D' => 55,
            'E' => 55,
            'F' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $this->mergePartnerCells($sheet);
        $this->mergeDescriptionCells($sheet);

        $sheet->getStyle('B4:F' . $highestRow)->getAlignment()->setWrapText(true);

        $sheet->mergeCells('B2:F2');
        $sheet->mergeCells('B3:F3');

        $sheet->getStyle('B2:F' . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('B2:F2')->applyFromArray([
            'font' => [
                'size' => 16,
                'bold' => true,
            ],
        ]);

        $sheet->getStyle('B3:F3')->applyFromArray([
            'font' => [
                'size' => 12,
                'bold' => true,
            ],
        ]);

        $sheet->getStyle('B2:F4')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'c2d9ff'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('B5:F' . $highestRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                // 'wrapText' => true, // Wrap text for data cells
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
    }

    public function mergePartnerCells(Worksheet $sheet)
    {
        $iterationColumn = 'B';
        $partnerNameColumn = 'C';
        $totalColumn = 'F';
        $rowCounter = 5;
    
        foreach ($this->partners as $partner) {
            $startRow = $rowCounter;
            $endRow = $startRow + count($partner->projects) - 1;
    
            if ($endRow > $startRow) {
                $sheet->mergeCells($iterationColumn . $startRow . ':' . $iterationColumn . $endRow);
                $sheet->mergeCells($partnerNameColumn . $startRow . ':' . $partnerNameColumn . $endRow);
                $sheet->mergeCells($totalColumn . $startRow . ':' . $totalColumn . $endRow);
            }
    
            $rowCounter = $endRow + 1;
        }
    }

    public function mergeDescriptionCells(Worksheet $sheet)
    {
        $descriptionColumn = 'D';
        $rowCounter = 5;
    
        foreach ($this->partners as $partner) {
            $startRow = $rowCounter;
            $endRow = $startRow + count($partner->projects) - 1;
    
            for ($i = $startRow; $i <= $endRow; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(-1); // Auto height
            }
    
            if ($endRow > $startRow) {
                $sheet->mergeCells($descriptionColumn . $startRow . ':' . $descriptionColumn . $endRow);
                $sheet->getStyle($descriptionColumn . $startRow)->getAlignment()->setWrapText(true);
            }
    
            $rowCounter = $endRow + 1;
        }
    }
    
}