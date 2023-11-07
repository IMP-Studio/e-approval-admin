<?php

namespace App\Exports;

use App\Models\StandUp;
use App\Models\Presence;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class PresenceRekapYearSheet implements WithTitle, WithHeadings,  WithStyles, WithCustomStartCell, WithColumnWidths, FromCollection
{
    private $iteration = 0;
    protected $startDate;
    protected $endDate;
    protected $userAbsenceSummaries = [];
    protected $absenceSummary;
    protected $absenceData; 

    public function __construct($absenceData, $monthName, $startDate, $endDate)
    {
        $this->absenceData = $absenceData;
        $this->monthName = $monthName;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->absenceSummary = $this->getAbsenceSummary();
    }

    public function startCell(): string
    {
        return 'B2';
    }

  public function title(): string
    {
        return $this->monthName . ' - Rekap'; 
    }



    public function headings(): array
    {
        return [
            ["Data Presence", '', '', '', '', '', '', '', "Standup", '', '', '', '', "Total Absen"],
            [
                'No',
                'Username',
                'Position',
                'Date',
                'Temporary Entry Time',
                'Entry Time',
                'Exit Time',
                'Category',
                'Done',
                'Doing',
                'Blocker',
                '',
                '',
                'ID',
                'User Name',
                'WFO',
                'Work trip',
                'telework',
                'leave',
                'skip',
                'Total',
            ]
        ];
    }

    protected function getAbsences()
    {
        $wfo = Presence::whereBetween('date', [$this->startDate, $this->endDate])
        ->where('category', 'WFO')
        ->orderBy('date', 'asc')
        ->get();

        $telework = Presence::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('category', 'telework')
            ->whereHas('telework.statusCommit', function ($query) {
                $query->where('status', 'allowed');
            })
            ->orderBy('date', 'asc')
            ->get();

        $work_trip = Presence::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('category', 'work_trip')
            ->whereHas('worktrip.statusCommit', function ($query) {
                $query->where('status', 'allowed');
            })
            ->orderBy('date', 'asc')
            ->get();

        $skip = Presence::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('category', 'skip')
            ->orderBy('date', 'asc')
            ->get();

        $leave = Presence::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('category', 'leave')   
            ->whereHas('leave.statusCommit', function ($query) {
                $query->where('status', 'allowed');
            }) 
            ->orderBy('date', 'asc')
            ->get();

        $allPresences = collect($wfo)->concat($telework)->concat($work_trip)->concat($skip)->concat($leave);

    
        return $allPresences->map(function ($presence, $index) {
            if ($presence->category === 'leave') {
                $standup = null;
            } else {
                $standup = StandUp::where('user_id', $presence->user_id)->first();
            }
    
            $done = '';
            $doing = '';
            $blocker = '';
    
            if ($standup !== null) {
                $done = $standup->done;
                $doing = $standup->doing;
                $blocker = $standup->blocker;
            }
    
            return [
                $index + 1,
                $presence->user->name,
                $presence->user->employee->position->name,
                $presence->date,
                $presence->temporary_entry_time,
                $presence->entry_time,
                $presence->exit_time,
                ucwords(strtolower($presence->category === 'work_trip' ? 'Work Trip' : $presence->category)),
                $done,
                $doing,
                $blocker,
                '',
                '',
            ];
        })->all();
    }
    

    protected function getAbsenceSummary()
    {
        $desiredStructure = [];

        $wfo = Presence::whereBetween('date', [$this->startDate, $this->endDate])
        ->where('category', 'WFO')
        ->orderBy('date', 'asc')
        ->get();

        $telework = Presence::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('category', 'telework')
            ->whereHas('telework.statusCommit', function ($query) {
                $query->where('status', 'allowed');
            })
            ->orderBy('date', 'asc')
            ->get();

        $work_trip = Presence::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('category', 'work_trip')
            ->whereHas('worktrip.statusCommit', function ($query) {
                $query->where('status', 'allowed');
            })
            ->orderBy('date', 'asc')
            ->get();

        $skip = Presence::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('category', 'skip')
            ->orderBy('date', 'asc')
            ->get();

        $leave = Presence::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('category', 'leave')   
            ->whereHas('leave.statusCommit', function ($query) {
                $query->where('status', 'allowed');
            }) 
            ->orderBy('date', 'asc')
            ->get();

        $allPresences = collect($wfo)->concat($telework)->concat($work_trip)->concat($skip)->concat($leave);

        foreach ($allPresences as $absence) {
            $userId = $absence->user_id;
            $name = $absence->user->name;
            $category = $absence->category;

            if (!isset($desiredStructure[$userId])) {
                $desiredStructure[$userId] = [
                    'user_id' => $userId,
                    'name' => $name,
                    'WFO' => 0,
                    'work_trip' => 0,
                    'telework' => 0,
                    'leave' => 0,
                    'skip' => 0,
                    'total_excluding_skip' => 0,
                ];
            }
            
            $desiredStructure[$userId][$category] += 1;
            
            $totalExcludingSkip = $desiredStructure[$userId]['WFO'] +
            $desiredStructure[$userId]['work_trip'] +
            $desiredStructure[$userId]['telework'] +
            $desiredStructure[$userId]['leave'];
            $desiredStructure[$userId]['total_excluding_skip'] = $totalExcludingSkip;
        }

        $result = collect($desiredStructure)->groupBy('user_id')->values()->toArray();
       

        return $result;
    }

    public function collection()
    {
        
        $absences = $this->getAbsences();
    
        $absenceSummary = $this->getAbsenceSummary();
    
        $combinedData = [];
        foreach ($absences as $userId => $absenceData) {
            $summaryData = $absenceSummary[$userId][0] ?? [];
            $combinedData[] = array_merge($absenceData, $summaryData);
        }
    
        return collect($combinedData);
    }
    

    public function columnWidths(): array
    {
        return [
            'B' => 8,
            'C' => 26,
            'D' => 28,
            'E' => 14,
            'F' => 24,
            'G' => 15,
            'H' => 15,
            'I' => 16,
            'J' => 22,
            'K' => 22,
            'L' => 22,
            'M' => 8,
            'O' => 8,
            'P' => 28,
            'Q' => 12,
            'R' => 14,
            'S' => 14,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();
        $absenceSummary = collect($this->getAbsenceSummary());
        $lastRowSum = $absenceSummary->count() + 3;
        

        foreach ($this->absenceSummary as $rowIndex => $data) {
            if (isset($data[0]['total_excluding_skip'])) {
                $totalExcludingSkip = $data[0]['total_excluding_skip'];
                if ($totalExcludingSkip < 2) {
                    $sheet->getStyle('V' . ($rowIndex + 4))
                        ->applyFromArray([
                            'font' => [
                                'size' => 8, 
                                'bold' => true,
                                'color' => ['rgb' => 'FFFFFF'],
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FF0000'], 
                            ],
                        ]);
                }else{
                     $sheet->getStyle('V' . ($rowIndex + 4))
                     ->applyFromArray([
                         'font' => [
                             'size' => 8, 
                             'bold' => true,
                         ],
                         'fill' => [
                             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                             'startColor' => ['rgb' => '92D050'], 
                         ],
                     ]);
                }
            }
        }
        

        for ($col = 'C'; $col <= $lastColumn; $col++) {
            $sheet->getStyle($col)->getAlignment()->setVertical('center');
            $sheet->getStyle($col)->getAlignment()->setIndent(1);
        }
        return [
            2 => [
                'font' => ['size' => 13, 'bold' => true],
            ],
            3 => [
                'font' => ['size' => 12, 'bold' => true],
            ],
            'B3:B' . $lastRow => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'C2D9FF'], 
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                    'horizontal' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
            'C3:L3'=> [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'C2D9FF'], 
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                    'horizontal' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'], 
                    ],
                    'vertical' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'], 
                    ],
                ],                
            ],
            'O4:O' . $lastRowSum => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'C2D9FF'], 
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                    'horizontal' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
            'O3:V3'=> [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'C2D9FF'], 
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                    'horizontal' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'], 
                    ],
                    'vertical' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'], 
                    ],
                ],                
            ],
            'F4:F' . $lastRow => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
            'Q3:Q' . $lastRow => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
            'R3:R' . $lastRow => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
            'S3:S' . $lastRow => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
            'T3:T' . $lastRow => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
            'V3:V' . $lastRow => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
            'J' => [
                'alignment' => ['wrapText' => true],
            ],
            'J' => [
                'alignment' => ['wrapText' => true],
            ],
            'K' => [
                'alignment' => ['wrapText' => true],
            ],
        ];
    }
}
