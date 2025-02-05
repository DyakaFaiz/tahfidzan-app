<?php

namespace App\Http\Controllers\admin;

use App\Models\User;

use App\Models\Santri;
use Illuminate\Http\Request;

use App\Models\MasterKetahfidzan;

use App\Http\Controllers\Controller;
use App\Models\MasterKuotaTahfidzan;

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
        $data = User::select(
            'users.id', 
            'users.nama', // Jika ingin menampilkan nama user juga
            \DB::raw('COUNT(master_ketahfidzan.id_ustad) as totalSantri')
        )
        ->leftJoin('master_ketahfidzan', 'master_ketahfidzan.id_ustad', '=', 'users.id')
        ->where('users.id', '!=', 0)
        ->orderBy('users.nama', 'asc')
        ->groupBy('users.id', 'users.nama') // Pastikan mencantumkan semua kolom yang di-select
        ->get();

        // Transformasi data akhir
        $transformedData = $data->values()->map(function ($row, $index) {
            $rowUstad = '<a href="' . route('ketahfidzan.ustad-tahfidz.detail', ['id' => $row->id]) . '">' . $row->nama . '</a>';
        
            $jmlSantri = $row->totalSantri;

            return [
                $index + 1, // Indeks akan berurutan mulai dari 1
                $rowUstad,
                $jmlSantri,
            ];
        });

        return response()->json(['data' => $transformedData]);
    }

    public function detail($id)
    {
        $find = MasterKetahfidzan::select('master_ketahfidzan.*', 'users.nama as namaUstad', 'santri.nama as namaSantri', 'santri.status AS statusSantri', 'master_kelas.kelas AS kelasSantri')
                    ->leftJoin('santri', 'santri.id', '=', 'master_ketahfidzan.id_santri')
                    ->leftJoin('master_kelas', 'master_kelas.id', '=', 'santri.id_kelas')
                    ->leftJoin('users', 'users.id', '=', 'master_ketahfidzan.id_ustad')
                    ->where('id_ustad', $id)
                    ->get();

        $user = User::where('id', $id)->first();

        $santri = Santri::whereNotIn('id', function ($query) {
            $query->select('id_santri')
                  ->from('master_ketahfidzan');
        })
        ->where('status', 1)
        ->get();

        $data = [
            'namaUstad' => $user->nama,
            'idUstad' => $id,
            'data'  => $find,
            'santri'  => $santri,
            'url' => 'ketahfidzan/ustad-tahfidz',
            'title'  => $user->nama . 'Page',
            'pageHeading'   => $user->nama . ' | Ketahfidzan',
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

        $data = MasterKetahfidzan::where('id', $idKetahfidzan)
            ->first();

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
