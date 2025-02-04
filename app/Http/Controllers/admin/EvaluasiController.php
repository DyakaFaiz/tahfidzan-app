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
