<?php

namespace App\Exports;

use App\Models\Project;
use App\Models\StandUp;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProjectExport implements FromCollection, WithTitle, WithCustomStartCell, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    private int $iteration = 1;

    // @phpstan-ignore-next-line
    public function collection()
    {
        return StandUp::select('project_id', 'user_id', DB::raw('MAX(id) as id'))
        ->orderBy('project_id')
        ->orderBy('user_id')
        ->groupBy('project_id', 'user_id')
        ->get();

    }

    public function title(): string
    {
        return "Data Project";
    }

    public function startCell(): string
    {
        return 'B2';
    }

    /**
    * @return array<array<string>>
     */
    public function headings(): array
    {
        $dateNow = Carbon::now()->format('j F Y');

        return [
            ["Rekap Project"],
            ["$dateNow"],
            [
                'No',
                'Nama Project',
                'Kontributor',
                'Divisi',
                'Posisi',
                'L / P',
                'Nama Partner',
                'Tanggal Mulai & Akhir',
                'Status Project',
            ]
        ];
    }

    /**
     * @param mixed $standup
     *
     * @return array<array<string>>
     */
    public function map($standup) : array
    {
        $startDate = Carbon::createFromFormat('Y-m-d', $standup->project->start_date);
        $endDate = Carbon::createFromFormat('Y-m-d', $standup->project->end_date);
        $today = Carbon::now();

        // @phpstan-ignore-next-line
        $formattedStartDate = $startDate->format('j F Y');
        // @phpstan-ignore-next-line
        $formattedEndDate = $endDate->format('j F Y');
        $formattedDate = $formattedStartDate . ' - ' . $formattedEndDate;
        $status = $today->gt($endDate) ? 'NON-AKTIF' : 'AKTIF';

        return [
            $this->iteration++,
            $standup->project->name,
            $standup->user->name,
            $standup->user->employee->division->name,
            $standup->user->employee->position->name,
            $standup->user->employee->gender == 'male' ? 'L' : 'P',
            $standup->project->partner->name,
            $formattedDate,
            $status

        ];
    }

    /**
     * @return array<string, int>
     */
    public function columnWidths(): array
    {
        return [
            'C' => 25,
            'H' => 25,
            'I' => 25,
            'J' => 25
        ];
    }

    /**
     * Merge cells in a column.
     *
     * @param Worksheet $sheet
     * @param string $column
     * @param int $lastRow
     * @param bool $bold
     * @return void
     */
    private function mergeCellsByColumn(Worksheet $sheet, $column, $lastRow, $bold): void
    {
        $lastValue = null;
        $mergeStartRow = 0;

        for ($row = 5; $row <= $lastRow; $row++) {
            $currentValue = $sheet->getCell($column . $row)->getValue();

            if ($currentValue !== $lastValue) {
                if ($mergeStartRow !== 0) {
                    $sheet->mergeCells("{$column}{$mergeStartRow}:{$column}" . ($row - 1));
                    if ($bold) {
                        $sheet->getStyle("{$column}{$mergeStartRow}")->applyFromArray([
                            'font' => [
                                'bold' => true,
                            ],
                        ]);
                    }
                }

                $mergeStartRow = $row;
            }

            $lastValue = $currentValue;

            if ($row === $lastRow && $mergeStartRow !== 0) {
                $sheet->mergeCells("{$column}{$mergeStartRow}:{$column}{$row}");
                if ($bold) {
                    $sheet->getStyle("{$column}{$mergeStartRow}")->applyFromArray([
                        'font' => [
                            'bold' => true,
                        ],
                    ]);
                }
            }
        }
    }

    public function styles(Worksheet $sheet): void
    {
        $fillSolid = Fill::FILL_SOLID;
        $borderMedium = Border::BORDER_MEDIUM;
        $borderThin = Border::BORDER_THIN;
        $lightBlue = ['rgb' => 'C2D9FF'];
        $black = ['rgb' => '000000'];

        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();
        // Set row height headings
        $rowHeights = [2 => 28, 3 => 28, 4 => 24];
        foreach ($rowHeights as $row => $height) {
            $sheet->getRowDimension($row)->setRowHeight($height);
        }
        // Set row height data
        for ($row = 5; $row <= $lastRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(24);
        }
        // Set Autosize width
        for ($column = 'D'; $column <= 'G'; $column++) {
            // @phpstan-ignore-next-line
            $sheet->getColumnDimension($column)->setAutoSize(2);
        }
        // Set all column center
        $sheet->getStyle('B2:' . $lastColumn . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);
        // Header
        $sheet->mergeCells('B2:' . $lastColumn . '2');
        $sheet->getStyle('B2:' . $lastColumn . '2')->applyFromArray([
            'font' => ['name' => 'Calibri', 'size' => 18, 'bold' => true],
            'fill' => [
                'fillType' => $fillSolid,
                'color' => $lightBlue,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => $borderMedium,
                    'color' => $black,
                ],
            ],
        ]);
        // Set all border in column data
        $sheet->getStyle('B5:' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => $borderThin,
                    'color' => $black,
                ],
            ],
        ]);
        // Date
        $sheet->mergeCells('B3:' . $lastColumn . '3');
        $sheet->getStyle('B3:' . $lastColumn . '3')->applyFromArray([
            'font' => ['name' => 'Calibri', 'size' => 14, 'bold' => true],
            'fill' => [
                'fillType' => $fillSolid,
                'color' => $lightBlue,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => $borderMedium,
                    'color' => $black,
                ],
            ],
        ]);
        // Headings
        $sheet->getStyle('B4:' . $lastColumn . '4')->applyFromArray([
            'font' => ['name' => 'Calibri', 'size' => 12, 'bold' => true],
            'fill' => [
                'fillType' => $fillSolid,
                'color' => $lightBlue,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => $borderMedium,
                    'color' => $black,
                ],
            ],
        ]);

        // Merge Cell Project
        $this->mergeCellsByColumn($sheet, 'C', $lastRow, true);
        // Merge Cell Partner
        $this->mergeCellsByColumn($sheet, 'H', $lastRow, true);
        // Merge Cell Date
        $this->mergeCellsByColumn($sheet, 'I', $lastRow, true);
        // Merge Cell Status
        $this->mergeCellsByColumn($sheet, 'J', $lastRow, true);

        // Set Wrap Text
        $columns = ['C', 'H', 'I', 'J'];

        foreach ($columns as $column) {
            $sheet->getStyle($column . '5:' . $column . $lastRow)->applyFromArray([
                'alignment' => [
                    'wrapText' => true
                ],
            ]);
        }

        // footer
        $sheet->mergeCells('B' . ($lastRow + 1) . ':' . $lastColumn . ($lastRow + 1));
        $sheet->getStyle('B' . ($lastRow + 1) . ':' . $lastColumn . ($lastRow + 1))->applyFromArray([
            'fill' => [
                'fillType' => $fillSolid,
                'color' => $lightBlue,
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => $borderMedium,
                    'color' => $black,
                ],
            ],
        ]);
    }
}
