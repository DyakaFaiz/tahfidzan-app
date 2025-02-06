<?php

namespace App\Http\Controllers\admin;

use Carbon\Carbon;
use App\Models\Juz;
use App\Models\Waktu;
use App\Models\Santri;
use App\Models\MasterJuz;
use App\Models\MasterSurat;
use Illuminate\Http\Request;
use App\Models\TahsinBinnadhor;
use App\Models\MasterKetahfidzan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TahsinBinnadhorController extends Controller
{
    public function index()
    {
        $roleUser = session('idRole');
        $idUser = session('idUser');

        $waktu = Waktu::latest()->first();
        $juz = Juz::all();
        $masterSurat = MasterSurat::all()->map(function ($item) {
            $item->nomor = $item->nama;
            return $item;
        });
        
        $pojok = [];
        for ($i = 1; $i <= 29; $i++) {
            $pojok[] = (object) ['id' => $i,'nomor' => $i];
        }

        $status = collect([
            0 => (object) ['id' => 0, 'nomor' => 'Boyong'],
            1 => (object) ['id' => 1, 'nomor' => 'Masih Ziyadah'],
            2 => (object) ['id' => 2, 'nomor' => 'Khatam'],
            3 => (object) ['id' => 3, 'nomor' => 'Khotimin'],
        ]);

        $kehadiran = collect([
            0 => (object) ['id' => 0, 'nomor' => 'Tidak Setor'],
            1 => (object) ['id' => 1, 'nomor' => 'Ngaji Kitab'],
            2 => (object) ['id' => 2, 'nomor' => 'Izin'],
            3 => (object) ['id' => 3, 'nomor' => 'Alpha'],
        ]);

        $waktu = Waktu::latest()->first();
        $hari = $waktu->hari;
        $tgl = Carbon::parse($waktu->tgl)->translatedFormat('d F Y');

        $waktuForSelect = Waktu::latest()->first();

        switch($roleUser) {
            case 1:
                $tahfidzan = TahsinBinnadhor::select(
                        'tahsin_binnadhor.*',
                        'tahsin_binnadhor.id_waktu as idWaktu',
                        'ustad.nama as namaUstad',
                        'santri.nama as namaSantri',
                        'santri.status as statusSantri'
                    )
                    ->leftJoin('waktu as waktu', 'waktu.id', '=', 'tahsin_binnadhor.id_waktu')
                    ->leftJoin('users as ustad', 'ustad.id', '=', 'tahsin_binnadhor.id_ustad')
                    ->leftJoin('santri as santri', 'santri.id', '=', 'tahsin_binnadhor.id_santri')
                    ->where('id_waktu', $waktuForSelect->id)
                    ->get();
                break;
            case 2:
                $tahfidzan = TahsinBinnadhor::select(
                        'tahsin_binnadhor.*',
                        'tahsin_binnadhor.id_waktu as idWaktu',
                        'ustad.nama as namaUstad',
                        'santri.nama as namaSantri',
                        'santri.status as statusSantri'
                    )
                    ->leftJoin('waktu as waktu', 'waktu.id', '=', 'tahsin_binnadhor.id_waktu')
                    ->leftJoin('users as ustad', 'ustad.id', '=', 'tahsin_binnadhor.id_ustad')
                    ->leftJoin('santri as santri', 'santri.id', '=', 'tahsin_binnadhor.id_santri')
                    ->where('id_waktu', $waktuForSelect->id)
                    ->where('tahsin_binnadhor.id_ustad', $idUser)
                    ->get();
                break;
            default:
                break;
        }

        $data = [
            'tahfidzan'  => $tahfidzan,
            'title'  => 'Tahsin Binnadhor',
            'pageHeading'   => 'Tahfidzan Harian Tahsin Binnadhor ' . ($roleUser == 1 ? '| ADMIN' : ''),
            'url'   => 'ketahfidzan/tahfidzan-admin/tahsin-binnadhor',
            'waktu'   => $waktu,
            'juz'   => $juz,
            'pojok'   => $pojok,
            'status'   => $status,
            'hari'   => $hari,
            'tgl'   => $tgl,
            'masterSurat'   => $masterSurat,
            'kehadiran'   => $kehadiran,
        ];

        return view('admin.tahfidzan-harian.tahsin-binnadhor.index', $data);
    }
    public function updateValue(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'value' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!is_numeric($value) && !is_string($value)) {
                        $fail("The $attribute must be either a numeric or a string.");
                    }
                },
            ],
            'idWaktu' => 'required|numeric',
            'idUstad' => 'required|numeric',
        ]);
        
        if ($validator->fails()) 
        {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tahfidzan = TahsinBinnadhor::where('id', $id)
            ->where('id_waktu', $request->idWaktu)
            ->where('id_ustad', $request->idUstad)
            ->first();

        if (!$tahfidzan) 
        {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $field = '';
        switch($request->field)
        {
            case 'capaianAwal':
                $field = 'capaian_awal';
                break;
            case 'capaianAkhir':
                $field = 'capaian_akhir';
                break;
            case 'suratAwal':
                $field = 'id_surat_awal';
                break;
            case 'suratAkhir':
                $field = 'id_surat_akhir';
                break;
            case 'juzAwal':
                $field = 'juz_awal';
                break;
            case 'juzAkhir':
                $field = 'juz_akhir';
                break;
            case 'kehadiran':
                $field = 'kehadiran';
                break;
            case 'status':
                $field = 'status';
                break;
            case 'catatanTahsinBinnadhor':
                $field = 'catatan';
                break;
            default:
                return response()->json(['message' => 'Field tidak valid'], 400);
        }

        if($field == 'status' && $request->value != 1){
            $santri = Santri::where('id', $tahfidzan->id_santri)
            ->first();

            $santri->update([
                'status' => $request->value
            ]);
        }

        $idSurat = (int) $request->idSurat;

        $nomorList = MasterJuz::select('master_juz.nomor')
            ->leftJoin("master_surat", "master_surat.id", "=", "master_juz.id_surat_dari")
            ->where("id_surat_dari", $idSurat)
            ->orWhere("id_surat_sampai", $idSurat)
            ->orderBy('master_juz.nomor')
            ->orWhereRaw("FIND_IN_SET(?, id_surat_between)", [$idSurat])
            ->pluck('nomor')
            ->toArray(); // Mengubah hasil query menjadi array

        $jumlah = count($nomorList);

        if ($jumlah == 0) {
            $juz = null; // Jika tidak ada data, kembalikan null
        } else {
            $indexTengah = floor($jumlah / 2); // Cari indeks tengah
            $juz = $nomorList[$indexTengah]; // Ambil nomor tengahnya
        }

        $idParts = explode('-', $request->idTeks);
        $awalAkhir = isset($idParts[1]) ? $idParts[1] : null;

        $tahfidzan->update([
            'juz_' . $awalAkhir => $juz,
            $field =>    $request->value,
            'jumlah' =>    $request->jmlTahsinBinnadhor,
        ]);

        return response()->json(['message' => 'Berhasil merubah data', 'rowNumber' => $request->idTahfidzan, 'juz' => $juz, 'awalAkhir' => $awalAkhir], 200);
    }
}
