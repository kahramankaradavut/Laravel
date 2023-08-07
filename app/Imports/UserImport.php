<?php

namespace App\Imports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Carbon\Carbon;

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
    
    public function model(array $row)
    {
        //dd($row);
        $baslamaTarihi = null;
        if (!empty($row['start_date'])) {
            $dateBaslama = Carbon::createFromFormat('M d, Y', $row['start_date']);
            $baslamaTarihi = $dateBaslama->format('Y.m.d');
        } else {
            $baslamaTarihi = null;
        }

        $bitisTarihi = null;
        if (!empty($row['closed_date'])) {
            $dateBitis = Carbon::createFromFormat('M d, Y', $row['closed_date']);
            $bitisTarihi = $dateBitis->format('Y.m.d');
        } else {
            $bitisTarihi = null;
        }

        $sonaErmeTarihi = null;
        if (!empty($row['schedule_date'])) {
            $dateSonaErme = Carbon::createFromFormat('M d, Y', $row['schedule_date']);
            $sonaErmeTarihi = $dateSonaErme->format('Y.m.d');
        } else {
            $sonaErmeTarihi = null;
        }

        $tarihFarki = null;
        $renk = null;
        if ($bitisTarihi && $dateSonaErme) {
            $tarihFarki = $dateSonaErme->diffInDays($dateBitis);
            if ($dateBitis > $dateSonaErme) {
                $tarihFarki *= -1;
            }
            $renk = $tarihFarki >= 0 ? '1' : '2';
        }



        if($row['assignees']) {

            $user = new Task([
                'title' => $row['title'] ?? null,
                'assignees' => $row['assignees'] ?? null,
                'status' => $row['status'] ?? null,
                'labels' => $row['labels'] ?? null,
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
