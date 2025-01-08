<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;

use App\Models\Juz;
use App\Models\MasterKetahfidzan;
use App\Models\MasterSurat;
use App\Models\Tahfidzan;
use App\Models\Waktu;

use Illuminate\Http\Request;

use Carbon\Carbon;

use Illuminate\Support\Facades\Validator;

class TahfidzanController extends Controller
{
    public function index()
    {
        $waktu = Waktu::latest()->first();
        $juz = Juz::all();
        $masterSurat = MasterSurat::all()->map(function ($item) {
            $item->nomor = $item->nama;
            return $item;
        });

        Carbon::setLocale('id');
        $tglDb = Carbon::parse($waktu->tgl)->format('d');
        $tglHariIni = now()->format('d');
        $tglLengkapHariIni = now()->format('Y-m-d H:i:s');
        $hariIni = now()->translatedFormat('l');

        if ($tglDb != $tglHariIni) {
            try {
                $createWaktu = Waktu::create([
                    'tgl' => $tglLengkapHariIni,
                    'hari' => $hariIni,
                ]);
        
                if ($createWaktu) {
                    session()->flash('success', 'Hari Telah berganti');
                } else {
                    session()->flash('error', 'Data Waktu gagal ditambahkan.');
                }
        
                // Mendapatkan data waktu terbaru
                $waktuTerbaru = Waktu::latest()->first();
        
                if (!$waktuTerbaru) {
                    session()->flash('error', 'Gagal mendapatkan data waktu terbaru.');
                    return;
                }
        
                // Mendapatkan data master tahfidzan
                $masterTahfidzan = MasterKetahfidzan::get();
        
                foreach ($masterTahfidzan as $row) {
                    $createTahfidzan = Tahfidzan::create([
                        'id_waktu' => $waktuTerbaru->id,
                        'id_ustad' => $row->id_ustad,
                        'id_santri' => $row->id_santri,
                    ]);
        
                    if (!$createTahfidzan) {
                        session()->flash('error', 'Gagal menambahkan data Tahfidzan.');
                        break;
                    }
                }
            } catch (\Exception $e) {
                session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }

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

        $hari = $waktu->hari;
        $tgl = Carbon::parse($waktu->tgl)->translatedFormat('d F Y');

        $waktuForSelect = Waktu::latest()->first();

        $tahfidzan = Tahfidzan::select(
            'tahfidzan.*',
            'tahfidzan.id_waktu as idWaktu',
            'ustad.nama as namaUstad',
            'santri.nama as namaSantri',
        )
            ->leftJoin('waktu as waktu', 'waktu.id', '=', 'tahfidzan.id_waktu')
            ->leftJoin('users as ustad', 'ustad.id', '=', 'tahfidzan.id_ustad')
            ->leftJoin('santri as santri', 'santri.id', '=', 'tahfidzan.id_santri')
            ->where('id_waktu', $waktuForSelect->id)
            ->get();

        $data = [
            'tahfidzan'  => $tahfidzan,
            'title'  => 'Tahfidzan Page',
            'pageHeading'   => 'Tahfidzan Harian | ADMIN',
            'url'   => 'ketahfidzan/tahfidzan-admin',
            'waktu'   => $waktu,
            'juz'   => $juz,
            'pojok'   => $pojok,
            'status'   => $status,
            'hari'   => $hari,
            'tgl'   => $tgl,
            'masterSurat'   => $masterSurat,
            'kehadiran'   => $kehadiran,
        ];

        return view('admin.tahfidzan-harian.index', $data);
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

        $tahfidzan = Tahfidzan::where('id', $id)
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
            case 'z-j-awal':
                $field = 'ziadah_juz_awal';
                break;
            case 'z-p-awal':
                $field = 'ziadah_pojok_awal';
                break;
            case 'z-j-akhir':
                $field = 'ziadah_juz_akhir';
                break;
            case 'z-p-akhir':
                $field = 'ziadah_pojok_akhir';
                break;
            case 'd-a-j-awal':
                $field = 'deresan_a_juz_awal';
                break;
            case 'd-a-p-awal':
                $field = 'deresan_a_pojok_awal';
                break;
            case 'd-a-j-akhir':
                $field = 'deresan_a_juz_akhir';
                break;
            case 'd-a-p-akhir':
                $field = 'deresan_a_pojok_akhir';
                break;
            case 'd-b-j-awal':
                $field = 'deresan_b_juz_awal';
                break;
            case 'd-b-p-awal':
                $field = 'deresan_b_pojok_awal';
                break;
            case 'd-b-j-akhir':
                $field = 'deresan_b_juz_akhir';
                break;
            case 'd-b-p-akhir':
                $field = 'deresan_b_pojok_akhir';
                break;
            case 'l-d':
                $field = 'level_deresan';
                break;
            case 'b-j':
                $field = 'bin_nadhor_juz';
                break;
            case 'b-p':
                $field = 'bin_nadhor_pojok';
                break;
            case 'hdr-ts':
                $field = 'hdr_ts';
                break;
            case 'hdr-i':
                $field = 'hdr_i';
                break;
            case 'hdr-a':
                $field = 'hdr_a';
                break;
            case 'hdr-p':
                $field = 'hdr_p';
                break;
            default:
                return response()->json(['message' => 'Field tidak valid'], 400);
        }

        $tahfidzan->update([
            $field =>    $request->value,
            'ziadah_jml' =>    $request->jmlZiadah,
            'deresan_a_jml' =>    $request->jmlDeresanA,
            'deresan_b_jml' =>    $request->jmlDeresanB,
            'total_deresan' =>    $request->totalDeresan,
        ]);

        return response()->json(['message' => 'Berhasil merubah data'], 200);
    }
}
