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
            'url'   => 'ketahfidzan/tahfidzan-admin/deresan-a',
            'dataDeresanA' => $queryDeresanA,
            'dataMurojaah' => $queryMuroajaah,
            'dataTahsin' => $queryTahsin,
            'dataZiyadah' => $queryZiyadah,
        ];

        return view('admin.evaluasi.index', $data);
    }

    public function updateEvaluasi(Request $request)
    {
        $id = $request->id;
        $kodeTahfidzan = $request->kdTahfidzan;

        if (!$kodeTahfidzan || !$id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter tidak lengkap.',
            ], 400);
        }

        switch($kodeTahfidzan){
            case 1:
                    $data = DeresanA::where('id', $id)->first();
                break;
            case 2:
                    $data = Murojaah::where('id', $id)->first();
                break;
            case 3:
                    $data = TahsinBinnadhor::where('id', $id)->first();
                break;
            case 4:
                    $data = Ziyadah::where('id', $id)->first();
                break;
            default:
                return "Kode Tahfidz tidak valid";
        }

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan.',
            ], 404);
        }

        $update = $data->update([
            'evaluasi' => 1,
            'evaluated_at' => now()
        ]);

        if($update){
            $message = 'Berhasil mengevaluasi Santri';
        }else{
            $message = 'Gagal mengevaluasi Santri';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
        ]);
    }
}
