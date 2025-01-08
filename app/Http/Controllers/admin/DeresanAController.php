<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DeresanA;
use App\Models\Waktu;
use App\Models\Juz;
use App\Models\MasterSurat;
use App\Models\MasterKetahfidzan;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;

class DeresanAController extends Controller
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

        // Carbon::setLocale('id');
        // $now = now();
        // $tglDb = Carbon::parse($waktu->tgl)->format('d');
        // $tglHariIni = $now->format('d');
        // $tglLengkapHariIni = $now->format('Y-m-d H:i:s');
        // $hariIni = $now->translatedFormat('l');
        // $cekDB = DeresanA::where('id_waktu', $waktu->id)->exists();

        // if ($tglDb != $tglHariIni) {
        //     try {
        //         $createWaktu = Waktu::create([
        //             'tgl' => $tglLengkapHariIni,
        //             'hari' => $hariIni,
        //         ]);
                
        //         if ($createWaktu) {
        //             session()->flash('success', 'Hari Telah berganti');
        //         } else {
        //             session()->flash('error', 'Data Waktu gagal ditambahkan.');
        //         }
                    
        //         $waktuTerbaru = Waktu::latest()->first();
        
        //         if (!$waktuTerbaru) {
        //             session()->flash('error', 'Gagal mendapatkan data waktu terbaru.');
        //             return;
        //         }
        
        //         $masterTahfidzan = MasterKetahfidzan::get();
        
        //         $data = $masterTahfidzan->map(function ($row) use ($waktuTerbaru) {
        //             return [
        //                 'id_waktu' => $waktuTerbaru->id,
        //                 'id_ustad' => $row->id_ustad,
        //                 'id_santri' => $row->id_santri,
        //             ];
        //         })->toArray();
                
        //         $save = DeresanA::insert($data);

        //         if(!$save){
        //             session()->flash('error', 'Gagal membuat lembar hari ini, coba sekali lagi');
        //         }else{
        //             session()->flash('success', 'Behasil, lembar baru siap digunakan');
        //         }
        //     } catch (\Exception $e) {
        //         session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        //     }
        // }
        // elseif($cekDB == false){
        //     $waktuTerbaru = Waktu::latest()->first();
        
        //         if (!$waktuTerbaru) {
        //             session()->flash('error', 'Gagal mendapatkan data waktu terbaru.');
        //             return;
        //         }
        
        //         $masterTahfidzan = MasterKetahfidzan::get();
        
        //         $data = $masterTahfidzan->map(function ($row) use ($waktuTerbaru) {
        //             return [
        //                 'id_waktu' => $waktuTerbaru->id,
        //                 'id_ustad' => $row->id_ustad,
        //                 'id_santri' => $row->id_santri,
        //             ];
        //         })->toArray();
                
        //         $save = DeresanA::insert($data);

        //         if(!$save){
        //             session()->flash('error', 'Gagal membuat lembar hari ini, coba sekali lagi');
        //         }else{
        //             session()->flash('success', 'Behasil, lembar baru siap digunakan');
        //         }
        // }

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
                $tahfidzan = DeresanA::select(
                        'deresan_a.*',
                        'deresan_a.id_waktu as idWaktu',
                        'ustad.nama as namaUstad',
                        'santri.nama as namaSantri'
                    )
                    ->leftJoin('waktu as waktu', 'waktu.id', '=', 'deresan_a.id_waktu')
                    ->leftJoin('users as ustad', 'ustad.id', '=', 'deresan_a.id_ustad')
                    ->leftJoin('santri as santri', 'santri.id', '=', 'deresan_a.id_santri')
                    ->where('id_waktu', $waktuForSelect->id)
                    ->get();
                break;
            case 2:
                $tahfidzan = DeresanA::select(
                        'deresan_a.*',
                        'deresan_a.id_waktu as idWaktu',
                        'ustad.nama as namaUstad',
                        'santri.nama as namaSantri'
                    )
                    ->leftJoin('waktu as waktu', 'waktu.id', '=', 'deresan_a.id_waktu')
                    ->leftJoin('users as ustad', 'ustad.id', '=', 'deresan_a.id_ustad')
                    ->leftJoin('santri as santri', 'santri.id', '=', 'deresan_a.id_santri')
                    ->where('id_waktu', $waktuForSelect->id)
                    ->where('deresan_a.id_ustad', $idUser)
                    ->get();
                break;
            default:
                break;
        }
                
        $data = [
            'tahfidzan'  => $tahfidzan,
            'title'  => 'Deresan A',
            'pageHeading' => 'Tahfidzan Harian Deresan A ' . ($roleUser == 1 ? '| ADMIN' : ''),
            'url'   => 'ketahfidzan/tahfidzan-admin/deresan-a',
            'waktu'   => $waktu,
            'juz'   => $juz,
            'pojok'   => $pojok,
            'status'   => $status,
            'hari'   => $hari,
            'tgl'   => $tgl,
            'masterSurat'   => $masterSurat,
            'kehadiran'   => $kehadiran,
        ];

        return view('admin.tahfidzan-harian.deresan-a.index', $data);
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

        $tahfidzan = DeresanA::where('id', $id)
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
            case 'catatanDeresanA':
                $field = 'catatan';
                break;
            default:
                return response()->json(['message' => 'Field tidak valid'], 400);
        }

        $tahfidzan->update([
            $field =>    $request->value,
            'jumlah' =>    $request->jmlDeresanA,
        ]);

        return response()->json(['message' => 'Berhasil merubah data'], 200);
    }
}