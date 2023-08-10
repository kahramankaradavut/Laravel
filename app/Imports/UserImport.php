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
        $baslamaTarihi = null;
        if (!empty($row[env('START_DATE')])) {
            $dateBaslama = Carbon::createFromFormat('M d, Y', $row[env('START_DATE')]);
            $baslamaTarihi = $dateBaslama->format('Y.m.d');
        }
    
        $bitisTarihi = null;
        if (!empty($row[env('END_DATE')])) {
            $dateBitis = Carbon::createFromFormat('M d, Y', $row[env('END_DATE')]);
            $bitisTarihi = $dateBitis->format('Y.m.d');
        }
        
        $sonaErmeTarihi = null;
        $dateSonaErme = null; // Burada $dateSonaErme'yi tanımlıyoruz
        if (!empty($row[env('DUE_DATE')])) {
            $dateSonaErme = Carbon::createFromFormat('M d, Y', $row[env('DUE_DATE')]);
            $sonaErmeTarihi = $dateSonaErme->format('Y.m.d');
        }
    
        $tarihFarki = null;
        $renk = null;
    
        if ($bitisTarihi && $sonaErmeTarihi) { // $dateSonaErme yerine $sonaErmeTarihi kullanıyoruz
            $tarihFarki = $dateSonaErme->diffInDays($dateBitis);
            if ($dateBitis > $dateSonaErme) {
                $tarihFarki *= -1;
            }
            $renk = $tarihFarki >= 0 ? '1' : '2';
        }
    
        if($row[env('ASSIGNEES')]) {
            $user = new Task([
                'title' => $row[env('TITLE')] ?? null,
                'assignees' => $row[env('ASSIGNEES')] ?? null,
                'status' => $row[env('STATUS')] ?? null,
                'labels' => $row[env('LABELS')] ?? null,
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
