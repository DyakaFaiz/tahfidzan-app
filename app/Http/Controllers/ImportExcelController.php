<?php

namespace App\Http\Controllers;

use App\Models\MasterKetahfidzan;
use App\Models\MasterKuotaTahfidzan;
use App\Models\Santri;
use App\Models\Tahfidzan;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Database\QueryException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;

class ImportExcelController extends Controller
{
    public function index(){
        return view('import-excel');
    }
    public function importSantri(Request $request)
    {
        $request->validate([
            'excel' => 'required|mimes:xlsx,xls',
        ]);

        // Menangkap file yang diupload
        $file = $request->file('excel');
        
        // Membaca file Excel
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getSheet(1); // Sheet pertama (index dimulai dari 0)
        $rows = $sheet->toArray();

        // Looping untuk memasukkan data ke database
        foreach ($rows as $row) {
            // Pastikan baris tidak kosong
            if (!empty($row[1])) {
                // Menyimpan data ke dalam tabel menggunakan Query Builder atau Eloquent
                Santri::create([
                    'id' => $row[0],
                    'nama' => $row[1],
                ]);
            }
        }

        return back()->with('success', 'Data berhasil diimport!');
    }

    public function importUstad(Request $request)
    {
        $request->validate([
            'excel' => 'required|mimes:xlsx,xls',
        ]);

        // Menangkap file yang diupload
        $file = $request->file('excel');
        
        // Membaca file Excel
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getSheet(0); // Sheet pertama (index dimulai dari 0)
        $rows = $sheet->toArray();

        // Looping untuk memasukkan data ke database
        foreach ($rows as $row) {
            // Pastikan baris tidak kosong
            if (!empty($row[1])) {
                $namOri = $row[1];
                $name = $row[1];

                // Menggunakan preg_replace untuk menghilangkan "Ust." di awal nama
                $name = preg_replace("/^Ust\. /", "", $name);  

                // Menghapus gelar setelah koma dan karakter lainnya seperti titik, koma, tanda kutip, dsb
                $name = preg_replace("/,.*$/", "", $name);  // Menghapus gelar setelah koma

                // Menghilangkan tanda baca seperti titik, koma, tanda kutip, dsb
                $name = preg_replace("/[.,'\"-]/", "", $name);

                // Mengubah nama menjadi huruf kecil semua
                $name = strtolower($name);

                // Menghilangkan semua spasi
                $name = str_replace(' ', '', $name);

                // Menyimpan data ke dalam tabel menggunakan Query Builder atau Eloquent
                User::create([
                    'id' => $row[0],
                    'nama' => $namOri,
                    'username' => $name,
                ]);
            }
        }

        return back()->with('success', 'Data berhasil diimport!');
    }

    public function kuotaKetahfidzan(Request $request)
    {
        try {
            // Ambil file excel dari request
            $file = $request->file('excel');
            
            // Periksa apakah file ada dan merupakan file Excel
            if (!$file || !$file->isValid()) {
                throw new Exception('File tidak valid atau tidak ada');
            }

            // Membaca file Excel menggunakan IOFactory
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getSheet(1); // Sheet pertama (index dimulai dari 0)
            $rows = $sheet->toArray();

            foreach ($rows as $row) {
                // Pastikan ada id_ustad dan id_santri yang valid
                if (!empty($row[7]) && !empty($row[0])) {
                    // Mengumpulkan id_ustad yang unik
                    $uniqueUstads = collect($rows)->pluck(7)->unique(); // Ambil id_ustad yang unik
            
                    foreach ($uniqueUstads as $idUstad) {
                        // Menghitung jumlah data berdasarkan id_ustad
                        $data = MasterKetahfidzan::where('id_ustad', $idUstad)->count();
            
                        // Menghitung kuota dengan cara menghitung data yang sudah ada
                        $kuota = 12 - $data;
            
                        // Cek apakah kuota lebih dari atau sama dengan 0 sebelum menyimpan data
                        if ($kuota >= 0) {
                            MasterKuotaTahfidzan::create([
                                'id_ustad' => $idUstad, // Ambil id_ustad dari row yang unik
                                'kuota' => $kuota, // Kuota yang tersisa
                            ]);
                        } else {
                            // Jika kuota tidak mencukupi, bisa diberikan pesan error atau log
                            // Misalnya, Anda bisa mengabaikan atau mencatat masalah ini
                            // Log::warning("Kuota untuk ustad {$idUstad} sudah penuh");
                        }
                    }
                }
            }

            // Berikan response jika proses selesai
            return back()->with('success', 'Data berhasil diimport!');
        } catch (QueryException $e) {
            dd(($e->getMessage()));
        } catch (Exception $e) {
            // Menangani kesalahan umum (seperti file tidak valid atau kuota penuh)
            // return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            dd(($e->getMessage()));
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            // Menangani kesalahan saat membaca file Excel
            // return back()->with('error', 'Terjadi kesalahan saat membaca file Excel: ' . $e->getMessage());

            dd(($e->getMessage()));            
        } catch (\Throwable $e) {
            // Menangani kesalahan tak terduga lainnya
            // return back()->with('error', 'Terjadi kesalahan tak terduga: ' . $e->getMessage());
            dd(($e->getMessage()));
        }
    }

    public function ketahfidzan(Request $request)
    {

        $file = $request->file('excel');
        
        // Membaca file Excel
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getSheet(1); // Sheet pertama (index dimulai dari 0)
        $rows = $sheet->toArray();

        foreach ($rows as $row) {
            if(!empty($row[7]) && !empty($row[0])){
                MasterKetahfidzan::create([
                    'id_ustad' => $row[7],
                    'id_santri' => $row[0],
                ]);
            }
        }

        return back()->with('success', 'Data berhasil diimport!');
    }
}