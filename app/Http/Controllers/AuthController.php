<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(){
        $data = [
            'title' => 'Login'
        ];

        return view('auth.login', $data);
    }

    public function loginProses(Request $request){
        $validatedData = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $validatedData['username'];
        $password = $validatedData['password'];

        $user = User::where('username', $username)->first();

        if(empty($user)){
            return redirect()->back()->with('error', 'username tidak ditemukan');
        }

        if (!Hash::check($password, $user->password)) {
            return redirect()->back()->with('error', 'password atau username salah');
        }

        session([
            'idUser'    => $user->id,
            'nama'      => $user->nama,
            'idRole'    => $user->role,
            'namaUser'  => $user->nama,
            'username'  => $user->username,
        ]);

        return redirect()->route('dashboard.')->with('success', 'Berhasil Login');
    }

    public function logout(){
        session()->flush();
        return redirect()->route('login')->with('success', 'Berhasil Keluar');
    }
}