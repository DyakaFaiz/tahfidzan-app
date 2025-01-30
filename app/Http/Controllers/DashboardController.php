<?php

namespace App\Http\Controllers;

use App\Exports\BlangkoExports;
use Maatwebsite\Excel\Facades\Excel;

use Carbon\Carbon;
use App\Models\Waktu;
use App\Models\Ziyadah;
use App\Models\DeresanA;
use App\Models\Murojaah;
use Illuminate\Http\Request;

use App\Models\TahsinBinnadhor;

use App\Models\MasterKetahfidzan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Maatwebsite\Excel\Events\AfterSheet;

class DashboardController extends Controller
{
    public function index()
    {
        $this->lembarBaru();

        $tglMin = Waktu::oldest()->first();
        $tglMax = Waktu::latest()->first();

        $tglMinFormatted = Carbon::parse($tglMin->tgl)->format('Y-m-d');
        $tglMaxFormatted = Carbon::parse($tglMax->tgl)->format('Y-m-d');

        // $data = MasterKetahfidzan::select(
        //     'master_ketahfidzan.*',
        //     'users.nama as namaUstad',
        //     'master_tingkatan.id AS idTingkatan',
        //     'master_tingkatan.tingkatan',
        //     'santri.status'
        // )
        // ->leftJoin('users', 'users.id', '=', 'master_ketahfidzan.id_ustad')
        // ->leftJoin('santri', 'santri.id', '=', 'master_ketahfidzan.id_santri')
        // ->leftJoin('master_kelas', 'master_kelas.id', '=', 'santri.id_kelas')
        // ->leftJoin('master_tingkatan', 'master_tingkatan.id', '=', 'master_kelas.id_tingkatan')
        // ->get();
        
        // // Filter data untuk hanya mengambil yang namaUstad tidak kosong
        // $filteredData = $data->filter(function ($row) {
        //     return !empty($row->namaUstad);
        // });
        
        // // Mengelompokkan data berdasarkan id_ustad dan menghitung jumlah id_santri unik
        // $jmlSantriPerUstad = $filteredData->groupBy('id_ustad')->map(function ($group) {
        //     return $group->unique('id_santri')->count();
        // });
        
        // // Mengambil data unik berdasarkan id_ustad
        // $uniqueDataPerUstad = $filteredData->unique('id_ustad');
        
        // // Transformasi data akhir
        // $transformedData = $uniqueDataPerUstad->map(function ($row, $index) use ($jmlSantriPerUstad, $filteredData) {
        //     // Membuat link untuk nama ustad
        //     $rowUstad = '<a href="' . route('ketahfidzan.ustad-tahfidz.detail', ['id' => $row->id_ustad]) . '">' . $row->namaUstad . '</a>';
            
        //     // Mengambil jumlah santri
        //     $jmlSantri = $jmlSantriPerUstad[$row->id_ustad] ?? 0;
            
        //     // Mengambil id_tingkatan dari data ini
        //     $idTingkatan = $row->idTingkatan;
            
        //     // Menghitung jumlah santri berdasarkan kategori target
        //     $santriZiyadahTarget = Target::where('id_tingkatan', $idTingkatan)
        //         ->where('nama', 'ziyadah')
        //         ->count();
            
        //     $santriZiyadahNonTarget = $filteredData->where('id_ustad', $row->id_ustad)
        //         ->whereIn('id_santri', function($query) use ($idTingkatan) {
        //             $query->select('santri.id')->from('santri')
        //                 ->join('master_kelas', 'master_kelas.id', '=', 'santri.id_kelas')
        //                 ->join('master_tingkatan', 'master_tingkatan.id', '=', 'master_kelas.id_tingkatan')
        //                 ->where(DB::raw('FIND_IN_SET(master_tingkatan.id, '. $idTingkatan .')'), '>', DB::raw('0'));
        //         })
        //         ->where('status', 0)  // Non-target
        //         ->count();
            
        //     $santriDeresanTarget = Target::where('id_tingkatan', $idTingkatan)
        //         ->where('nama', 'deresan')
        //         ->count();
            
        //     $santriDeresanNonTarget = $filteredData->where('id_ustad', $row->id_ustad)
        //         ->whereIn('id_santri', function($query) use ($idTingkatan) {
        //             $query->select('santri.id')->from('santri')
        //                 ->join('master_kelas', 'master_kelas.id', '=', 'santri.id_kelas')
        //                 ->join('master_tingkatan', 'master_tingkatan.id', '=', 'master_kelas.id_tingkatan')
        //                 ->where(DB::raw('FIND_IN_SET(master_tingkatan.id, '. $idTingkatan .')'), '>', DB::raw('0'));
        //         })
        //         ->where('status', 1)  // Non-target
        //         ->count();
            
        //     $santriLembagaTarget = Target::where('id_tingkatan', $idTingkatan)
        //         ->where('nama', 'lembaga')
        //         ->count();
            
        //     $santriLembagaNonTarget = $filteredData->where('id_ustad', $row->id_ustad)
        //         ->whereIn('id_santri', function($query) use ($idTingkatan) {
        //             $query->select('santri.id')->from('santri')
        //                 ->join('master_kelas', 'master_kelas.id', '=', 'santri.id_kelas')
        //                 ->join('master_tingkatan', 'master_tingkatan.id', '=', 'master_kelas.id_tingkatan')
        //                 ->where(DB::raw('FIND_IN_SET(master_tingkatan.id, '. $idTingkatan .')'), '>', DB::raw('0'));
        //         })
        //         ->where('status', 2)  // Non-target
        //         ->count();
            
        //     // Menghitung jumlah santri berdasarkan status
        //     $santriBoyong = $filteredData->where('id_ustad', $row->id_ustad)->where('status', 0)->count();
        //     $santriKhatam = $filteredData->where('id_ustad', $row->id_ustad)->where('status', 2)->count();
        //     $santriKhotimin = $filteredData->where('id_ustad', $row->id_ustad)->where('status', 3)->count();
            
        //     return [
        //         $index + 1,
        //         $rowUstad,
        //         $jmlSantri,
        //         $row->tingkatan,
        //         $santriZiyadahTarget,
        //         $santriZiyadahNonTarget,
        //         $santriDeresanTarget,
        //         $santriDeresanNonTarget,
        //         $santriLembagaTarget,
        //         $santriLembagaNonTarget,
        //         $santriBoyong,
        //         $santriKhatam,
        //         $santriKhotimin,
        //     ];
        // });
    
        // dd($transformedData);

        $data = [
            'tglMin'  => $tglMinFormatted,
            'tglMax'  => $tglMaxFormatted,
            'title'  => 'Dashboard',
            'pageHeading'   => 'Dashboard',
            'url'   => 'dashboard'
        ];
        return view('dashboard', $data);
    }

