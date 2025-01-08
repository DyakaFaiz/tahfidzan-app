<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Waktu;
use App\Models\Ziyadah;
use App\Models\DeresanA;
use App\Models\Murojaah;
use Illuminate\Http\Request;

use App\Models\TahsinBinnadhor;

use App\Models\MasterKetahfidzan;
use Illuminate\Validation\ValidationException;

class DashboardController extends Controller
{
    public function index(){
        $this->lembarBaru();
        
        $tglMin = Waktu::oldest()->first();
        $tglMax = Waktu::latest()->first();

        $tglMinFormatted = Carbon::parse($tglMin->tgl)->format('Y-m-d');
        $tglMaxFormatted = Carbon::parse($tglMax->tgl)->format('Y-m-d');

        $data = [
            'tglMin'  => $tglMinFormatted,
            'tglMax'  => $tglMaxFormatted,
            'title'  => 'Dashboard',
            'pageHeading'   => 'Dashboard',
            'url'   => 'dashboard'
        ];
        return view('dashboard', $data);
    }

    public function blangko(Request $request)
    {
        $idUser = session('idUser');
        $idRole = session('idRole');

        try {
            $request->validate([
                'tglAwal' => 'required|date',
                'tglAkhir' => 'required|date',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        }

        $formattedAwal = Carbon::parse($request->tglAwal)->format('Y-m-d');
        $formattedAkhir = Carbon::parse($request->tglAkhir)->format('Y-m-d');

        $textTglAwal = Carbon::parse($request->tglAwal)->locale('id')->format('d F Y');
        $textTglAkhir = Carbon::parse($request->tglAkhir)->locale('id')->format('d F Y');

        $getIdWaktu = Waktu::whereRaw('DATE(tgl) BETWEEN ? AND ?', [$formattedAwal, $formattedAkhir])->pluck('id');
        $idWaktus = $getIdWaktu->toArray();

        $deresanA = DeresanA::select('deresan_a.*', 'juzAwal.nomor AS juzAwal', 'santri.nama AS namaSantri', 'juzAkhir.nomor AS juzAkhir')
            ->leftJoin('santri', 'santri.id', '=', 'deresan_a.id_santri')
            ->leftJoin('master_juz AS juzAwal', 'juzAwal.id', '=', 'deresan_a.juz_awal')
            ->leftJoin('master_juz AS juzAkhir', 'juzAkhir.id', '=', 'deresan_a.juz_akhir')
            ->whereIn('id_waktu', $idWaktus)
            ->when($idRole == 2, function ($query) use ($idUser) {
                return $query->where('id_ustad', $idUser);
            })
            ->get();

        $murojaah = Murojaah::select('murojaah.*', 'juzAwal.nomor AS juzAwal', 'santri.nama AS namaSantri', 'juzAkhir.nomor AS juzAkhir')
                ->leftJoin('santri', 'santri.id', '=', 'murojaah.id_santri')
                ->leftJoin('master_juz AS juzAwal', 'juzAwal.id', '=', 'murojaah.juz_awal')
                ->leftJoin('master_juz AS juzAkhir', 'juzAkhir.id', '=', 'murojaah.juz_akhir')
                ->whereIn('id_waktu', $idWaktus)
                ->when($idRole == 2, function ($query) use ($idUser) {
                    return $query->where('id_ustad', $idUser);
                })
                ->get();
        
        $tahsinBinnadhor = TahsinBinnadhor::select('tahsin_binnadhor.*', 'juzAwal.nomor AS juzAwal', 'santri.nama AS namaSantri', 'juzAkhir.nomor AS juzAkhir')
                ->leftJoin('santri', 'santri.id', '=', 'tahsin_binnadhor.id_santri')
                ->leftJoin('master_juz AS juzAwal', 'juzAwal.id', '=', 'tahsin_binnadhor.juz_awal')
                ->leftJoin('master_juz AS juzAkhir', 'juzAkhir.id', '=', 'tahsin_binnadhor.juz_akhir')
                ->whereIn('id_waktu', $idWaktus)
                ->when($idRole == 2, function ($query) use ($idUser) {
                    return $query->where('id_ustad', $idUser);
                })
                ->get();
        
        $ziyadah = Ziyadah::select('ziyadah.*', 'juzAwal.nomor AS juzAwal', 'santri.nama AS namaSantri', 'juzAkhir.nomor AS juzAkhir')
                ->leftJoin('santri', 'santri.id', '=', 'ziyadah.id_santri')
                ->leftJoin('master_juz AS juzAwal', 'juzAwal.id', '=', 'ziyadah.juz_awal')
                ->leftJoin('master_juz AS juzAkhir', 'juzAkhir.id', '=', 'ziyadah.juz_akhir')
                ->whereIn('id_waktu', $idWaktus)
                ->when($idRole == 2, function ($query) use ($idUser) {
                    return $query->where('id_ustad', $idUser);
                })
                ->get();

        $allData = $deresanA->concat($murojaah)
                    ->concat($tahsinBinnadhor)
                    ->concat($ziyadah);

        // Mengambil data dari fungsi getHafalan untuk masing-masing kategori
        $dataDeresanA = $this->getHafalan($deresanA);
        $dataMurojaah = $this->getHafalan($murojaah);
        $dataTahsinBinnadhor = $this->getHafalan($tahsinBinnadhor);
        $dataZiyadah = $this->getHafalan($ziyadah);

        // Menghitung jumlah pojok untuk setiap kategori
        $jmlDeresanA = $this->jmlPojok($dataDeresanA);
        $jmlMurojaah = $this->jmlPojok($dataMurojaah);
        $jmlTahsinBinnadhor = $this->jmlPojok($dataTahsinBinnadhor);
        $jmlZiyadah = $this->jmlPojok($dataZiyadah);

        $kehadiran = $allData->groupBy('id_santri')->map(function ($groupBySantri) {
            return $groupBySantri->groupBy('id_waktu')->map(function ($groupByWaktu) {
                $setor = $groupByWaktu->where('kehadiran', 1)->count();
                $tidakSetor = $groupByWaktu->where('kehadiran', 0)->count();
                $izin = $groupByWaktu->where('kehadiran', 2)->count();
                $alpha = $groupByWaktu->where('kehadiran', 3)->count();
        
                return [
                    'setor' => $setor,
                    'tidakSetor' => $tidakSetor,
                    'izin' => $izin,
                    'alpha' => $alpha,
                ];
            });
        });
        
        $totalSetor = $kehadiran->map(function ($waktus) {
            return collect($waktus)->sum('setor');
        });
        $totalIzin = $kehadiran->map(function ($waktus) {
            return collect($waktus)->sum('izin');
        });
        $totalTidakSetor = $kehadiran->map(function ($waktus) {
            return collect($waktus)->sum('tidakSetor');
        });
        $totalAlpha = $kehadiran->map(function ($waktus) {
            return collect($waktus)->sum('alpha');
        });
        
        $result = $dataDeresanA->map(function ($deresanData, $idSantri) use ($dataMurojaah, $dataTahsinBinnadhor, $dataZiyadah, $jmlDeresanA, $jmlMurojaah, $jmlTahsinBinnadhor, $jmlZiyadah, $kehadiran, $totalSetor, $totalAlpha, $totalIzin, $totalTidakSetor) {
            $jmlAllPojok = ($jmlDeresanA[$idSantri] ?? 0) + ($jmlMurojaah[$idSantri] ?? 0) + ($jmlTahsinBinnadhor[$idSantri] ?? 0) + ($jmlZiyadah[$idSantri] ?? 0);
        
            return [
                'no' => $idSantri,
                'namaSantri'    => $deresanData['namaSantri'],
        
                'juzAwalZiyadah' => $dataZiyadah[$idSantri]['juzAwal'] ?? '-',
                'pojokAwalZiyadah' => $dataZiyadah[$idSantri]['pojokAwal'] ?? 0,
                'juzAkhirZiyadah' => $dataZiyadah[$idSantri]['juzAkhir'] ?? '-',
                'pojokAkhirZiyadah' => $dataZiyadah[$idSantri]['pojokAkhir'] ?? 0,
                'jmlZiyadah' => $jmlZiyadah[$idSantri] ?? 0,
                
                'juzAwalDeresanA' => $deresanData['juzAwal'] ?? '-',
                'pojokAwalDeresanA' => $deresanData['pojokAwal'] ?? 0,
                'juzAkhirDeresanA' => $deresanData['juzAkhir'] ?? '-',
                'pojokAkhirDeresanA' => $deresanData['pojokAkhir'] ?? 0,
                'jmlDeresanA' => $jmlDeresanA[$idSantri] ?? 0,
                
                'juzAwalMurojaah' => $dataMurojaah[$idSantri]['juzAwal'] ?? '-',
                'pojokAwalMurojaah' => $dataMurojaah[$idSantri]['pojokAwal'] ?? 0,
                'juzAkhirMurojaah' => $dataMurojaah[$idSantri]['juzAkhir'] ?? '-',
                'pojokAkhirMurojaah' => $dataMurojaah[$idSantri]['pojokAkhir'] ?? 0,
                'jmlMurojaah' => $jmlMurojaah[$idSantri] ?? 0,
        
                'totalSeluruhPojok' => $jmlAllPojok ?? 0,
        
                'lvlDeresan' => 'Testing',
        
                'juzAkhirTahsinBinnadhor' => $dataTahsinBinnadhor[$idSantri]['juzAkhir'] ?? '-',
                'pojokAkhirTahsinBinnadhor' => $dataTahsinBinnadhor[$idSantri]['pojokAkhir'] ?? 0,
                
                'tidakSetor' => $totalTidakSetor[$idSantri] ?? 0,
                'izin' => $totalIzin[$idSantri] ?? 0,
                'alpha' => $totalAlpha[$idSantri] ?? 0,
                'setor' => $totalSetor[$idSantri] ?? 0,
            ];
        });
        
        $data = [
            'dataBlangko' => $result,
            'txtTglAwal' => $textTglAwal,
            'txtTglAkhir' => $textTglAkhir,
            'title'  => 'Dashboard',
            'pageHeading'   => 'Dashboard',
            'url'   => 'dashboard'
        ];

        return response()->json($data);
    }

    private function getHafalan($data)
    {
        return $data->groupBy('id_santri')->map(function ($group) {
            $sortedGroup = $group->sortBy('id_waktu');
            
            return [
                'namaSantri' => $group->first()->namaSantri,
                'juzAwal' => $sortedGroup->first()->juzAwal,
                'pojokAwal' => $sortedGroup->first()->capaian_awal,
                'juzAkhir' => $sortedGroup->last()->juzAkhir,
                'pojokAkhir' => $sortedGroup->last()->capaian_akhir,
            ];
        });
    }

    function jmlPojok($data) {
        $maxPojok = 20;
        return $data->map(function ($item) use ($maxPojok) {
            if ($item['juzAkhir'] > $item['juzAwal']) {
                return ($maxPojok - $item['pojokAwal']) + $item['pojokAkhir'];
            } else {
                return $item['pojokAkhir'] - $item['pojokAwal'];
            }
        });
    }

    private function lembarBaru(){
        $waktu = Waktu::latest()->first();

        Carbon::setLocale('id');
        $now = now();
        $tglDb = Carbon::parse($waktu->tgl)->format('d');
        $tglHariIni = $now->format('d');
        $tglLengkapHariIni = $now->format('Y-m-d H:i:s');
        $hariIni = $now->translatedFormat('l');
        $cekDB = DeresanA::where('id_waktu', $waktu->id)->exists();

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
                    
                $waktuTerbaru = Waktu::latest()->first();
        
                if (!$waktuTerbaru) {
                    session()->flash('error', 'Gagal mendapatkan data waktu terbaru.');
                    return;
                }
        
                $masterTahfidzan = MasterKetahfidzan::get();
        
                $data = $masterTahfidzan->map(function ($row) use ($waktuTerbaru) {
                    return [
                        'id_waktu' => $waktuTerbaru->id,
                        'id_ustad' => $row->id_ustad,
                        'id_santri' => $row->id_santri,
                    ];
                })->toArray();
                
                $saveDeresanA = DeresanA::insert($data);
                $saveMurojaah = Murojaah::insert($data);
                $saveTahsinBinnadhor = TahsinBinnadhor::insert($data);
                $saveZiyadah = Ziyadah::insert($data);

                if (!$saveDeresanA || !$saveMurojaah || !$saveTahsinBinnadhor || !$saveZiyadah) {
                    session()->flash('error', 'Gagal membuat lembar hari ini, coba sekali lagi');
                } else {
                    session()->flash('success', 'Behasil, lembar baru siap digunakan');
                }
            } catch (\Exception $e) {
                session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }
    }
}