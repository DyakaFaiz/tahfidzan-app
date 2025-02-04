<?php

namespace App\Http\Controllers\admin;

use App\Models\Ziyadah;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EvaluasiZiyadahController extends Controller
{
    public function index(){
        $idUser = session('idUser');

        $queryZiyadah = Ziyadah::select(
            'ziyadah.id',
            'ziyadah.catatan',
            'waktu.tgl AS tglSetor',
            'waktu.hari AS hariSetor',
            'santri.nama AS namaSantri',
            'ziyadah.evaluated_at AS tglEvaluasi',
            'ziyadah.evaluasi'
        )
        ->leftJoin('santri', 'santri.id', '=', 'ziyadah.id_santri')
        ->leftJoin('waktu', 'waktu.id', '=', 'ziyadah.id_waktu')
        ->whereNotNull('catatan')
        ->where('id_ustad', $idUser)
        ->where('catatan', 'like', '%evaluasi%')
        ->where(function($query) {
            $query->where('evaluasi', 0)
                  ->orWhere('evaluasi', 1);
        })
        ->get();
        
        $data = [
            'title'  => 'Evaluasi',
            'pageHeading' => 'Evaluasi',
            'url'   => 'ketahfidzan/evaluasi/ziyadah',
            'dataZiyadah' => $queryZiyadah,
        ];

        return view('admin.evaluasi.ziyadah.index', $data);
    }
}