    public function diagramZiyadah(Request $request)
    {
        $idUser = session('idUser');
        $idRole = session('idRole');

        // Validasi input
        try {
            $request->validate([
                'tglAwal' => 'date',
                'tglAkhir' => 'date',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        }

        if ($request->has(['tglAwal', 'tglAkhir'])) 
        {
            $formattedAwal = Carbon::parse($request->tglAwal)->format('Y-m-d');
            $formattedAkhir = Carbon::parse($request->tglAkhir)->format('Y-m-d');

            $textTglAwal = Carbon::parse($request->tglAwal)->locale('id')->format('d F Y');
            $textTglAkhir = Carbon::parse($request->tglAkhir)->locale('id')->format('d F Y');

            $idWaktus = Waktu::whereRaw('DATE(tgl) BETWEEN ? AND ?', [$formattedAwal, $formattedAkhir])
                ->pluck('id')
                ->toArray();

            $ziyadahDonut = Ziyadah::select(
                'ziyadah.id_santri',
                'santri.nama AS namaSantri',
                DB::raw('SUM(ziyadah.jumlah) AS total_jumlah'),
                'master_target.jumlah AS targetJumlah',
                'ziyadah.status',
            )
            ->leftJoin('santri', 'santri.id', '=', 'ziyadah.id_santri')
            ->leftJoin('master_kelas', 'master_kelas.id', '=', 'santri.id_kelas')
            ->leftJoin('master_tingkatan', 'master_tingkatan.id', '=', 'master_kelas.id_tingkatan')
            ->leftJoin('master_target', DB::raw('FIND_IN_SET(master_tingkatan.id, master_target.id_tingkatan)'), '>', DB::raw('0'))
            ->where('master_target.nama', 'ziyadah')
            ->whereIn('id_waktu', $idWaktus)
            ->when($idRole == 2, function ($query) use ($idUser) {
                return $query->where('id_ustad', $idUser);
            })
            ->groupBy(
                'ziyadah.id_santri',
                'santri.nama',
                'master_target.jumlah',
                'ziyadah.status',
            )
            ->get();

            $totalSantri = $ziyadahDonut->unique('id_santri')->count();
            $target = $ziyadahDonut->filter(function ($item) {
                return $item->total_jumlah >= $item->targetJumlah;
            })->count();

            $khatam = $ziyadahDonut->filter(function ($item) {
                return $item->total_jumlah >= $item->targetJumlah && $item->status == 2;
            })->count();

            $tidakTarget = $totalSantri - $target;

            $persentaseTarget = $totalSantri > 0 ? ($target / $totalSantri) * 100 : 0;
            $persentaseTidakTarget = $totalSantri > 0 ? ($tidakTarget / $totalSantri) * 100 : 0;
            $persentaseKhatam = $totalSantri > 0 ? ($khatam / $totalSantri) * 100 : 0;
 
            $ziyadahStick = Ziyadah::select(
                'master_tingkatan.tingkatan',
                DB::raw('COUNT(DISTINCT ziyadah.id_santri) AS totalSantri'),
                DB::raw('SUM(CASE WHEN ziyadah.jumlah >= master_target.jumlah THEN 1 ELSE 0 END) AS totalTarget'),
                DB::raw('SUM(CASE WHEN ziyadah.jumlah >= master_target.jumlah AND ziyadah.status = 2 THEN 1 ELSE 0 END) AS totalKhatam'),
                DB::raw('SUM(CASE WHEN ziyadah.jumlah < master_target.jumlah THEN 1 ELSE 0 END) AS totalTidakTarget')
            )
            ->leftJoin('santri', 'santri.id', '=', 'ziyadah.id_santri')
            ->leftJoin('master_kelas', 'master_kelas.id', '=', 'santri.id_kelas')
            ->leftJoin('master_tingkatan', 'master_tingkatan.id', '=', 'master_kelas.id_tingkatan')
            ->leftJoin('master_target', DB::raw('FIND_IN_SET(master_tingkatan.id, master_target.id_tingkatan)'), '>', DB::raw('0'))
            ->when($idRole == 2, function ($query) use ($idUser) {
                return $query->where('ziyadah.id_ustad', $idUser);
            })
            ->whereIn('id_waktu', $idWaktus)
            ->whereNotNull('santri.id_kelas')
            ->where('master_target.nama', 'ziyadah')
            ->where('santri.id_kelas', '!=', 'boyong')
            ->where('santri.id_kelas', '!=', 25)
            ->groupBy('master_tingkatan.tingkatan')
            ->get();
            
            $dataDiagramStick = $ziyadahStick->mapWithKeys(function ($item) {
                return [
                    $item->tingkatan => [
                        'totalSantri' => $item->totalSantri,
                        'totalTarget' => $item->totalTarget,
                        'totalKhatam' => $item->totalKhatam,
                        'totalTidakTarget' => $item->totalTidakTarget,
                    ]
                ];
            });

            return response()->json([
                'persentaseKhatam' => $persentaseKhatam,
                'persentaseTarget' => round($persentaseTarget, 2),
                'persentaseTidakTarget' => round($persentaseTidakTarget, 2),
                'txtTglAwal' => $textTglAwal,
                'txtTglAkhir' => $textTglAkhir,
                'dataChart' => $dataDiagramStick
            ]);
        }else{

            //Diagram Donut 

            $ziyadahDonut = Ziyadah::select(
                'ziyadah.id_santri',
                'santri.nama AS namaSantri',
                DB::raw('SUM(ziyadah.jumlah) AS total_jumlah'),
                'master_target.jumlah AS targetJumlah',
                'ziyadah.status',
            )
            ->leftJoin('santri', 'santri.id', '=', 'ziyadah.id_santri')
            ->leftJoin('master_kelas', 'master_kelas.id', '=', 'santri.id_kelas')
            ->leftJoin('master_tingkatan', 'master_tingkatan.id', '=', 'master_kelas.id_tingkatan')
            ->leftJoin('master_target', DB::raw('FIND_IN_SET(master_tingkatan.id, master_target.id_tingkatan)'), '>', DB::raw('0'))
            ->where('master_target.nama', 'ziyadah')
            ->when($idRole == 2, function ($query) use ($idUser) {
                return $query->where('id_ustad', $idUser);
            })
            ->groupBy(
                'ziyadah.id_santri',
                'santri.nama',
                'master_target.jumlah',
                'ziyadah.status',
            )
            ->get();

            $totalSantri = $ziyadahDonut->unique('id_santri')->count();
            $target = $ziyadahDonut->filter(function ($item) {
                return $item->total_jumlah >= $item->targetJumlah;
            })->count();

            $khatam = $ziyadahDonut->filter(function ($item) {
                return $item->total_jumlah >= $item->targetJumlah && $item->status == 2;
            })->count();

            $tidakTarget = $totalSantri - $target;

            $persentaseTarget = $totalSantri > 0 ? ($target / $totalSantri) * 100 : 0;
            $persentaseTidakTarget = $totalSantri > 0 ? ($tidakTarget / $totalSantri) * 100 : 0;
            $persentaseKhatam = $totalSantri > 0 ? ($khatam / $totalSantri) * 100 : 0;


            // Diagram Stick
            $ziyadahStick = Ziyadah::select(
                    'master_tingkatan.tingkatan',
                    DB::raw('COUNT(DISTINCT ziyadah.id_santri) AS totalSantri'),
                    DB::raw('SUM(CASE WHEN ziyadah.jumlah >= master_target.jumlah THEN 1 ELSE 0 END) AS totalTarget'),
                    DB::raw('SUM(CASE WHEN ziyadah.jumlah >= master_target.jumlah AND ziyadah.status = 2 THEN 1 ELSE 0 END) AS totalKhatam'),
                    DB::raw('SUM(CASE WHEN ziyadah.jumlah < master_target.jumlah THEN 1 ELSE 0 END) AS totalTidakTarget')
                )
                ->leftJoin('santri', 'santri.id', '=', 'ziyadah.id_santri')
                ->leftJoin('master_kelas', 'master_kelas.id', '=', 'santri.id_kelas')
                ->leftJoin('master_tingkatan', 'master_tingkatan.id', '=', 'master_kelas.id_tingkatan')
                ->leftJoin('master_target', DB::raw('FIND_IN_SET(master_tingkatan.id, master_target.id_tingkatan)'), '>', DB::raw('0'))
                ->when($idRole == 2, function ($query) use ($idUser) {
                    return $query->where('ziyadah.id_ustad', $idUser);
                })
                ->whereNotNull('santri.id_kelas')
                ->where('master_target.nama', 'ziyadah')
                ->where('santri.id_kelas', '!=', 'boyong')
                ->where('santri.id_kelas', '!=', 25)
                ->groupBy('master_tingkatan.tingkatan')
                ->get();
            
            $dataDiagramStick = $ziyadahStick->mapWithKeys(function ($item) {
                return [
                    $item->tingkatan => [
                        'totalSantri' => $item->totalSantri,
                        'totalTarget' => $item->totalTarget,
                        'totalKhatam' => $item->totalKhatam,
                        'totalTidakTarget' => $item->totalTidakTarget,
                    ]
                ];
            });

            return response()->json([
                'persentaseKhatam' => $persentaseKhatam,
                'persentaseTarget' => round($persentaseTarget, 2),
                'persentaseTidakTarget' => round($persentaseTidakTarget, 2),
                'dataChart' => $dataDiagramStick
            ]);
        }

        // Jika parameter tidak valid
        return response()->json([
            'success' => false,
            'message' => 'Parameter tglAwal dan tglAkhir diperlukan.'
        ], 400);
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
        
                'lvlDeresan' => ($jmlAllPojok > 5) ? "A" : (($jmlAllPojok == 5) ? "B" : (($jmlAllPojok >= 3 && $jmlAllPojok <= 4) ? "C" : (($jmlAllPojok >= 1 && $jmlAllPojok <= 2) ? "K" : "-"))),
        
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

    public function kondisiHalaqoh(Request $request)
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


    }

    // public function exportBlangko(Request $request)
    // {
    //     return Excel::download(new BlangkoExports, 'data.xlsx');
    // }
    public function exportBlangko(Request $request)
    {
        // Buat objek spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Menambahkan 3 baris kosong di atas data
        for ($i = 1; $i <= 3; $i++) {
            $sheet->insertNewRowBefore($i, 1); // Menyisipkan baris kosong
        }

        // Menambahkan merge cells dan pengaturan header
        $sheet->mergeCells('C1:F1');
        $sheet->mergeCells('H1:K1');
        $sheet->mergeCells('M1:P1');
        // $sheet->mergeCells('T1:U1');
        // $sheet->mergeCells('V1:Y1');

        $sheet->mergeCells('C2:D2');
        $sheet->mergeCells('E2:F2');

        $sheet->mergeCells('H2:I2');
        $sheet->mergeCells('J2:K2');

        $sheet->mergeCells('M2:N2');
        $sheet->mergeCells('O2:P2');
        
        $sheet->mergeCells('A1:A3');
        $sheet->mergeCells('B1:B3');
        $sheet->mergeCells('G1:G3');
        $sheet->mergeCells('L1:L3');
        // $sheet->mergeCells('Q1:Q3');
        // $sheet->mergeCells('R1:R3');
        // $sheet->mergeCells('S1:S3');

        // $sheet->mergeCells('T1:T2');
        // $sheet->mergeCells('U1:U2');

        // $sheet->mergeCells('V1:V2');
        // $sheet->mergeCells('W1:W2');
        // $sheet->mergeCells('X1:X2');
        // $sheet->mergeCells('Y1:Y2');

        // Set label untuk merged cells
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'ZIADAH');
        $sheet->setCellValue('G1', 'JML(POJOK)');
        $sheet->setCellValue('H1', 'DERESAN A');
        $sheet->setCellValue('L1', 'JML(POJOK)');
        $sheet->setCellValue('M1', 'DERESAN B');
        $sheet->setCellValue('T1', 'BIN NADHOR');
        $sheet->setCellValue('V1', 'KEHADIRAN');
        
        // Set label untuk merged cells di baris kedua
        $sheet->setCellValue('C2', 'AWAL');
        $sheet->setCellValue('E2', 'AKHIR');
        $sheet->setCellValue('H2', 'AWAL');
        $sheet->setCellValue('J2', 'AKHIR');
        $sheet->setCellValue('M2', 'AWAL');
        $sheet->setCellValue('O2', 'AKHIR');
        
        $sheet->setCellValue('C3', 'JUZ');
        $sheet->setCellValue('D3', 'PJ');

        $sheet->setCellValue('E3', 'JUZ');
        $sheet->setCellValue('F3', 'PJ');

        $sheet->setCellValue('H3', 'JUZ');
        $sheet->setCellValue('I3', 'PJ');

        $sheet->setCellValue('J3', 'JUZ');
        $sheet->setCellValue('K3', 'PJ');

        $sheet->setCellValue('M3', 'JUZ');
        $sheet->setCellValue('N3', 'PJ');

        $sheet->setCellValue('O3', 'JUZ');
        $sheet->setCellValue('P3', 'PJ');
        
        // Set style untuk font dan alignment
        $sheet->getStyle('A1:X1')->getFont()->setBold(true);
        $sheet->getStyle('A2:X2')->getFont()->setBold(true);
        $sheet->getStyle('A3:X3')->getFont()->setBold(true);
        $sheet->getStyle('A1:X3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:X3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Menentukan data yang ingin dimasukkan
        $data = [
            ['2', 'Jane Doe', '15', '50', '14', '48', '5', '65', '6', '8', '40', '50', '60', '6', '3', '5', '4', '130', '130', '3', '4', '2', '1', '4', '3']
        ];

        // Menambahkan data ke baris berikutnya (baris ke-4 setelah 3 baris kosong)
        $row = 4;
        foreach ($data as $rowData) {
            $sheet->fromArray($rowData, null, 'A' . $row++);
        }

        // Menulis file Excel langsung ke output untuk diunduh
        $writer = new Xlsx($spreadsheet);
        $filename = 'data.xlsx';

        return response()->stream(
            function() use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
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

    private function jmlPojok($data) {
        $maxPojok = 20;
        return $data->map(function ($item) use ($maxPojok) {
            if ($item['juzAkhir'] > $item['juzAwal']) {
                return ($maxPojok - $item['pojokAwal']) + $item['pojokAkhir'];
            } else {
                return $item['pojokAkhir'] - $item['pojokAwal'];
            }
        });
    }

    private function lembarBaru()
    {
        $waktu = Waktu::latest()->first();

        Carbon::setLocale('id');
        $now = now();
        $tglDb = Carbon::parse($waktu->tgl)->format('d');
        $tglHariIni = $now->format('d');
        $tglLengkapHariIni = $now->format('Y-m-d H:i:s');
        $hariIni = $now->translatedFormat('l');

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
        
                $masterTahfidzan = MasterKetahfidzan::select(
                    'master_ketahfidzan.*',
                    'santri.status',
                    )
                ->leftJoin('santri', 'santri.id', '=', 'master_ketahfidzan.id_santri')
                ->get();
        
                $data = $masterTahfidzan->map(function ($row) use ($waktuTerbaru) {
                    return [
                        'id_waktu' => $waktuTerbaru->id,
                        'id_ustad' => $row->id_ustad,
                        'id_santri' => $row->id_santri,
                        'status' => $row->status,
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