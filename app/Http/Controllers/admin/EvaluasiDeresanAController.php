<?php

namespace App\Http\Controllers\admin;

use App\Models\DeresanA;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EvaluasiDeresanAController extends Controller
{
    public function index()
    {
        $idUser = session('idUser');

        $queryDeresanA = DeresanA::select(
            'deresan_a.id',
            'deresan_a.catatan',
            'waktu.tgl AS tglSetor',
            'waktu.hari AS hariSetor',
            'santri.nama AS namaSantri',
            'deresan_a.evaluated_at AS tglEvaluasi',
            'deresan_a.evaluasi'
        )
        ->leftJoin('santri', 'santri.id', '=', 'deresan_a.id_santri')
        ->leftJoin('waktu', 'waktu.id', '=', 'deresan_a.id_waktu')
        ->whereNotNull('catatan')
        ->where('id_ustad', $idUser)
        ->where('catatan', 'like', '%evaluasi%')
        ->where(function($query) {
            $query->where('evaluasi', 0)
                  ->orWhere('evaluasi', 1);
        })
        ->get();
        $data = [
            'title'  => 'Evaluasi Deresan A',
            'pageHeading' => 'Evaluasi Deresan A',
            'url'   => 'ketahfidzan/evaluasi/deresan-a',
            'dataDeresanA' => $queryDeresanA,
        ];

        return view('admin.evaluasi.deresan-a.index', $data);
    }
}
