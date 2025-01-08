<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DeresanA;
use App\Models\Murojaah;
use App\Models\TahsinBinnadhor;
use App\Models\Ziyadah;
use Illuminate\Http\Request;

class EvaluasiController extends Controller
{
    public function index(){
        $idUser = session('idUser');

        $getDeresanA = DeresanA::select(
            'deresan_a.catatan',
            'waktu.tgl AS tglSetor',
            'waktu.hari AS hariSetor',
            'santri.nama AS namaSantri'
            )
        ->leftJoin('santri', 'santri.id', '=', 'deresan_a.id_santri')
        ->leftJoin('waktu', 'waktu.id', '=', 'deresan_a.id_waktu')
        ->whereNotNull('catatan')
        ->where('id_ustad', $idUser)
        ->get();

        $getMurojaah = Murojaah::select(
            'murojaah.catatan',
            'waktu.tgl AS tglSetor',
            'waktu.hari AS hariSetor',
            'santri.nama AS namaSantri'
            )
        ->leftJoin('santri', 'santri.id', '=', 'murojaah.id_santri')
        ->leftJoin('waktu', 'waktu.id', '=', 'murojaah.id_waktu')
        ->whereNotNull('catatan')
        ->where('id_ustad', $idUser)
        ->get();
        
        $getTahsin = TahsinBinnadhor::select(
            'tahsin_binnadhor.catatan',
            'waktu.tgl AS tglSetor',
            'waktu.hari AS hariSetor',
            'santri.nama AS namaSantri'
            )
        ->leftJoin('santri', 'santri.id', '=', 'tahsin_binnadhor.id_santri')
        ->leftJoin('waktu', 'waktu.id', '=', 'tahsin_binnadhor.id_waktu')
        ->whereNotNull('catatan')
        ->where('id_ustad', $idUser)
        ->get();

        $getZiyadah = Ziyadah::select(
            'ziyadah.catatan',
            'waktu.tgl AS tglSetor',
            'waktu.hari AS hariSetor',
            'santri.nama AS namaSantri'
            )
        ->leftJoin('santri', 'santri.id', '=', 'ziyadah.id_santri')
        ->leftJoin('waktu', 'waktu.id', '=', 'ziyadah.id_waktu')
        ->whereNotNull('catatan')
        ->where('id_ustad', $idUser)
        ->get();
        
        $data = [
            'title'  => 'Evaluasi',
            'pageHeading' => 'Evaluasi',
            'url'   => 'ketahfidzan/tahfidzan-admin/deresan-a',
            'dataDeresanA' => $getDeresanA,
            'dataMurojaah' => $getMurojaah,
            'dataTahsinBinnadhor' => $getTahsin,
            'dataZiyadah' => $getZiyadah,
        ];

        return view('admin.evaluasi.index', $data);
    }
}
