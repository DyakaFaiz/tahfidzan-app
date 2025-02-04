<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Models\TahsinBinnadhor;
use App\Http\Controllers\Controller;

class EvaluasiTahsinController extends Controller
{
    public function index(){
        $idUser = session('idUser');
        
        $queryTahsin = TahsinBinnadhor::select(
            'tahsin_binnadhor.id',
            'tahsin_binnadhor.catatan',
            'waktu.tgl AS tglSetor',
            'waktu.hari AS hariSetor',
            'santri.nama AS namaSantri',
            'tahsin_binnadhor.evaluated_at AS tglEvaluasi',
            'tahsin_binnadhor.evaluasi'
        )
        ->leftJoin('santri', 'santri.id', '=', 'tahsin_binnadhor.id_santri')
        ->leftJoin('waktu', 'waktu.id', '=', 'tahsin_binnadhor.id_waktu')
        ->whereNotNull('catatan')
        ->where('id_ustad', $idUser)
        ->where('catatan', 'like', '%evaluasi%')
        ->where(function($query) {
            $query->where('evaluasi', 0)
                  ->orWhere('evaluasi', 1);
        })
        ->get();

        $data = [
            'title'  => 'Evaluasi Tahsin Binnadhor',
            'pageHeading' => 'Evaluasi Tahsin Binnadhor',
            'url'   => 'ketahfidzan/evaluasi/tahsin-binnadhor',
            'dataTahsin' => $queryTahsin,
        ];

        return view('admin.evaluasi.tahsin-binnadhor.index', $data);
    }
}
