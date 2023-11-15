<?php

namespace App\Exports;

use GuzzleHttp\Client;
use App\Models\StandUp;
use App\Models\Presence;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class PresenceResumYearSheet implements WithTitle, WithHeadings,  WithStyles, WithCustomStartCell, WithColumnWidths, FromCollection
{
    private $iteration = 0;
    protected $absenceData;
    protected $monthName;
    protected $startDate;
    protected $endDate;
    protected $dateHeaders; 

    public function __construct($absenceData, $monthName, $startDate, $endDate, $year)
    {
        $this->absenceData = $absenceData;
        $this->monthName = $monthName;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->year = $year;
    }
    
    

    public function startCell(): string
    {
        return 'B2';
    }

    public function title(): string
    {
        return $this->monthName . ' - Resume'; // Set the sheet title including the month
    }



    public function headings(): array
    {
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);
    
        // Structure your data for all days in the month with null values for days with no presence
        $daysInMonth = [];
        $currentDate = $startDate->copy()->startOfMonth();
    
        while ($currentDate <= $endDate) {
            $day = $currentDate->format('d');
            $daysInMonth[$day] = null; // Initialize with null for days with no presence
            $currentDate->addDay();
        }
    
        // Populate $daysInMonth with presence data
        foreach ($this->absenceData as $absence) {
            $date = Carbon::parse($absence->date);
            $day = $date->format('d');
            $daysInMonth[$day] = $absence->presenceValue; // Set presence data, replace presenceValue with your actual data
        }
    
        // Continue with the rest of your headings logic
        $dateHeaders = [];
        $currentDate = $startDate->copy();
    
        while ($currentDate <= $endDate) {
            $dateHeaders[] = $currentDate->format('d');
            $currentDate->addDay();
        }
    
        $mainHeaders = [
            'No',
            'Nama Lengkap',
            'Position',
            'L/P',
        ];
    
        $startDatehead = $startDate->format('d-m-Y');
        $endDatehead = $endDate->format('d-m-Y');
    
        $secondHead = $startDatehead . ' - ' . $endDatehead;
    
        $datetitle = [$secondHead];
        $combinedHeaders = array_merge($mainHeaders, $dateHeaders);
    
        $additionalHeaders = [
            'WFO',
            'Work trip',
            'Telework',
            'Leave',
            'Sick',
            'Skip',
            'Total',
        ];
    
        $rekaptotalcategory = [
            '',
            '',
            'No',
            'Jenis absensi',
            'Penanda',
            'Total absensi',
        ];
    
        $combinedHeaderss = array_merge($combinedHeaders, $additionalHeaders);
    
        $combinedHeadersWithCategory = array_merge($combinedHeaderss, $rekaptotalcategory);
    
        $this->dateHeaders = $dateHeaders;

        return [
            ["Resum Data"],
            $datetitle,
            $combinedHeadersWithCategory,
        ];
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
            ->whereHas('telework', function ($query) {
                $query->where('telework_category', '!=','kesehatan');
            })
            ->whereHas('telework.statusCommit', function ($query) {
                $query->where('status', 'allowed');
            })
            ->orderBy('date', 'asc')
            ->get();

        $sick = Presence::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('category', 'telework')
            ->whereHas('telework', function ($query) {
                $query->where('telework_category', 'kesehatan');
            })
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

        $allPresences = collect($wfo)->concat($telework)->concat($sick)->concat($work_trip)->concat($skip)->concat($leave);

        $dateHeaders = [];
        $currentDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);
        while ($currentDate->lte($endDate)) {
            $dateHeaders[] = $currentDate->format('d M Y');
            $currentDate->addDay();
        }        

        foreach ($allPresences as $absence) {
            $userId = $absence->user_id;
            $name = $absence->user->name;
            $category = $absence->category;
            $absenceDate = Carbon::parse($absence->date)->format('d M Y');
            $dateIndex = array_search($absenceDate, $dateHeaders); 
        
            if (!isset($desiredStructure[$userId])) {
                $gender = $absence->user->employee->gender === 'female' ? 'P' : 'L';
                $desiredStructure[$userId] = [
                    'user_id' => $userId,
                    'name' => $name,
                    'position' => $absence->user->employee->position->name,
                    'gender' => $gender,
                ];
                foreach ($dateHeaders as $headerDate) {
                    $desiredStructure[$userId]['date_' . $headerDate] = 0;
                }
            }

            if ($dateIndex !== false && $category == 'WFO') {
                $desiredStructure[$userId]['date_' . $absenceDate] = 'O';
            }elseif ($category == 'telework' && $absence->telework->telework_category === 'kesehatan') {
                $desiredStructure[$userId]['date_' . $absenceDate] = 'S';
            }elseif ($dateIndex !== false && $category == 'telework') {
                $desiredStructure[$userId]['date_' . $absenceDate] = 'T';
            }elseif ($dateIndex !== false && $category == 'work_trip') {
                $desiredStructure[$userId]['date_' . $absenceDate] = 'W';
            }elseif ($dateIndex !== false && $category == 'skip') {
                $desiredStructure[$userId]['date_' . $absenceDate] = 'B';
            }
            else{
                $desiredStructure[$userId]['date_' . $absenceDate] = 'C';
            }

        }
        
        $result = collect($desiredStructure)->groupBy('user_id')->values()->toArray();
       

        return $result;
    }
    
    protected function getAbsenceCategory()
    {
        $desiredStructure = [];

        $wfo = Presence::whereBetween('date', [$this->startDate, $this->endDate])
        ->where('category', 'WFO')
        ->orderBy('date', 'asc')
        ->get();

        $telework = Presence::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('category', 'telework')
            ->whereHas('telework', function ($query) {
                $query->where('telework_category', '!=','kesehatan');
            })
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

        $sick = Presence::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('category', 'telework')
            ->whereHas('telework', function ($query) {
                $query->where('telework_category', 'kesehatan');
            })
            ->whereHas('telework.statusCommit', function ($query) {
                $query->where('status', 'allowed');
            })
            ->orderBy('date', 'asc')
            ->get();
        

        $leave = Presence::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('category', 'leave')   
            ->whereHas('leave.statusCommit', function ($query) {
                $query->where('status', 'allowed');
            }) 
            ->orderBy('date', 'asc')
            ->get();

        $allPresences = collect($wfo)->concat($work_trip)->concat($telework)->concat($leave)->concat($sick)->concat($skip);

        foreach ($allPresences as $absence) {
            $userId = $absence->user_id;
            $category = $absence->category;

            if (!isset($desiredStructure[$userId])) {
                $gender = $absence->user->employee->position->name === 'female' ? 'P' : 'L';
                $desiredStructure[$userId] = [
                    'user_id' => $userId,
                    'WFO' => 0,
                    'work_trip' => 0,
                    'telework' => 0,
                    'leave' => 0,
                    'sick' => 0, 
                    'skip' => 0,
                    'total_excluding_skip' => 0,
                ];
            } 

            if ($category === 'telework' && $absence->telework->telework_category === 'kesehatan') {
                $desiredStructure[$userId]['sick'] += 1;
            }else{

                $desiredStructure[$userId][$category] += 1;
            }

            $totalExcludingSkip = $desiredStructure[$userId]['WFO'] +
            $desiredStructure[$userId]['work_trip'] +
            $desiredStructure[$userId]['telework'] +
            $desiredStructure[$userId]['sick'] +
            $desiredStructure[$userId]['leave'];
            $desiredStructure[$userId]['total_excluding_skip'] = $totalExcludingSkip;
        }


        $result = collect($desiredStructure)->groupBy('user_id')->values()->toArray();
       
        return $result;
    }

    protected function getTotalSemuaCategory()
    {
        $wfo = Presence::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('category', 'WFO')
            ->orderBy('date', 'asc')
            ->count();

        $telework = Presence::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('category', 'telework')
            ->whereHas('telework', function ($query) {
                $query->where('telework_category', '!=','kesehatan');
            })
            ->whereHas('telework.statusCommit', function ($query) {
                $query->where('status', 'allowed');
            })
            ->orderBy('date', 'asc')
            ->count();


        $work_trip = Presence::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('category', 'work_trip')
            ->whereHas('worktrip.statusCommit', function ($query) {
                $query->where('status', 'allowed');
            })
            ->orderBy('date', 'asc')
            ->count();

        $skip = Presence::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('category', 'skip')
            ->orderBy('date', 'asc')
            ->count();

        $sick = Presence::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('category', 'telework')
            ->whereHas('telework', function ($query) {
                $query->where('telework_category', 'kesehatan');
            })
            ->whereHas('telework.statusCommit', function ($query) {
                $query->where('status', 'allowed');
            })
            ->orderBy('date', 'asc')
            ->count();
        

        $leave = Presence::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('category', 'leave')   
            ->whereHas('leave.statusCommit', function ($query) {
                $query->where('status', 'allowed');
            }) 
            ->orderBy('date', 'asc')
            ->count();

            $totalCategoryData = [
                ['','',1, 'Work From Office', 'O', $wfo],
                ['','',2, 'Work From Anywhere', 'T', $telework],
                ['','',3, 'Perjalanan Dinas', 'W', $work_trip],
                ['','',4, 'Cuti', 'C', $leave],
                ['','',5, 'Sakit', 'S', $sick],
                ['','',6, 'Bolos', 'B', $skip],
            ];
        
            return $totalCategoryData;
    }

    public function collection()
{
    $absenceCategories = $this->getAbsenceCategory();
    $absenceSummaries = $this->getAbsenceSummary();
    $totalCategoryData = $this->getTotalSemuaCategory();

    $mergedData = [];
    
    $invisibleRowCount = 6;
    
    foreach ($absenceSummaries as $key => $summary) {
        $userId = $summary[0]['user_id'];
        
        $categoryData = collect($absenceCategories)->first(function ($item) use ($userId) {
            return $item[0]['user_id'] === $userId;
        });
        
        if (!empty($summary)) {
            $mergedData[] = array_merge($summary[0], $categoryData[0]);
        }
    }
    
    $userSummaryCount = count($absenceSummaries);
    $additionalInvisibleRowCount = max(0, $invisibleRowCount - $userSummaryCount);


    if($userSummaryCount > 0){
        for ($i = 0; $i < $additionalInvisibleRowCount; $i++) {
            $keysToExclude = ['user_id']; 
            
            // Buat baris tidak terlihat
            $invisibleRow = array_diff_key(
                array_fill_keys(array_keys(($absenceSummaries[0][0] ?? []) + ($categoryData[0] ?? [])), ''),
                array_flip($keysToExclude)
            );
            $invisibleRow['invisible'] = ''; 
            $mergedData[] = $invisibleRow;
        }
    }else {
        for ($i = 0; $i < $additionalInvisibleRowCount; $i++) {
            $dateHeadersCount = count($this->dateHeaders);
        
            $emptyStrings = array_fill(0, $dateHeadersCount, '');
        
            $invisibleRow = [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ...$emptyStrings, 
            ];
        
            $mergedData[] = $invisibleRow;
        }
        
    }
    
    foreach ($absenceSummaries as $key => $summary) {
        $userId = $summary[0]['user_id'];
        $categoryData = collect($absenceCategories)->first(function ($item) use ($userId) {
            return $item[0]['user_id'] === $userId;
        });
        
        $mergedData[] = array_fill_keys(array_keys(($summary[0] ?? []) + ($categoryData[0] ?? [])), '');
    }
    
    foreach ($totalCategoryData as $key => $totalData) {
        if (!isset($mergedData[$key])) {
            $mergedData[$key] = []; 
        }
        $mergedData[$key] = array_merge($mergedData[$key], $totalData);
    }
    
    return collect($mergedData);
}
    
    
    public function columnWidths(): array
    {
        $columnWidths = [
            'B' => 8,
            'C' => 26,
            'D' => 28,
            'E' => 5,
        ];
    
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);
        $currentDate = $startDate;
        $column = 'F'; 
    
        while ($currentDate->lte($endDate)) {
            $columnWidths[$column] = 3;
            $currentDate->addDay();
            $column++;
        }
    
        return $columnWidths;
    }
    
    private function fetchNationalDays($startYear, $endYear)
    {
        $holidaysWithWeekends = [];
    
        for ($year = $startYear; $year <= $endYear; $year++) {
            $apiUrl = "https://api-harilibur.vercel.app/api?year={$year}";
            $response = file_get_contents($apiUrl);
    
            if ($response) {
                $holidayData = json_decode($response, true);
    
                if ($holidayData) {
                    $holidays = $holidayData;
    
                    // Calculate the date range for the entire year
                    $startDate = Carbon::createFromDate($year, 1, 1);
                    $endDate = Carbon::createFromDate($year, 12, 31);
    
                    while ($startDate->lte($endDate)) {
                        if ($startDate->isWeekend()) {
                            $weekendDate = $startDate->toDateString();
                            $holidays[] = [
                                'holiday_name' => 'Weekend',
                                'holiday_date' => $weekendDate,
                                'is_national_holiday' => true, 
                            ];
                        }
    
                        $startDate->addDay();
                    }
    
                    $nationalHolidays = array_filter($holidays, function ($holiday) {
                        return isset($holiday['is_national_holiday']) ? $holiday['is_national_holiday'] === true : true;
                    });
    
                    $holidaysWithWeekends = array_merge($holidaysWithWeekends, $nationalHolidays);
                } else {
                    echo 'Failed to parse JSON response for year ' . $year . '.';
                }
            } else {
                echo 'Failed to fetch data from the API for year ' . $year . '.';
            }
        }
    
        return $holidaysWithWeekends;
    }
    


    public function styles(Worksheet $sheet)
    {
        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();

        // date
        $startColumn = 'F';
        $dateHeadersCount = count($this->dateHeaders);
        $numberOfColumns = $dateHeadersCount;
        $endColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($startColumn) + $numberOfColumns - 1;
        $endColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endColumnIndex);
        
        $holidays = $this->fetchNationalDays(substr($this->startDate, 0, 4), substr($this->endDate, 0, 4));
        
        for ($columnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($startColumn); $columnIndex <= $endColumnIndex; $columnIndex++) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
        
            $date = Carbon::parse($this->startDate)->addDays($columnIndex - \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($startColumn));
        
            $isHoliday = false;
            foreach ($holidays as $holiday) {
                $holidayDate = Carbon::parse($holiday['holiday_date'])->format('Y-m-d');
                $currentDate = $date->format('Y-m-d');
                if ($currentDate === $holidayDate) {
                    $isHoliday = true;
                    break;
                }
            }
        
            $sheet->getStyle($column . '5:' . $column . $lastRow)->applyFromArray([
                'fill' => [
                    'fillType' => $isHoliday ? \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID : \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE,
                    'startColor' => $isHoliday ? ['rgb' => 'B8CCE4'] : ['rgb' => 'FFFFFF'],
                ],
            ]);   
        }

        $lastRow = intval($lastRow);
        $sheet->getStyle('B' . ($lastRow + 1) . ':' . $lastColumn . ($lastRow + 1))->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C2D9FF'],
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'horizontal' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        
        // heading
        $columnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastColumn);

        $targetColumnheading = $columnIndex - 1; 
        $targetColumnNameheading = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($targetColumnheading);
        $lastRowCoordinate = $targetColumnNameheading . 2;
        $sheet->setCellValue($lastRowCoordinate, 'REKAP TOTAL ABSENSI PEGAWAI');
        

        // baris 
        $sheet->getStyle('F3:' . $lastColumn . '3')->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'width' => [
                'size' => 3,
            ],
        ]);


        $targetColumnIndex3 = $columnIndex - 6; 
        $targetColumnName3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($targetColumnIndex3);
        $sheet->mergeCells('B2:' . $targetColumnName3 . '2');
        $sheet->getStyle('B2:' . $lastColumn . '2')->applyFromArray([
            'font' => ['name' => 'Calibri', 'size' => 13, 'bold' => true],
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
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'horizontal' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $targetColumnIndexb3 = $columnIndex - 6; 
        $targetColumnNameb3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($targetColumnIndexb3);
        $sheet->mergeCells('B3:' . $targetColumnNameb3 . '3');
        $sheet->getStyle('B3:' . $lastColumn . '3')->applyFromArray([
            'font' => ['name' => 'Calibri', 'size' => 12, 'bold' => true],
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
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'horizontal' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        
        $sheet->getStyle('B4:' . $lastColumn . '4')->applyFromArray([
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
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
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
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // TABLE KEDUA
        // 0 kolom dari terakhir

        $targetColumnIndex3 = $columnIndex - 0; 
        $targetColumnName3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($targetColumnIndex3);
        
        $sheet->getColumnDimension($targetColumnName3)->setWidth(20); 
        $sheet->getStyle($targetColumnName3 . '2:'. $targetColumnName3 . 10)->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],             
            ],
        ]);
        
        $sheet->getColumnDimension($targetColumnName3)->setWidth(20); 
        $sheet->getStyle($targetColumnName3 . '5:'. $targetColumnName3 . 10)->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],             
                'horizontal' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],             
            ],
        ]);
        // end

        // 1 kolom dari terakhir

        $targetColumnIndex3 = $columnIndex - 1; 
        $targetColumnName3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($targetColumnIndex3);
        
        $sheet->getColumnDimension($targetColumnName3)->setWidth(10); 
        $sheet->getStyle($targetColumnName3 . '4:'. $targetColumnName3 . 10)->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $targetColumnIndex3 = $columnIndex - 1; 
        $targetColumnName3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($targetColumnIndex3);
        
        $sheet->getColumnDimension($targetColumnName3)->setWidth(10); 
        $sheet->getStyle($targetColumnName3 . '5:'. $targetColumnName3 . 10)->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],             
                'horizontal' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],             
            ],
        ]);
        // end

        // 2 kolom dari terakhir

        $targetColumnIndex3 = $columnIndex - 2; 
        $targetColumnName3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($targetColumnIndex3);
        
        $sheet->getColumnDimension($targetColumnName3)->setWidth(20); 
        $sheet->getStyle($targetColumnName3 . '4:'. $targetColumnName3 . 10 )->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getColumnDimension($targetColumnName3)->setWidth(20); 
        $sheet->getStyle($targetColumnName3 . '5:'. $targetColumnName3 . 10)->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],             
                'horizontal' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],             
            ],
        ]);
        // end

         // 3 kolom dari terakhir


      $targetColumnIndex3 = $columnIndex - 3; 
      $targetColumnName3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($targetColumnIndex3);
      
      $sheet->getColumnDimension($targetColumnName3)->setWidth(5); 
      $sheet->getStyle($targetColumnName3 . '4:'. $targetColumnName3 . 10)->applyFromArray([
         'alignment' => [
             'horizontal' => 'center',
             'vertical' => 'center',
         ],
         'borders' => [
             'right' => [
                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                 'color' => ['rgb' => '000000'],
             ],
         ],
      ]);

            //  membuat baris ke 7 menjadi berwarna biru dibagian total
         $sheet->getStyle($targetColumnName3 . 11 . ':' . $lastColumn . 11)->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C2D9FF'],
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'horizontal' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
         ]);

      $sheet->getStyle($targetColumnName3 . ($lastRow + 1) . ':' . $lastColumn . ($lastRow + 1))->applyFromArray([
         'fill' => [
             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE,
         ],
         'borders' => [
             'outline' => [
                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
             ],
             'horizontal' => [
                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
                 'color' => ['rgb' => '000000'],
             ],
         ],
     ]);
     $sheet->getStyle($targetColumnName3 . '5:'. $targetColumnName3 . 10)->applyFromArray([
        'alignment' => [
            'horizontal' => 'center',
            'vertical' => 'center',
        ],
        'borders' => [
            'right' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => ['rgb' => '000000'],
            ],             
            'horizontal' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],             
        ],
    ]);
     // end

     // 4 kolom dari terakhir

     $targetColumnIndex4 = $columnIndex - 4; 
     $targetColumnName4 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($targetColumnIndex4);
     
     $sheet->getStyle($targetColumnName4 . '2:'. $targetColumnName4 . ($lastRow + 1))->applyFromArray([
         'fill' => [
             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
             'startColor' => ['rgb' => 'ffffff'], 
         ],
         'borders' => [
             'outline' => [
                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                 'color'      => ['rgb' => 'D4D4D4'],
             ],
             'horizontal' => [
                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                 'color'      => ['rgb' => 'D4D4D4'],
             ],
             'vertical' => [
                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                   'color'      => ['rgb' => 'D4D4D4'],
             ],
         ],
     ]);
     $sheet->getStyle($targetColumnName4 . '2:'. $targetColumnName4 . 10)->applyFromArray([
         'borders' => [
             'right' => [
                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                 'color' => ['rgb' => '000000'],
             ],
         ],
     ]);
     $sheet->getStyle($targetColumnName4 . '2:'. $targetColumnName4 . 10)->applyFromArray([
         'borders' => [
             'right' => [
                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                 'color' => ['rgb' => '000000'],
             ],
         ],
     ]);
     // end

     // 5 kolom dari terakhir

     $targetColumnIndex5 = $columnIndex - 5; 
     $targetColumnName = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($targetColumnIndex5);
     
     $sheet->getStyle($targetColumnName . '2:'. $targetColumnName .($lastRow + 1))->applyFromArray([
         'fill' => [
             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
             'startColor' => ['rgb' => 'ffffff'], 
         ],
         'borders' => [
             'outline' => [
                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                 'color'      => ['rgb' => 'D4D4D4'],
             ],
             'horizontal' => [
                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                 'color'      => ['rgb' => 'D4D4D4'],
             ],
             'vertical' => [
                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                 'color'      => ['rgb' => 'D4D4D4'],
             ],
             'left' => [
                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                 'color' => ['rgb' => '000000'],
             ],
         ],
     ]);
     // end
