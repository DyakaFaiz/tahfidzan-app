<?php

namespace App\Http\Controllers\admin;

use Carbon\Carbon;
use App\Models\Juz;
use App\Models\Waktu;
use App\Models\Santri;
use App\Models\Murojaah;
use App\Models\MasterSurat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MurojaahController extends Controller
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
        for ($i = 1; $i <= 20; $i++) {
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
                $tahfidzan = Murojaah::select(
                        'murojaah.*',
                        'murojaah.id_waktu as idWaktu',
                        'ustad.nama as namaUstad',
                        'santri.nama as namaSantri',
                        'santri.status as statusSantri'
                    )
                    ->leftJoin('waktu as waktu', 'waktu.id', '=', 'murojaah.id_waktu')
                    ->leftJoin('users as ustad', 'ustad.id', '=', 'murojaah.id_ustad')
                    ->leftJoin('santri as santri', 'santri.id', '=', 'murojaah.id_santri')
                    ->where('id_waktu', $waktuForSelect->id)
                    ->get();
                break;
            case 2:
                $tahfidzan = Murojaah::select(
                        'murojaah.*',
                        'murojaah.id_waktu as idWaktu',
                        'ustad.nama as namaUstad',
                        'santri.nama as namaSantri',
                        'santri.status as statusSantri'
                    )
                    ->leftJoin('waktu as waktu', 'waktu.id', '=', 'murojaah.id_waktu')
                    ->leftJoin('users as ustad', 'ustad.id', '=', 'murojaah.id_ustad')
                    ->leftJoin('santri as santri', 'santri.id', '=', 'murojaah.id_santri')
                    ->where('id_waktu', $waktuForSelect->id)
                    ->where('murojaah.id_ustad', $idUser)
                    ->get();
                break;
            default:
                break;
        }

        $data = [
            'tahfidzan'  => $tahfidzan,
            'title'  => 'Murojaah',
            'pageHeading'   => 'Tahfidzan Harian Murojaah '. ($roleUser == 1 ? '| ADMIN' : ''),
            'url'   => 'ketahfidzan/tahfidzan-admin/murojaah',
            'waktu'   => $waktu,
            'juz'   => $juz,
            'pojok'   => $pojok,
            'status'   => $status,
            'hari'   => $hari,
            'tgl'   => $tgl,
            'masterSurat'   => $masterSurat,
            'kehadiran'   => $kehadiran,
        ];

        return view('admin.tahfidzan-harian.murojaah.index', $data);
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

        $tahfidzan = Murojaah::where('id', $id)
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
            case 'catatanMurojaah':
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

        $tahfidzan->update([
            $field =>    $request->value,
            'jumlah' =>    $request->jmlMurojaah,
        ]);

        return response()->json(['message' => 'Berhasil merubah data'], 200);
    }
}