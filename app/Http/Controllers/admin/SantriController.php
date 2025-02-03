<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MasterKelas;
use App\Models\Santri;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SantriController extends Controller
{
    public function index()
    {
        $mKelas = MasterKelas::all();

        $data = [
            'title'  => 'Santri',
            'pageHeading'   => 'Santri Management',
            'url'   => 'santri',
            'masterKelas'   => $mKelas,
        ];

        return view('admin.santri.index', $data);
    }

    public function getData()
    {
        $data = Santri::select('santri.*', 'master_kelas.kelas as kelasSantri')
                ->leftJoin('master_kelas', 'master_kelas.id', '=', 'santri.id_kelas')
                ->orderBy('santri.nama', 'asc')
                ->get()
                ;

        $transformedData = $data->map(function ($row, $index) {
            $aksi = "
                <button class='btn icon btn-primary' id='btn-edit' data-id='{$row->id}' data-bs-toggle='modal' data-bs-target='#modal-edit'>
                    <i class='bi bi-pencil'></i>
                </button>";
            $status = $row->status == 0 ? 'BOYONG' : ($row->status == 1 ? 'MASIH ZIYADAH' : ($row->status == 2 ? 'Khatam' : 'Khotimin'));
            return [
                $index + 1,
                $row->nama,
                $row->kelasSantri,
                $status,
                $aksi,
            ];
        });

        return response()->json(['data' => $transformedData]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string',
            'kelas'     => 'required|string',
        ]);
        
        $data = [
            'nama'  => $request->nama,
            'id_kelas'  => $request->kelas,
            'status'  => 1,
        ];

        $save = Santri::create($data);

        if($save){
            return redirect()->back()->with('success', 'Behasil menambah data');
        }else{
            return redirect()->back()->with('error', 'Gagal menambah data');
        }
    }

    public function edit($id = NULL)
    {
        $data = Santri::where('id', $id)->first();

        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'kelas'     => 'required|string',
            'status'     => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' =>  $validator->errors()->all()], 400);
        }

        $user = Santri::findOrFail($id);

        $user->update([
            'nama' => $request->nama,
            'id_kelas'  => $request->kelas,
            'status'  => $request->status,
        ]);

        return response()->json(['message' => 'Berhasil merubah data']);
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        try {
            $data = Santri::findOrFail($id);

            $data->delete();

            return response()->json(['message' => 'Berhasil menghapus data'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => $e], 500);
        }
    }
}
