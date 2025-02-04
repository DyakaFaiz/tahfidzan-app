<?php

namespace App\Http\Controllers\admin;

use App\Models\Murojaah;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EvaluasiMurojaahController extends Controller
{
    public function index()
    {
        $idUser = session('idUser');

        $queryMuroajaah = Murojaah::select(
            'murojaah.id',
            'murojaah.catatan',
            'waktu.tgl AS tglSetor',
            'waktu.hari AS hariSetor',
            'santri.nama AS namaSantri',
            'murojaah.evaluated_at AS tglEvaluasi',
            'murojaah.evaluasi'
        )
        ->leftJoin('santri', 'santri.id', '=', 'murojaah.id_santri')
        ->leftJoin('waktu', 'waktu.id', '=', 'murojaah.id_waktu')
        ->whereNotNull('catatan')
        ->where('id_ustad', $idUser)
        ->where('catatan', 'like', '%evaluasi%')
        ->where(function($query) {
            $query->where('evaluasi', 0)
                  ->orWhere('evaluasi', 1);
        })
        ->get();

        $data = [
            'title'  => 'Evaluasi Murojaah',
            'pageHeading' => 'Evaluasi Murojaah',
            'url'   => 'ketahfidzan/evaluasi/murojaah',
            'dataMurojaah' => $queryMuroajaah,
        ];

        return view('admin.evaluasi.murojaah.index', $data);
    }
}
