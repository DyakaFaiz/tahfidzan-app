<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Waktu;

use App\Models\Target;
use App\Models\Ziyadah;
use App\Models\DeresanA;
use App\Models\Murojaah;
use Illuminate\Http\Request;
use App\Exports\BlangkoExports;

use App\Models\TahsinBinnadhor;

use App\Models\MasterKetahfidzan;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Validation\ValidationException;

class DashboardController extends Controller
{
    public function index()
    {
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

            // Ziyadah

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

            $persentaseTargetZiyadah = $totalSantri > 0 ? ($target / $totalSantri) * 100 : 0;
            $persentaseTidakTargetZiyadah = $totalSantri > 0 ? ($tidakTarget / $totalSantri) * 100 : 0;
            $persentaseKhatamZiyadah = $totalSantri > 0 ? ($khatam / $totalSantri) * 100 : 0;
 
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
            
            $dataGraphZiyadah = $ziyadahStick->mapWithKeys(function ($item) {
                return [
                    $item->tingkatan => [
                        'totalSantri' => $item->totalSantri,
                        'totalTarget' => $item->totalTarget,
                        'totalKhatam' => $item->totalKhatam,
                        'totalTidakTarget' => $item->totalTidakTarget,
                    ]
                ];
            });

            // Chart Deresan

            $deresanA = DeresanA::select(
                'deresan_a.id_santri',
                'santri.nama AS namaSantri',
                DB::raw('SUM(deresan_a.jumlah) AS total_jumlah'),
                'master_target.jumlah AS targetJumlah',
                'deresan_a.status',
            )
            ->leftJoin('santri', 'santri.id', '=', 'deresan_a.id_santri')
            ->leftJoin('master_kelas', 'master_kelas.id', '=', 'santri.id_kelas')
            ->leftJoin('master_tingkatan', 'master_tingkatan.id', '=', 'master_kelas.id_tingkatan')
            ->leftJoin('master_target', DB::raw('FIND_IN_SET(master_tingkatan.id, master_target.id_tingkatan)'), '>', DB::raw('0'))
            ->where('master_target.nama', 'deresan')
            ->whereIn('id_waktu', $idWaktus)
            ->when($idRole == 2, function ($query) use ($idUser) {
                return $query->where('id_ustad', $idUser);
            })
            ->groupBy(
                'deresan_a.id_santri',
                'santri.nama',
                'master_target.jumlah',
                'deresan_a.status',
            )
            ->get();

            $murojaah = Murojaah::select(
                'murojaah.id_santri',
                'santri.nama AS namaSantri',
                DB::raw('SUM(murojaah.jumlah) AS total_jumlah'),
                'master_target.jumlah AS targetJumlah',
                'murojaah.status',
            )
            ->leftJoin('santri', 'santri.id', '=', 'murojaah.id_santri')
            ->leftJoin('master_kelas', 'master_kelas.id', '=', 'santri.id_kelas')
            ->leftJoin('master_tingkatan', 'master_tingkatan.id', '=', 'master_kelas.id_tingkatan')
            ->leftJoin('master_target', DB::raw('FIND_IN_SET(master_tingkatan.id, master_target.id_tingkatan)'), '>', DB::raw('0'))
            ->where('master_target.nama', 'deresan')
            ->whereIn('id_waktu', $idWaktus)
            ->when($idRole == 2, function ($query) use ($idUser) {
                return $query->where('id_ustad', $idUser);
            })
            ->groupBy(
                'murojaah.id_santri',
                'santri.nama',
                'master_target.jumlah',
                'murojaah.status',
            )
            ->get();

            $deresan = $deresanA->merge($murojaah);

            $totalSantriDeresan = $deresan->unique('id_santri')->count();
            $targetDeresan = $deresan->filter(function ($item) {
                return $item->total_jumlah >= $item->targetJumlah;
            })->count();

            $tidakTertulis = $deresan->filter(function ($item) {
                return $item->total_jumlah == 0;
            })->count();

            $tidakTargetDeresan = $totalSantriDeresan - $targetDeresan;

            $persentaseTargetDeresan = $totalSantriDeresan > 0 ? ($targetDeresan / $totalSantriDeresan) * 100 : 0;
            $persentaseTidakTargetDeresan = $totalSantriDeresan > 0 ? ($tidakTargetDeresan / $totalSantriDeresan) * 100 : 0;
            $persentaseTidakTertulisDeresan = $totalSantriDeresan > 0 ? ($tidakTertulis / $totalSantriDeresan) * 100 : 0;


            // Graph Deresan
            $deresanGraph = DB::query()->fromSub(function ($query) use ($idWaktus, $idUser, $idRole) {
                $query->select(
                        'master_tingkatan.tingkatan',
                        DB::raw('COUNT(DISTINCT deresan_a.id_santri) AS totalSantri'),
                        DB::raw('COUNT(DISTINCT CASE WHEN deresan_a.jumlah >= master_target.jumlah THEN deresan_a.id_santri END) AS totalTarget'),
                        DB::raw('COUNT(DISTINCT CASE WHEN deresan_a.jumlah IS NULL THEN deresan_a.id_santri END) AS totalTidakTertulis'),
                        DB::raw('COUNT(DISTINCT CASE WHEN deresan_a.jumlah < master_target.jumlah THEN deresan_a.id_santri END) AS totalTidakTarget')
                    )
                    ->from('deresan_a')
                    ->leftJoin('santri', 'santri.id', '=', 'deresan_a.id_santri')
                    ->leftJoin('master_kelas', 'master_kelas.id', '=', 'santri.id_kelas')
                    ->leftJoin('master_tingkatan', 'master_tingkatan.id', '=', 'master_kelas.id_tingkatan')
                    ->leftJoin('master_target', DB::raw('FIND_IN_SET(master_tingkatan.id, master_target.id_tingkatan)'), '>', DB::raw('0'))
                    ->whereIn('id_waktu', $idWaktus)
                    ->when($idRole == 2, function ($query) use ($idUser) {
                        return $query->where('id_ustad', $idUser);
                    })
                    ->whereNotNull('santri.id_kelas')
                    ->where('master_target.nama', 'deresan')
                    ->whereNotIn('santri.id_kelas', ['boyong', 25])
                    ->groupBy('master_tingkatan.tingkatan')
            
                    ->unionAll(
                        DB::table('murojaah')
                            ->select(
                                'master_tingkatan.tingkatan',
                                DB::raw('COUNT(DISTINCT murojaah.id_santri) AS totalSantri'),
                                DB::raw('COUNT(DISTINCT CASE WHEN murojaah.jumlah >= master_target.jumlah THEN murojaah.id_santri END) AS totalTarget'),
                                DB::raw('COUNT(DISTINCT CASE WHEN murojaah.jumlah IS NULL THEN murojaah.id_santri END) AS totalTidakTertulis'),
                                DB::raw('COUNT(DISTINCT CASE WHEN murojaah.jumlah < master_target.jumlah THEN murojaah.id_santri END) AS totalTidakTarget')
                            )
                            ->leftJoin('santri', 'santri.id', '=', 'murojaah.id_santri')
                            ->leftJoin('master_kelas', 'master_kelas.id', '=', 'santri.id_kelas')
                            ->leftJoin('master_tingkatan', 'master_tingkatan.id', '=', 'master_kelas.id_tingkatan')
                            ->leftJoin('master_target', DB::raw('FIND_IN_SET(master_tingkatan.id, master_target.id_tingkatan)'), '>', DB::raw('0'))
                            ->whereIn('id_waktu', $idWaktus)
                            ->when($idRole == 2, function ($query) use ($idUser) {
                                return $query->where('id_ustad', $idUser);
                            })
                            ->whereNotNull('santri.id_kelas')
                            ->where('master_target.nama', 'deresan')
                            ->whereNotIn('santri.id_kelas', ['boyong', 25])
                            ->groupBy('master_tingkatan.tingkatan')
                    );
            }, 'combined_data')
            ->groupBy('tingkatan')
            ->select(
                'tingkatan',
                DB::raw('SUM(totalSantri) AS totalSantri'),
                DB::raw('SUM(totalTarget) AS totalTarget'),
                DB::raw('SUM(totalTidakTertulis) AS totalTidakTertulis'),
                DB::raw('SUM(totalTidakTarget) AS totalTidakTarget')
            )
            ->get();
    
            $dataDiagramGraphDeresan = $deresanGraph->mapWithKeys(function ($item) {
                return [
                    $item->tingkatan => [
                        'totalSantri' => $item->totalTarget + $item->totalTidakTertulis + $item->totalTidakTarget,
                        'totalTarget' => $item->totalTarget,
                        'totalTidakTertulis' => $item->totalTidakTertulis,
                        'totalTidakTarget' => $item->totalTidakTarget,
                    ]
                ];
            });

            return response()->json([
                'persentaseKhatamZiyadah' => $persentaseKhatamZiyadah,
                'persentaseTargetZiyadah' => round($persentaseTargetZiyadah, 2),
                'persentaseTidakTargetZiyadah' => round($persentaseTidakTargetZiyadah, 2),
                'txtTglAwal' => $textTglAwal,
                'txtTglAkhir' => $textTglAkhir,
                'dataGraphZiyadah' => $dataGraphZiyadah,
                
                'persentaseTidakTertulisDeresan' => $persentaseTidakTertulisDeresan,
                'persentaseTargetDeresan' => round($persentaseTargetDeresan, 2),
                'persentaseTidakTargetDeresan' => round($persentaseTidakTargetDeresan, 2),
                'dataGraphDeresan' => $dataDiagramGraphDeresan,
            ]);
        }
        else
        {

            //Chart Ziyadah 

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

            $persentaseTargetZiyadah = $totalSantri > 0 ? ($target / $totalSantri) * 100 : 0;
            $persentaseTidakTargetZiyadah = $totalSantri > 0 ? ($tidakTarget / $totalSantri) * 100 : 0;
            $persentaseKhatamZiyadah = $totalSantri > 0 ? ($khatam / $totalSantri) * 100 : 0;


            // Graph Ziyadah

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
                
            
            $dataGraphZiyadah = $ziyadahStick->mapWithKeys(function ($item) {
                return [
                    $item->tingkatan => [
                        'totalSantri' => $item->totalSantri,
                        'totalTarget' => $item->totalTarget,
                        'totalKhatam' => $item->totalKhatam,
                        'totalTidakTarget' => $item->totalTidakTarget,
                    ]
                ];
            });


            // Chart Deresan

            $deresanA = DeresanA::select(
                'deresan_a.id_santri',
                'santri.nama AS namaSantri',
                DB::raw('SUM(deresan_a.jumlah) AS total_jumlah'),
                'master_target.jumlah AS targetJumlah',
                'deresan_a.status',
            )
            ->leftJoin('santri', 'santri.id', '=', 'deresan_a.id_santri')
            ->leftJoin('master_kelas', 'master_kelas.id', '=', 'santri.id_kelas')
            ->leftJoin('master_tingkatan', 'master_tingkatan.id', '=', 'master_kelas.id_tingkatan')
            ->leftJoin('master_target', DB::raw('FIND_IN_SET(master_tingkatan.id, master_target.id_tingkatan)'), '>', DB::raw('0'))
            ->where('master_target.nama', 'deresan')
            ->when($idRole == 2, function ($query) use ($idUser) {
                return $query->where('id_ustad', $idUser);
            })
            ->groupBy(
                'deresan_a.id_santri',
                'santri.nama',
                'master_target.jumlah',
                'deresan_a.status',
            )
            ->get();

            $murojaah = Murojaah::select(
                'murojaah.id_santri',
                'santri.nama AS namaSantri',
                DB::raw('SUM(murojaah.jumlah) AS total_jumlah'),
                'master_target.jumlah AS targetJumlah',
                'murojaah.status',
            )
            ->leftJoin('santri', 'santri.id', '=', 'murojaah.id_santri')
            ->leftJoin('master_kelas', 'master_kelas.id', '=', 'santri.id_kelas')
            ->leftJoin('master_tingkatan', 'master_tingkatan.id', '=', 'master_kelas.id_tingkatan')
            ->leftJoin('master_target', DB::raw('FIND_IN_SET(master_tingkatan.id, master_target.id_tingkatan)'), '>', DB::raw('0'))
            ->where('master_target.nama', 'deresan')
            ->when($idRole == 2, function ($query) use ($idUser) {
                return $query->where('id_ustad', $idUser);
            })
            ->groupBy(
                'murojaah.id_santri',
                'santri.nama',
                'master_target.jumlah',
                'murojaah.status',
            )
            ->get();

            $deresan = $deresanA->merge($murojaah);

            $totalSantriDeresan = $deresan->unique('id_santri')->count();
            $targetDeresan = $deresan->filter(function ($item) {
                return $item->total_jumlah >= $item->targetJumlah;
            })->count();

            $tidakTertulis = $deresan->filter(function ($item) {
                return $item->total_jumlah == 0;
            })->count();

            $tidakTargetDeresan = $totalSantriDeresan - $targetDeresan;

            $persentaseTargetDeresan = $totalSantriDeresan > 0 ? ($targetDeresan / $totalSantriDeresan) * 100 : 0;
            $persentaseTidakTargetDeresan = $totalSantriDeresan > 0 ? ($tidakTargetDeresan / $totalSantriDeresan) * 100 : 0;
            $persentaseTidakTertulisDeresan = $totalSantriDeresan > 0 ? ($tidakTertulis / $totalSantriDeresan) * 100 : 0;


            // Graph Deresan
            $deresanGraph = DB::query()->fromSub(function ($query) {
                $query->select(
                        'master_tingkatan.tingkatan',
                        DB::raw('COUNT(DISTINCT deresan_a.id_santri) AS totalSantri'),
                        DB::raw('COUNT(DISTINCT CASE WHEN deresan_a.jumlah >= master_target.jumlah THEN deresan_a.id_santri END) AS totalTarget'),
                        DB::raw('COUNT(DISTINCT CASE WHEN deresan_a.jumlah IS NULL THEN deresan_a.id_santri END) AS totalTidakTertulis'),
                        DB::raw('COUNT(DISTINCT CASE WHEN deresan_a.jumlah < master_target.jumlah THEN deresan_a.id_santri END) AS totalTidakTarget')
                    )
                    ->from('deresan_a')
                    ->leftJoin('santri', 'santri.id', '=', 'deresan_a.id_santri')
                    ->leftJoin('master_kelas', 'master_kelas.id', '=', 'santri.id_kelas')
                    ->leftJoin('master_tingkatan', 'master_tingkatan.id', '=', 'master_kelas.id_tingkatan')
                    ->leftJoin('master_target', DB::raw('FIND_IN_SET(master_tingkatan.id, master_target.id_tingkatan)'), '>', DB::raw('0'))
                    ->whereNotNull('santri.id_kelas')
                    ->where('master_target.nama', 'deresan')
                    ->whereNotIn('santri.id_kelas', ['boyong', 25])
                    ->groupBy('master_tingkatan.tingkatan')
            
                    ->unionAll(
                        DB::table('murojaah')
                            ->select(
                                'master_tingkatan.tingkatan',
                                DB::raw('COUNT(DISTINCT murojaah.id_santri) AS totalSantri'),
                                DB::raw('COUNT(DISTINCT CASE WHEN murojaah.jumlah >= master_target.jumlah THEN murojaah.id_santri END) AS totalTarget'),
                                DB::raw('COUNT(DISTINCT CASE WHEN murojaah.jumlah IS NULL THEN murojaah.id_santri END) AS totalTidakTertulis'),
                                DB::raw('COUNT(DISTINCT CASE WHEN murojaah.jumlah < master_target.jumlah THEN murojaah.id_santri END) AS totalTidakTarget')
                            )
                            ->leftJoin('santri', 'santri.id', '=', 'murojaah.id_santri')
                            ->leftJoin('master_kelas', 'master_kelas.id', '=', 'santri.id_kelas')
                            ->leftJoin('master_tingkatan', 'master_tingkatan.id', '=', 'master_kelas.id_tingkatan')
                            ->leftJoin('master_target', DB::raw('FIND_IN_SET(master_tingkatan.id, master_target.id_tingkatan)'), '>', DB::raw('0'))
                            ->whereNotNull('santri.id_kelas')
                            ->where('master_target.nama', 'deresan')
                            ->whereNotIn('santri.id_kelas', ['boyong', 25])
                            ->groupBy('master_tingkatan.tingkatan')
                    );
            }, 'combined_data')
            ->groupBy('tingkatan')
            ->select(
                'tingkatan',
                DB::raw('SUM(totalSantri) AS totalSantri'),
                DB::raw('SUM(totalTarget) AS totalTarget'),
                DB::raw('SUM(totalTidakTertulis) AS totalTidakTertulis'),
                DB::raw('SUM(totalTidakTarget) AS totalTidakTarget')
            )
            ->get();
    
            $dataDiagramGraphDeresan = $deresanGraph->mapWithKeys(function ($item) {
                return [
                    $item->tingkatan => [
                        'totalSantri' => $item->totalTarget + $item->totalTidakTertulis + $item->totalTidakTarget,
                        'totalTarget' => $item->totalTarget,
                        'totalTidakTertulis' => $item->totalTidakTertulis,
                        'totalTidakTarget' => $item->totalTidakTarget,
                    ]
                ];
            });

            return response()->json([
                'persentaseKhatamZiyadah' => $persentaseKhatamZiyadah,
                'persentaseTargetZiyadah' => round($persentaseTargetZiyadah, 2),
                'persentaseTidakTargetZiyadah' => round($persentaseTidakTargetZiyadah, 2),
                'dataGraphZiyadah' => $dataGraphZiyadah,

                'persentaseTidakTertulisDeresan' => $persentaseTidakTertulisDeresan,
                'persentaseTargetDeresan' => round($persentaseTargetDeresan, 2),
                'persentaseTidakTargetDeresan' => round($persentaseTidakTargetDeresan, 2),
                'dataGraphDeresan' => $dataDiagramGraphDeresan,
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
                'error' => "Isikan terlebih dahulu tanggalnya",
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
            'url'   => 'dashboard',
            'formattedAwal'  => $formattedAwal,
            'formattedAkhir'  => $formattedAkhir,
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

    public function exportBlangko(Request $request)
    {
        $idUser = session('idUser');
        $idRole = session('idRole');
        $namaUser = session('namaUser');
        
        $formattedAwal = Carbon::parse($request->tglAwalBlangko)->format('Y-m-d');
        $formattedAkhir = Carbon::parse($request->tglAkhirBlangko)->format('Y-m-d');

        $textTglAwal = Carbon::parse($request->tglAwalBlangko)->locale('id')->format('d F Y');
        $textTglAkhir = Carbon::parse($request->tglAkhirBlangko)->locale('id')->format('d F Y');

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
        
            static $no = 1;  // Menetapkan nilai awal $no menjadi 1 dan increment pada setiap iterasi

            return [
                'no' => $no++,
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


        // SHEET 1

        // Buat objek spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Blangko Santri');

        // Menambahkan 3 baris kosong di atas data
        for ($i = 1; $i <= 3; $i++) {
            $sheet1->insertNewRowBefore($i, 1); // Menyisipkan baris kosong
        }
        
        // Menambahkan merge cells dan pengaturan header

        $sheet1->mergeCells('A1:H1');
        $sheet1->setCellValue('A1', 'BLANGKO REKAPAN SANTRI');
        $sheet1->getStyle('A1')->getFont()->setSize(16);

        $sheet1->mergeCells('A3:H3');
        $sheet1->setCellValue('A3', "Mulai Tanggal : " . $textTglAwal . " s/d " . $textTglAkhir);
        $sheet1->getStyle('A3')->getFont()->setSize(12); // Mengatur ukuran font menjadi 12

        $sheet1->mergeCells('T3:Y3');
        $sheet1->setCellValue('T3', "Halaqoh : " . $namaUser);
        $sheet1->getStyle('T3')->getFont()->setSize(12); // Mengatur ukuran font menjadi 12
        
        $sheet1->mergeCells('C5:F5');
        $sheet1->mergeCells('H5:K5');
        $sheet1->mergeCells('M5:P5');

        $sheet1->mergeCells('C6:D6');
        $sheet1->mergeCells('E6:F6');

        $sheet1->mergeCells('H6:I6');
        $sheet1->mergeCells('J6:K6');

        $sheet1->mergeCells('M6:N6');
        $sheet1->mergeCells('O6:P6');
        
        $sheet1->mergeCells('A5:A7');
        $sheet1->mergeCells('B5:B7');
        $sheet1->mergeCells('G5:G7');
        $sheet1->mergeCells('L5:L7');
        $sheet1->mergeCells('Q5:Q7');
        $sheet1->mergeCells('R5:R7');
        $sheet1->mergeCells('S5:S7');

        $sheet1->mergeCells('T5:U6');
        $sheet1->mergeCells('V5:Y6');

        // Set label untuk merged cells
        $sheet1->setCellValue('A5', 'No');
        $sheet1->setCellValue('B5', 'Nama');
        $sheet1->setCellValue('C5', 'ZIADAH');
        $sheet1->setCellValue('G5', 'JML(POJOK)');
        $sheet1->setCellValue('H5', 'DERESAN A');
        $sheet1->setCellValue('L5', 'JML(POJOK)');
        $sheet1->setCellValue('M5', 'DERESAN B');
        $sheet1->setCellValue('Q5', 'JML(POJOK)');
        $sheet1->setCellValue('R5', 'TOTAL DERESAN(POJOK)');
        $sheet1->setCellValue('S5', 'LEVEL DERESAN');
        $sheet1->setCellValue('T5', 'BIN NADHOR');
        $sheet1->setCellValue('V5', 'KEHADIRAN');
        
        // Set label untuk merged cells di baris kedua
        $sheet1->setCellValue('C6', 'AWAL');
        $sheet1->setCellValue('E6', 'AKHIR');
        $sheet1->setCellValue('H6', 'AWAL');
        $sheet1->setCellValue('J6', 'AKHIR');
        $sheet1->setCellValue('M6', 'AWAL');
        $sheet1->setCellValue('O6', 'AKHIR');

        $sheet1->setCellValue('T7', 'JUZ');
        $sheet1->setCellValue('U7', 'PJ');

        $sheet1->setCellValue('V7', 'TS');
        $sheet1->setCellValue('W7', 'I');
        $sheet1->setCellValue('X7', 'A');
        $sheet1->setCellValue('Y7', 'P');
        
        $sheet1->setCellValue('C7', 'JUZ');
        $sheet1->setCellValue('D7', 'PJ');

        $sheet1->setCellValue('E7', 'JUZ');
        $sheet1->setCellValue('F7', 'PJ');

        $sheet1->setCellValue('H7', 'JUZ');
        $sheet1->setCellValue('I7', 'PJ');

        $sheet1->setCellValue('J7', 'JUZ');
        $sheet1->setCellValue('K7', 'PJ');

        $sheet1->setCellValue('M7', 'JUZ');
        $sheet1->setCellValue('N7', 'PJ');

        $sheet1->setCellValue('O7', 'JUZ');
        $sheet1->setCellValue('P7', 'PJ');
        
        // Set style untuk font dan alignment
        $sheet1->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet1->getStyle('A3:H3')->getFont()->setBold(true);
        $sheet1->getStyle('T3:Y3')->getFont()->setBold(true);

        $sheet1->getStyle('A1:H3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet1->getStyle('A1:H3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet1->getStyle('T3:Y3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet1->getStyle('A5:Y5')->getFont()->setBold(true);
        $sheet1->getStyle('A6:Y6')->getFont()->setBold(true);
        $sheet1->getStyle('A7:Y7')->getFont()->setBold(true);
        $sheet1->getStyle('A5:Y7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet1->getStyle('A5:Y7')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Menambahkan data ke baris berikutnya (baris ke-4 setelah 3 baris kosong)
        $row = 8;  // Mulai dari baris 8, misalnya, untuk menempatkan data pertama

        // Menambahkan data ke spreadsheet
        foreach ($result as $rowData) {
            $sheet1->fromArray($rowData, null, 'A' . $row++);
        }

        $sheet1->setCellValue('A' . ($row + 5), 'Catatan*** : ')
        ->getStyle('A' . ($row + 5))->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet1->mergeCells('A' . ($row + 5) . ':Y' . ($row + 10));

        $sheet1->getStyle('A5:Y' . $row)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN, // Garis tipis
                    'color' => ['rgb' => '000000'], // Warna hitam
                ],
            ],
        ]);


        if($idRole == 1)
        {

            // SHEET 2

            $newSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, "Pantauan Hafalan");
            $sheet2 = $spreadsheet->addSheet($newSheet);
            $spreadsheet->setActiveSheetIndex(1);

            $sheet2->mergeCells('A1:J1')
            ->setCellValue('A1', 'PANTAUAN PERKEMBANGAN HAFALAN SANTRI | ' . $textTglAwal . ' - ' . $textTglAkhir)
            ->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 12
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Header tabel
            $headers = ['Keterangan Ziyadah Santri', 'Kelas VII', 'Kelas VIII', 'Kelas IX', 'Kelas X', 'Kelas XI', 'Kelas XII', 'Jumlah'];
            $sheet2->fromArray([$headers], NULL, 'A4');

            // Query Data dari Database
            $targetZiyadah = Ziyadah::select(
                'master_tingkatan.tingkatan',
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

            // Mengonversi data menjadi array
            $dataArray = $targetZiyadah->mapWithKeys(function ($item) {
                return [
                    $item->tingkatan => [
                        'totalSantri' => $item->totalTarget + $item->totalKhatam + $item->totalTidakTarget,
                        'totalTarget' => $item->totalTarget,
                        'totalKhatam' => $item->totalKhatam,
                        'totalTidakTarget' => $item->totalTidakTarget,
                    ]
                ];
            })->toArray();

            // Inisialisasi Data untuk Spreadsheet
            $data = [
                ['Target'],
                ['Tidak Target'],
                ['Khatam'],
                ['Total Santri']
            ];

            $totalTarget = $totalTidakTarget = $totalKhatam = $totalSantri = 0;

            // Isi Data dari Tingkatan 7 sampai 12
            for ($i = 7; $i <= 12; $i++) {
                $data[0][] = $dataArray[$i]['totalTarget'] ?? 0;
                $totalTarget += $dataArray[$i]['totalTarget'] ?? 0;

                $data[1][] = $dataArray[$i]['totalTidakTarget'] ?? 0;
                $totalTidakTarget += $dataArray[$i]['totalTidakTarget'] ?? 0;

                $data[2][] = $dataArray[$i]['totalKhatam'] ?? 0;
                $totalKhatam += $dataArray[$i]['totalKhatam'] ?? 0;

                $data[3][] = $dataArray[$i]['totalSantri'] ?? 0;
                $totalSantri += $dataArray[$i]['totalSantri'] ?? 0;
            }

            // Tambahkan kolom jumlah di akhir setiap baris
            $data[0][] = $totalTarget;
            $data[1][] = $totalTidakTarget;
            $data[2][] = $totalKhatam;
            $data[3][] = $totalSantri;

            $row = 5;

            // Menambahkan data ke spreadsheet
            foreach ($data as $rowData) {
                $sheet2->fromArray($rowData, null, 'A' . $row++);
            }

            $ranges = ['A4:H4', 'A18:H18'];

            foreach ($ranges as $range) {
                $sheet2->getStyle($range)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F0A04B'],
                    ],
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
            }
            
            $sheet2->getStyle('A5:A8')->getFont()->setBold(true);

            $ranges = ['A8:H8', 'A22:H22'];
            foreach ($ranges as $range) {
                $sheet2->getStyle($range)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'EEEEEE'],
                    ],
                ]);
            }