// Function to apply styles to a column


       // Apply styles to columns from 6th to 12th from the end
for ($i = 6; $i <= 12; $i++) {
    $targetColumnIndex = $columnIndex - $i;
    $targetColumnName = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($targetColumnIndex);

    $styleArray = [
        
        'alignment' => [
            'horizontal' => 'center',
            'vertical' => 'center',
        ],
    ];

    // Add additional styling for the 12th column
    if ($i == 12) {
        $styleArray['borders'] = [
            'left' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => ['rgb' => '000000'],
            ],
        ];
    }

    $sheet->getStyle($targetColumnName . '5:' . $targetColumnName . $lastRow)->applyFromArray($styleArray);
}

// Apply styles to all columns from 'C' to the last column
for ($col = 'C'; $col <= $lastColumn; $col++) {
    $sheet->getStyle($col)->getAlignment()->setVertical('center');
    $sheet->getStyle($col)->getAlignment()->setIndent(1);
}

// Get the updated last column and index
$lastColumn = $sheet->getHighestColumn();
$lastColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastColumn);


        return [
            4 => [
                'font' => ['name' => 'Calibri', 'size' => 11, 'bold' => true],
            ],
            'B4:B' . $lastRow => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
                'borders' => [
                    'left' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                    'right' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                    'horizontal' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
            
             
            'B4' => [
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
            'E4' => [
                'borders' => [

                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                    'horizontal' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                    'right' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],

            // ini buat date - table
            'F5:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColumnIndex - 6) . $lastRow => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
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
                    'right' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                    'left' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],



           
            'F5:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColumnIndex - 13) . $lastRow => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
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
                    'right' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                    'left' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],

         
            

            'C5:C' . $lastRow => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
                'borders' => [
                    'left' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                    'right' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                    'horizontal' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],

            'D5:D' . $lastRow => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
                'borders' => [
                    'left' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                    'right' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                    'horizontal' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],

            'E5:E' . $lastRow => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
                'borders' => [
                    'left' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                    'right' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                    'horizontal' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
            
        ];
        foreach ($styles as $range => $style) {
            $sheet->getStyle($range)->applyFromArray($style);
        }
    }
}
