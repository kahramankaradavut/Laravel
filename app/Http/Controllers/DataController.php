<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectDefination;
use App\Models\Employees;

class DataController extends Controller
{
    public function deleteData(Request $request)
    {
        dd($request);
        $password = $request->input('password');
        $dataType = $request->input('type');
        $dataId = $request->input('id');

        if ($password === env('DELETE_PASSWORD')) {
            if ($dataType === 'user') {
                // Kullanıcı verilerini silme işlemi
                Employees::where('id', $dataId)->delete();
                Session::flash('success', 'Kullanıcı verileri başarıyla silindi.');
                return redirect()->route('user-list');
            } elseif ($dataType === 'project') {
                // Proje verilerini silme işlemi
                ProjectDefination::where('id', $dataId)->delete();
                Session::flash('success', 'Proje verileri başarıyla silindi.');
                return redirect()->route('project-list');
            }
        }

        Session::flash('error', 'Geçersiz şifre. Veriler silinemedi.');
        return redirect()->back();
    }
}
