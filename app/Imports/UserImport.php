<?php

namespace App\Imports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Carbon\Carbon;
use App\Imports\envNameFormatter;

class UserImport implements ToModel, WithHeadingRow, WithBatchInserts
{
    private $textInput;

    protected $mainTableId;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function __construct($mainTableId) {
        $this->mainTableId = $mainTableId;
        
    }
    
public function envNameFormatter ($name) {
 $name = str_replace(' ','_', $name);
    return strtolower($name);
}

    public function model(array $row)
    {
        $dueDateColumn = env("DUE_DATE", 'schedule_date');

        $startDate = self::envNameFormatter(env("START_DATE"));
        $endDate = self::envNameFormatter(env("END_DATE"));
        $dueDate = self::envNameFormatter(env("DUE_DATE"));
        $title = self::envNameFormatter(env("TITLE"));
        $assignees = self::envNameFormatter(env("ASSIGNEES"));
        $status = self::envNameFormatter(env("STATUS"));
        $labels = self::envNameFormatter(env("LABELS"));


        $baslamaTarihi = null;
        if (!empty($row[$startDate])) {
            $dateBaslama = Carbon::createFromFormat('M d, Y', $row[$startDate]);
            $baslamaTarihi = $dateBaslama->format('Y.m.d');
        }
    
        $bitisTarihi = null;
        if (!empty($row[$endDate])) {
            $dateBitis = Carbon::createFromFormat('M d, Y', $row[$endDate]);
            $bitisTarihi = $dateBitis->format('Y.m.d');
        }
        
        $sonaErmeTarihi = null;
        $dateSonaErme = null; 
        if (!empty($row[$dueDate])) {
            $dateSonaErme = Carbon::createFromFormat('M d, Y', $row[$dueDate]);
            $sonaErmeTarihi = $dateSonaErme->format('Y.m.d');
        }
    
        $tarihFarki = null;
        $renk = null;
    
        if ($bitisTarihi && $sonaErmeTarihi) { 
            $tarihFarki = $dateSonaErme->diffInDays($dateBitis);
            if ($dateBitis > $dateSonaErme) {
                $tarihFarki *= -1;
            }
            $renk = $tarihFarki >= 0 ? '1' : '2';
        }
    
        if($row[$assignees]) {
            $user = new Task([
                'title' => $row[$title] ?? null,
                'assignees' => $row[$assignees] ?? null,
                'status' => $row[$status] ?? null,
                'labels' => $row[$labels] ?? null,
                'start_date' => $baslamaTarihi ?? null,
                'end_date' => $bitisTarihi ?? null,
                'due_date' => $sonaErmeTarihi ?? null,
                'complation_status' => $tarihFarki ?? null,
                'complation_status_color' => $renk ?? null,
                'project_defination_id' => $this->mainTableId
            ]);
    
            return $user;
        }
    }
    

    public function batchSize(): int
    {
        return 1000;
    }
}
