<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;

use App\Models\Santri;
use App\Models\MasterKetahfidzan;
use App\Models\MasterKuotaTahfidzan;

use Illuminate\Http\Request;

class UstadTahfidzController extends Controller
{
    public function index()
    {
        $data  = [
            'url' => 'ketahfidzan/ustad-tahfidz',
            'title'  => 'Ustad Tahfidz',
            'pageHeading'   => 'Ustad Tahfidz',
        ];

        return view('admin.ustadz-tahfidz.index', $data);
    }

    public function getData()
    {
        $data = MasterKetahfidzan::select('master_ketahfidzan.*', 'users.nama as namaUstad')
            ->leftJoin('users', 'users.id', '=', 'master_ketahfidzan.id_ustad')
            ->get();

        // Filter data untuk hanya mengambil yang namaUstad tidak kosong
        $filteredData = $data->filter(function ($row) {
            return !empty($row->namaUstad);
        });

        // Mengelompokkan data berdasarkan id_ustad dan menghitung jumlah id_santri unik
        $jmlSantriPerUstad = $filteredData->groupBy('id_ustad')->map(function ($group) {
            return $group->unique('id_santri')->count();
        });

        // Mengambil data unik berdasarkan id_ustad
        $uniqueDataPerUstad = $filteredData->unique('id_ustad');

        // Transformasi data akhir
        $transformedData = $uniqueDataPerUstad->map(function ($row, $index) use ($jmlSantriPerUstad) {
            $rowUstad = '<a href="' . route('ketahfidzan.ustad-tahfidz.detail', ['id' => $row->id_ustad]) . '">' . $row->namaUstad . '</a>';

            $jmlSantri = $jmlSantriPerUstad[$row->id_ustad] ?? 0;

            return [
                $index + 1,
                $rowUstad,
                $jmlSantri,
            ];
        });

        return response()->json(['data' => $transformedData]);
    }

    public function detail($id)
    {
        $find = MasterKetahfidzan::select('master_ketahfidzan.*', 'users.nama as namaUstad', 'santri.nama as namaSantri')
                    ->leftJoin('santri', 'santri.id', '=', 'master_ketahfidzan.id_santri')
                    ->leftJoin('users', 'users.id', '=', 'master_ketahfidzan.id_ustad')
                    ->where('id_ustad', $id)
                    ->get();

        // Mengecek jika data tidak ada
        if ($find->isEmpty()) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Mengambil hanya satu data pertama
        $firstData = $find->first();
        $santri = Santri::whereNotIn('id', function ($query) {
            $query->select('id_santri')
                  ->from('master_ketahfidzan');
        })->get();

        $data = [
            'namaUstad' => $firstData->namaUstad,
            'idUstad' => $firstData->id_ustad,
            'data'  => $find,
            'santri'  => $santri,
            'url' => 'ketahfidzan/ustad-tahfidz',
            'title'  => $firstData->namaUstad . 'Page',
            'pageHeading'   => $firstData->namaUstad . ' | Ketahfidzan',
        ];

        return view('admin.ustadz-tahfidz.detail', $data);
    }

    public function storeKetahfidzan(Request $request)
    {
        $idSantri = $request->idSantri;
        $idUstad = $request->idUstad;

        if(!$idSantri || !$idUstad){
            return response()->json(['message' => 'Data tidak ditemukan'], 422);    
        }

        $kuotaUstad = MasterKuotaTahfidzan::where('id_ustad', $idUstad)->first();
        
        if($kuotaUstad->kuota <= 0){
            return response()->json(['message' => 'Kuota penuh!'],  422);
        }

        MasterKetahfidzan::create([
            'id_ustad' => $idUstad,
            'id_santri' => $idSantri,
        ]);

        $jmlKuota = MasterKuotaTahfidzan::where('id_ustad', $idUstad)->first();
        $sisaKuota = $jmlKuota->kuota - 1;
        $jmlKuota->update(['kuota' => $sisaKuota]);

        return response()->json(['message' => 'Berhasil menambah santri ke tahfidzan'], 200);
    }

    public function delete(Request $request, $id)
    {
        $idKetahfidzan = $id;
        $idUstad = $request->idUstad;

        // Cari data berdasarkan id_ustad dan id_santri
        $data = MasterKetahfidzan::where('id', $idKetahfidzan)
            ->first();

        // Periksa apakah data ditemukan
        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404); // Status 404 untuk data tidak ditemukan
        }

        // Hapus data dan periksa hasil
        if ($data->delete()) {

            $jmlKuota = MasterKuotaTahfidzan::where('id_ustad', $idUstad)->first();
            $sisaKuota = $jmlKuota->kuota + 1;
            $jmlKuota->update(['kuota' => $sisaKuota]);

            return response()->json(['message' => 'Berhasil menghapus Santri dari Tahfidzan'], 200);
        } else {
            return response()->json(['message' => 'Gagal menghapus Santri dari Tahfidzan'], 500); // Status 500 untuk kesalahan server
        }
    }

}
