<?php

namespace App\Http\Controllers\admin;

use App\Models\User;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\MasterKuotaTahfidzan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class UserController extends Controller
{
    public function index()
    {
        $users = User::select('users.*', 'master_role.nama as role')
                ->leftJoin('master_role', 'master_role.id', 'users.role')
                ->orderBy('username', 'asc')
                ->get()
                ;              

        $data = [
            'title'  => 'User',
            'pageHeading'   => 'User Management',
            'url'   => 'user',
            'users' => $users,
        ];

        return view('admin.user.index', $data);
    }

    public function getData()
    {
        $data = User::select('users.*', 'master_role.nama as role')
            ->leftJoin('master_role', 'master_role.id', '=', 'users.role')
            ->orderBy('username', 'asc')
            ->get();

        // Transform data menjadi array
        $transformedData = $data->map(function ($row, $index) {
            $aksi = "
                <button class='btn icon btn-primary' id='btn-edit' data-id='{$row->id}' data-bs-toggle='modal' data-bs-target='#modal-edit'>
                    <i class='bi bi-pencil'></i>
                </button>";

            if (!$row->password) {
                $aksi .= "
                    <button class='btn icon btn-danger' id='btn-input-password' data-id='{$row->id}'>
                        <i class='bi bi-key'></i>
                    </button>";
            }

            return [
                $index + 1,
                $row->nama,
                $row->username,
                $row->role,
                $aksi,
            ];
        });

        return response()->json(['data' => $transformedData]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'nama'     => 'required|string',
            'password'  =>  'required',
        ]);

        $user = User::where('username', $request->username);
        if($user->exists()){
            return redirect()->back()->withInput()->with('error', 'Username sudah dipakai');
        }
        
        $data = [
            'username' => $request->username,
            'nama'  => $request->nama,
            'password'  => Hash::make($request->password),
        ];

        $save = User::create($data);

        $newUser = User::latest()->first();
        $save = MasterKuotaTahfidzan::create([
            'id_ustad' => $newUser->id,
            'kuota' => 12
        ]);

        if($save){
            return redirect()->back()->with('success', 'Behasil menambah data');
        }else{
            return redirect()->back()->with('error', 'Gagal menambah data');
        }
    }

    public function edit($id = NULL)
    {
        $data = User::where('id', $id)->first();

        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'nama' => 'required|string|max:255',
            'password' => 'nullable|string|min:4',
        ]);

        if ($validator->fails()) {
            response()->json(['message' => $validator, 500]);
        }

        $user = User::findOrFail($id);

        $data = [
            'username' => $request->username,
            'nama' => $request->nama,
        ];

        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json(['message' => 'Berhasil merubah data']);
    }

    public function storePassword(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:users,id',
            'password' => 'required|string',
        ]);
        
        $id = $request->id;
        $password = Hash::make($request->password);

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found!'], 404);
        }

        $user->update([
            'password' => $password
        ]);

        return response()->json(['message' => 'Password updated successfully!']);
    }

    public function delete(Request $request)
    {
        $id = $request->id;

        if($id == 0){
            return response()->json(['message' => 'User ini tidak boleh dihapus!!'], 500);
        }

        try {
            $data = User::findOrFail($id);
            $data2 = MasterKuotaTahfidzan::where('id_ustad', $id);

            if ($data) {
                $data->delete();
            }
        
            if ($data2) {
                $data2->delete();
            }

            return response()->json(['message' => 'Berhasil menghapus data'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => $e], 500);
        }
    }
}