            $sheet2->mergeCells('A3:G3')
            ->setCellValue('A3', 'Target Ziyadah (Pojokan) yaitu 7 pojok (Kelas 7) dan 11 Pojok (Kelas 8-12)')
            ->getStyle('A3')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 12
                ]
            ]);

            $sheet2->mergeCells('A17:G17')
            ->setCellValue('A17', 'Target Deresan yaitu 30 pojok (Kelas 7) dan 80 Pojok (Kelas 8-12)')
            ->getStyle('A17')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 12
                ]
            ]);
            
            
            $sheet2->getStyle('A8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet2->getStyle('A8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $sheet2->getStyle('B5:H8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet2->getStyle('B5:H8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $ranges = ['A4:H8', 'A18:H22']; // Tambahkan range lain jika perlu

            foreach ($ranges as $range) {
                $sheet2->getStyle($range)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN, // Garis tipis
                            'color' => ['rgb' => '000000'], // Warna hitam
                        ],
                    ],
                ]);
            }

            $deresanGraph = DB::query()->fromSub(function ($query) use ($idWaktus, $idUser, $idRole) {
                $query->select(
                        'master_tingkatan.tingkatan',
                        DB::raw('COUNT(DISTINCT deresan_a.id_santri) AS totalSantri'),
                        DB::raw('COUNT(DISTINCT CASE WHEN deresan_a.jumlah >= master_target.jumlah THEN deresan_a.id_santri END) AS totalTarget'),
                        DB::raw('COUNT(DISTINCT CASE WHEN deresan_a.jumlah IS NULL THEN deresan_a.id_santri END) AS totalTidakTertulis'),
                        DB::raw('COUNT(DISTINCT CASE WHEN deresan_a.jumlah < master_target.jumlah THEN deresan_a.id_santri END) AS totalTidakTarget')
                    )
                    ->from('deresan_a')
                    ->leftJoin('santri', 'santri.id', '=', 'deresan_a.id_santri')
                    ->leftJoin('master_kelas', 'master_kelas.id', '=', 'santri.id_kelas')
                    ->leftJoin('master_tingkatan', 'master_tingkatan.id', '=', 'master_kelas.id_tingkatan')
                    ->leftJoin('master_target', DB::raw('FIND_IN_SET(master_tingkatan.id, master_target.id_tingkatan)'), '>', DB::raw('0'))
                    ->whereIn('id_waktu', $idWaktus)
                    ->when($idRole == 2, function ($query) use ($idUser) {
                        return $query->where('id_ustad', $idUser);
                    })
                    ->whereNotNull('santri.id_kelas')
                    ->where('master_target.nama', 'deresan')
                    ->whereNotIn('santri.id_kelas', ['boyong', 25])
                    ->groupBy('master_tingkatan.tingkatan')
            
                    ->unionAll(
                        DB::table('murojaah')
                            ->select(
                                'master_tingkatan.tingkatan',
                                DB::raw('COUNT(DISTINCT murojaah.id_santri) AS totalSantri'),
                                DB::raw('COUNT(DISTINCT CASE WHEN murojaah.jumlah >= master_target.jumlah THEN murojaah.id_santri END) AS totalTarget'),
                                DB::raw('COUNT(DISTINCT CASE WHEN murojaah.jumlah IS NULL THEN murojaah.id_santri END) AS totalTidakTertulis'),
                                DB::raw('COUNT(DISTINCT CASE WHEN murojaah.jumlah < master_target.jumlah THEN murojaah.id_santri END) AS totalTidakTarget')
                            )
                            ->leftJoin('santri', 'santri.id', '=', 'murojaah.id_santri')
                            ->leftJoin('master_kelas', 'master_kelas.id', '=', 'santri.id_kelas')
                            ->leftJoin('master_tingkatan', 'master_tingkatan.id', '=', 'master_kelas.id_tingkatan')
                            ->leftJoin('master_target', DB::raw('FIND_IN_SET(master_tingkatan.id, master_target.id_tingkatan)'), '>', DB::raw('0'))
                            ->whereIn('id_waktu', $idWaktus)
                            ->when($idRole == 2, function ($query) use ($idUser) {
                                return $query->where('id_ustad', $idUser);
                            })
                            ->whereNotNull('santri.id_kelas')
                            ->where('master_target.nama', 'deresan')
                            ->whereNotIn('santri.id_kelas', ['boyong', 25])
                            ->groupBy('master_tingkatan.tingkatan')
                    );
            }, 'combined_data')
            ->groupBy('tingkatan')
            ->select(
                'tingkatan',
                DB::raw('SUM(totalSantri) AS totalSantri'),
                DB::raw('SUM(totalTarget) AS totalTarget'),
                DB::raw('SUM(totalTidakTertulis) AS totalTidakTertulis'),
                DB::raw('SUM(totalTidakTarget) AS totalTidakTarget')
            )
            ->get();
    
            $dataDeresan = $deresanGraph->mapWithKeys(function ($item) {
                return [
                    $item->tingkatan => [
                        'totalSantri' => $item->totalTarget + $item->totalTidakTertulis + $item->totalTidakTarget,
                        'totalTarget' => $item->totalTarget,
                        'totalTidakTertulis' => $item->totalTidakTertulis,
                        'totalTidakTarget' => $item->totalTidakTarget,
                    ]
                ];
            });

            // Header tabel
            $headersDeresan = ['Keterangan', 'Kelas VII', 'Kelas VIII', 'Kelas IX', 'Kelas X', 'Kelas XI', 'Kelas XII', 'Jumlah'];
            $sheet2->fromArray([$headersDeresan], NULL, 'A18');

            // Inisialisasi Data untuk Spreadsheet
            $dataSide = [
                ['Target'],
                ['Tidak Target'],
                ['Belum Terisi'],
                ['Total Santri']
            ];

            $totalTarget = $totalTidakTarget = $totalTidakTertulis = $totalSantri = 0;

            // Isi Data dari Tingkatan 7 sampai 12
            for ($i = 7; $i <= 12; $i++) 
            {
                $dataSide[0][] = $dataDeresan[$i]['totalTarget'] ?? 0;
                $totalTarget += $dataDeresan[$i]['totalTarget'] ?? 0;

                $dataSide[1][] = $dataDeresan[$i]['totalTidakTarget'] ?? 0;
                $totalTidakTarget += $dataDeresan[$i]['totalTidakTarget'] ?? 0;

                $dataSide[2][] = $dataDeresan[$i]['totalTidakTertulis'] ?? 0;
                $totalTidakTertulis += $dataDeresan[$i]['totalTidakTertulis'] ?? 0;

                $dataSide[3][] = $dataDeresan[$i]['totalSantri'] ?? 0;
                $totalSantri += $dataDeresan[$i]['totalSantri'] ?? 0;
            }

            // Tambahkan kolom jumlah di akhir setiap baris
            $dataSide[0][] = $totalTarget;
            $dataSide[1][] = $totalTidakTarget;
            $dataSide[2][] = $totalTidakTertulis;
            $dataSide[3][] = $totalSantri;

            $row = 19;

            // Menambahkan data ke spreadsheet
            foreach ($dataSide as $rowData) {
                $sheet2->fromArray($rowData, null, 'A' . $row++);
            }
        }

        // Menulis file Excel langsung ke output untuk diunduh
        $writer = new Xlsx($spreadsheet);
        $filename = 'Blangko Rekapan Santri ' . $namaUser . '.xlsx';

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