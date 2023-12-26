<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Presence;
use App\Models\Telework;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use App\Models\StatusCommit;
use App\Models\WorkTrip;

class EmployeeSheet implements FromArray, WithHeadings, WithStyles, WithCustomStartCell, WithColumnWidths, WithTitle
{
    protected $employees;

    public function __construct(Collection $employees)
    {
        $this->employees = $employees;
    }

    public function array(): array
    {
        $data = [];
        foreach ($this->employees as $key => $employee) {
            $user = $employee->user;
            $standups = $employee->user->standups; 
    
            $todayStandups = $standups->filter(function ($standup) {
                return \Carbon\Carbon::parse($standup->created_at)->isToday();
            });

            $doneCount = $todayStandups->where('done', true)->count();
            $doingCount = $todayStandups->where('doing', true)->count();
            $blockerCount = $todayStandups->where('blocker', true)->count();

            $todayPresence = Presence::where('user_id', $user->id)
            ->whereDate('date', now()->toDateString())
            ->first();
            
            $wfoPresenceCount = Presence::where('category', 'WFO')
            ->where('user_id', $user->id)
            ->whereDate('date', now()->toDateString())
            ->count();

            $workTripPresenceCount = Presence::where('category', 'work_trip')
            ->where('user_id', $user->id)
            ->whereDate('date', now()->toDateString())
            ->count();

            $teleworkPresenceCount = Telework::whereHas('presence', function ($query) use ($user) {
                $query->where('category', 'telework')
                    ->where('user_id', $user->id)
                    ->whereDate('date', now()->toDateString());
            })
            ->where('telework_category', '!=', 'kesehatan')
            ->count();

            $hadirStatus = ($workTripPresenceCount > 0 || $wfoPresenceCount > 0 || $teleworkPresenceCount > 0) ? '✓' : 0;

            $sick = Telework::whereHas('presence', function ($query) use ($user) {
                $query->where('category', 'telework')
                    ->where('user_id', $user->id)
                    ->whereDate('date', now()->toDateString());
            })
            ->where('telework_category', 'kesehatan')
            ->count();

            $sakitStatus = ($sick > 0) ? '✓' : 0;

            $skipPresenceCount = Presence::where('category', 'skip')
            ->where('user_id', $user->id)
            ->whereDate('date', now()->toDateString())
            ->count();
            
            $birthDate = \Carbon\Carbon::parse($employee->birth_date);
            $monthName = $this->getMonthNameIndonesia($birthDate->month);

            $presenceStatus = '';
            if ($todayPresence || $sick > 0) {

                $category = $todayPresence->category ?? '';

                switch ($category) {
                    case 'WFO':
                        $presenceStatus = 'Work From Office';
                        break;
                    case 'work_trip':
                        $workTripStatus = StatusCommit::where('statusable_type', WorkTrip::class)
                            ->where('statusable_id', optional($todayPresence->worktrip)->id)
                            ->where('status', 'pending')
                            ->first();
                        
                        $canApproveWorkTrip = $workTripStatus &&
                            $workTripStatus->approver_id === auth()->user()->id;
                        
                        if ($workTripStatus && !$canApproveWorkTrip) {
                            $hadirStatus = 0;
                            $presenceStatus = 'Perjalanan Dinas (pending)';
                        } else {
                            $presenceStatus = 'Perjalanan Dinas';
                        }
                        break;                        
                    case 'skip':
                        $presenceStatus = 'Bolos';
                        break;
                    case 'telework':
                        $teleworkStatus = StatusCommit::where('statusable_type', Telework::class)
                            ->where('statusable_id', optional($todayPresence->telework)->id)
                            ->where('status', 'pending')
                            ->first();
                        
                        $canApproveTelework = $teleworkStatus &&
                            $teleworkStatus->approver_id === auth()->user()->id;
                        
                        if ($teleworkStatus && !$canApproveTelework) {
                            $hadirStatus = 0;
                            $sakitStatus = 0;
                            $teleworkCategory = Telework::where('user_id', $user->id)
                                ->whereDate('created_at', now()->toDateString())
                                ->where('telework_category', 'kesehatan')
                                ->count();
                                
                            $presenceStatus = $teleworkCategory > 0 ? 'Sakit (pending)' : 'Work From Anywhere (pending)';
                        } else {
                            $teleworkCategory = Telework::where('user_id', $user->id)
                                ->whereDate('created_at', now()->toDateString())
                                ->where('telework_category', 'kesehatan')
                                ->count();
                        
                            $presenceStatus = $teleworkCategory > 0 ? 'Sakit' : 'Work From Anywhere';
                        }
                        break;
                    default:
                        break;
                        
                }
            }
    
            $data[] = [
                'No' => $key + 1,
                'Nama Lengkap' => $user->name,
                'Divisi' => $employee->division->name,
                'Posisi' => $employee->position->name,
                'Tanggal Lahir' => $birthDate->format('d') . ' ' . $monthName . ' ' . $birthDate->format('Y'),
                'Role' => $this->determineRole($employee),
                'L / P' => ($employee->gender == 'male') ? 'L' : 'P',
                'Hadir' => $hadirStatus ? '✓' : 0,
                'Sakit' => $sakitStatus ? '✓' : 0,
                'Bolos' => $skipPresenceCount > 0 ? '✓' : 0,
                'Done' => $doneCount > 0 ? '✓' : 0, 
                'Doing' => $doingCount > 0 ? '✓' : 0,
                'Blocker' => $blockerCount > 0 ? '✓' : 0,
                'Status' => $presenceStatus
            ];
        }

        return $data;
    }

    public function startCell(): string
    {
        return 'B2';
    }

    public function headings(): array
    {
        $monthNamesMapping = [
            'January' => 'JANUARI',
            'February' => 'FEBRUARI',
            'March' => 'MARET',
            'April' => 'APRIL',
            'May' => 'MEI',
            'June' => 'JUNI',
            'July' => 'JULI',
            'August' => 'AGUSTUS',
            'September' => 'SEPTEMBER',
            'October' => 'OKTOBER',
            'November' => 'NOVEMBER',
            'December' => 'DESEMBER',
        ];

        $formattedDate = strtoupper(now()->format('d F Y'));

        foreach ($monthNamesMapping as $englishMonth => $indonesianMonth) {
            $formattedDate = str_replace(' ' . strtoupper($englishMonth) . ' ', ' ' . $indonesianMonth . ' ', $formattedDate);
        }
        return [
            ['REKAP PEGAWAI'],
            [$formattedDate],
            [
                'NO',
                'Nama Lengkap',
                'Divisi',
                'Posisi',
                'Tanggal Lahir',
                'Role',
                'L / P',
                'Hadir',
                'Sakit',
                'Bolos',
                'Done',
                'Doing',
                'Blocker',
                'Status',
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'B' => 8,
            'C' => 30,
            'D' => 30,
            'E' => 30,
            'F' => 30,
            'G' => 30,
            'H' => 8,
            'I' => 8,
            'J' => 8,
            'K' => 8,
            'L' => 8,
            'M' => 8,
            'N' => 8,
            'O' => 30,
        ];
    }

    public function styles(Worksheet $sheet)
    {

        $lastDataRow = $this->employees->count() + 4;

        $sheet->getStyle('B2:O2')->applyFromArray([
            'font' => [
                'size' => 16,
                'bold' => true,
            ],
        ]);

        $sheet->getStyle('B3:O3')->applyFromArray([
            'font' => [
                'size' => 12,
                'bold' => true,
            ],
        ]);

        $sheet->getStyle('B2:O4')->applyFromArray([
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

        // Apply the same color to the entire row 4 and set border
        $sheet->getStyle('B4:O4')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'c2d9ff'],
            ],
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Set the background color for the entire column 'H' (Hadir) until 'O' (Status)
        $sheet->getStyle('H2:O4')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'c2d9ff'],
            ],
        ]);

        $sheet->getStyle('B4:O4')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('B5:O' . ($this->employees->count() + 4))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('B5:O' . ($this->employees->count() + 4))->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('B5:O' . $lastDataRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Apply medium left and right border to column B for row 4
        $sheet->getStyle('B4:B' . ($this->employees->count() + 4))->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('I4:I' . ($this->employees->count() + 4))->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('L4:L' . ($this->employees->count() + 4))->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('N4:N' . ($this->employees->count() + 4))->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('O4:O' . ($this->employees->count() + 4))->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        
        $sheet->mergeCells('B' . ($lastDataRow + 1) . ':O' . ($lastDataRow + 1));
        $sheet->getStyle('B' . ($lastDataRow + 1) . ':O' . ($lastDataRow + 1))->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'c2d9ff'],
            ],
        ]);

    
        $sheet->getStyle('B' . ($lastDataRow + 1))->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('B5:B' . ($this->employees->count() + 4))->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
    
        $sheet->getStyle('O' . ($lastDataRow + 1))->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

    
        $sheet->getStyle('B' . ($lastDataRow + 1) . ':O' . ($lastDataRow + 1))->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->mergeCells('B2:O2');
        $sheet->mergeCells('B3:O3');
        $sheet->getStyle('B2:O3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B2:O3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Rekap Pegawai';
    }

    private function determineRole($employee)
    {
        $permissions = $employee->user->permissions->pluck('id')->toArray();

        if (in_array(38, $permissions) && in_array(42, $permissions)) {
            return 'Human Resource';
        } elseif (in_array(39, $permissions) && in_array(43, $permissions)) {
            return 'Head Of Tribe';
        } else {
            return 'Ordinary Employee';
        }
    }

    private function getMonthNameIndonesia($monthNumber)
    {
        $monthNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $monthNames[$monthNumber] ?? '';
    }
}
