@extends('layout.main')

@section('custom-header-css')
<link rel="stylesheet" href="{{ url('') }}/assets/vendors/simple-datatables/style.css">
<style>
    .table-wrapper {
        max-height: 78vh;
        overflow-y: auto;
        position: relative;
    }
    #tabel-blangko, #tabel-kondisi-halaqoh {
        width: 100%;
        table-layout: auto;
    }

    #tabel-blangko th,
    #tabel-blangko td,
    #tabel-kondisi-halaqoh th,
    #tabel-kondisi-halaqoh td {
        padding: 8px;
        text-align: center;
    }

    .sticky-row, .sticky-2, .sticky-3, .sticky-no, .sticky-nama {
        position: sticky;
        background: #f8f9fa;
        z-index: 2;
    }

    .list-group-container {
        max-height: 300px; /* Sesuaikan tinggi maksimal sesuai kebutuhan */
        overflow-y: auto;
        overflow-x: hidden;
        border-radius: 5px;
    }


    input[type="date"]:invalid {
        background-color: #f0f0f0;
        color: #aaa;
    }
    @media (max-width: 767px) {
        /* Mengurangi ukuran font tabel untuk layar kecil */
        table {
            font-size: 12px;
        }
        /* Jika perlu, Anda bisa mengurangi padding sel juga */
        table td, table th {
            padding: 0.5rem;
        }

        .list-group-container ul {
            display: block;
            white-space: nowrap;
            overflow-x: auto; /* Aktifkan scroll horizontal untuk tabel panjang */
            padding-bottom: 10px; /* Beri ruang agar scroll terlihat */
        }

        .list-group-item {
            display: flex;
            flex-direction: column; /* Susun elemen dalam satu baris vertikal */
            align-items: flex-start;
            gap: 5px; /* Beri jarak antar elemen */
        }

        .list-group-item span {
            width: 100%; /* Biar tidak melebar */
            text-align: left;
        }

        .badge {
            font-size: 12px; /* Ukuran badge lebih kecil */
            padding: 5px 8px;
        }
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col">
        <div class="">
            @if (session('idRole') != 3)
                <div class="">
                    <div id="text-waktu-ziyadah" class="row d-none mb-3">
                        <div class="col-md-4 text-center">
                            <h3 id="text-tgl-awal-ziyadah"></h3>
                        </div>
                        <div class="col-md-4 text-center">
                            <h3 id="text-emote-ziyadah"></h3>
                        </div>
                        <div class="col-md-4 text-center">
                            <h3 id="text-tgl-akhir-ziyadah"></h3>
                        </div>
                    </div>
                    <div class="row d-flex align-items-center">
                        <div class="col-md-8 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Graph Ziyadah</h4>
                                </div>
                                <div class="card-body">
                                    <div id="graph-ziyadah"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-12 bg-white rounded">
                            <div class="p-3">
                                <h4>Chart Ziyadah Hafalan Santri</h4>
                            </div>
                            <div class="card-body">
                                <form id="form-diagram-ziyadah">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="tgl-awal">Tanggal Awal</label>
                                            <input type="date" class="form-control" name="tglAwal" id="tgl-awal" min="{{ $tglMin }}" max="{{ $tglMax }}">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="tgl-akhir">Tanggal Akhir</label>
                                            <input type="date" class="form-control" name="tglAkhir" id="tgl-akhir" min="{{ $tglMin }}" max="{{ $tglMax }}">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="bi bi-file-earmark-ruled-fill"></i>
                                    </button>
                                </form>
                                <div id="chart-ziyadah"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-8 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Graph Deresan</h4>
                                </div>
                                <div class="card-body">
                                    <div id="graph-deresan"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-12 d-flex align-items-center justify-content-center">
                            <div class="bg-white rounded p-5">
                                <div id="chart-deresan"></div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Blangko --}}
                    <form id="form-range-waktu" class="bg-white rounded p-3">
                        <div class="row">
                            <div class="py-2">
                                <h4>Blangko Santri</h4>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="tgl-awal">Tanggal Awal</label>
                                <input type="date" class="form-control" name="tglAwal" id="tgl-awal" min="{{ $tglMin }}" max="{{ $tglMax }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="tgl-akhir">Tanggal Akhir</label>
                                <input type="date" class="form-control" name="tglAkhir" id="tgl-akhir" min="{{ $tglMin }}" max="{{ $tglMax }}">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary float-end">
                            <i class="bi bi-file-earmark-ruled-fill"></i>
                        </button>
                        <div class="row mt-4">
                            <div class="col-md-4 text-center">
                                <h3 id="text-tgl-awal-blangko"></h3>
                            </div>
                            <div class="col-md-4 text-center">
                                <h3 id="text-emote-blangko"></h3>
                            </div>
                            <div class="col-md-4 text-center">
                                <h3 id="text-tgl-akhir-blangko"></h3>
                            </div>
                        </div>
                    </form>
                    <div class="col-12 bg-white rounded p-4 d-none" id="blangko-wrapper">
                        <div class="table-responsive table-wrapper">
                            <table class="table table-lg" id="tabel-blangko">
                                <thead>
                                    <tr class="sticky-row">
                                        <th rowspan="3">NO</th>
                                        <th rowspan="3">NAMA</th>
                                        <th class="text-center" colspan="4">ZIADAH</th>
                                        <th rowspan="3" style="width:12px">JML (Pojok)</th>
                                        
                                        <th class="text-center" colspan="4">DERESAN A (KBM SORE)</th>
                                        <th rowspan="3">JML (Pojok)</th>
            
                                        <th class="text-center" colspan="4">DERESAN B (KBM MALAM)</th>
                                        <th rowspan="3">JML (Pojok)</th>
                                        
                                        <th rowspan="3">TOTAL DERESAN (POJOK)</th>
                                        
                                        <th rowspan="3">LEVEL DERESAN</th>
            
                                        <th class="text-center" colspan="2" rowspan="2">BIN NADHOR</th>
            
                                        <th class="text-center" colspan="4" rowspan="2">KEHADIRAN</th>
                                    </tr>
                                    <tr class="sticky-2">
                                        <th colspan="2" class="text-center">Awal</th>
                                        <th colspan="2" class="text-center">Akhir</th>
            
                                        <th colspan="2" class="text-center">Awal</th>
                                        <th colspan="2" class="text-center">Akhir</th>
            
                                        <th colspan="2" class="text-center">Awal</th>
                                        <th colspan="2" class="text-center">Akhir</th>
                                    </tr>
                                    <tr class="sticky-3">
                                        <th>juz</th>
                                        <th>pj</th>
                                        <th>juz</th>
                                        <th>pj</th>
            
                                        <th>juz</th>
                                        <th>pj</th>
                                        <th>juz</th>
                                        <th>pj</th>
            
                                        <th>juz</th>
                                        <th>pj</th>
                                        <th>juz</th>
                                        <th>pj</th>
            
                                        <th>juz</th>
                                        <th>pj</th>
            
                                        <th>TS</th>
                                        <th>I</th>
                                        <th>A</th>
                                        <th>P</th>
                                    </tr>
                                </thead>
                                <tbody id="table-body">
                                    {{-- Simple DataTable --}}
                                </tbody>
                            </table>
                        </div>
                        <form action="{{ route('dashboard.export-blangko') }}" id="form-cetak-blangko" class="d-none" method="POST">
                            @csrf
                            <input type="hidden" name="tglAwalBlangko" id="tanggal-awal-blangko">
                            <input type="hidden" name="tglAkhirBlangko" id="tanggal-akhir-blangko">
                            <button type="submit" id="btn-cetak-blangko" class="btn btn-sm icon icon-left btn-success"><i class="bi bi-file-earmark-ruled-fill"></i> Cetak Blangko</button>
                        </form>
                    </div>
                    {{-- /Blangko --}}

                    {{-- Kondisi Halaqoh --}}
                    {{-- <form id="form-kondisi-halaqoh">
                        <div class="row">
                            <div class="py-2">
                                <h4>Kondisi Halaqoh</h4>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="tgl-awal">Tanggal Awal</label>
                                <input type="date" class="form-control" name="tglAwal" id="tgl-awal" min="{{ $tglMin }}" max="{{ $tglMax }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="tgl-akhir">Tanggal Akhir</label>
                                <input type="date" class="form-control" name="tglAkhir" id="tgl-akhir" min="{{ $tglMin }}" max="{{ $tglMax }}">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary float-end">
                            <i class="bi bi-file-earmark-ruled-fill"></i>
                        </button>
                        <div class="row mt-4">
                            <div class="col-md-4 text-center">
                                <h3 id="text-tgl-awal-kondisi-halaqoh"></h3>
                            </div>
                            <div class="col-md-4 text-center">
                                <h3 id="text-emote-kondisi-halaqoh"></h3>
                            </div>
                            <div class="col-md-4 text-center">
                                <h3 id="text-tgl-akhir-kondisi-halaqoh"></h3>
                            </div>
                        </div>
                    </form>
                    <div class="col-12 mt-3">
                        <div class="table-responsive table-wrapper">
                            <table class="table table-lg" id="tabel-kondisi-halaqoh">
                                <thead>
                                    <tr class="sticky-row">
                                        <th rowspan="3">NO</th>
                                        <th rowspan="3">HALAQOH</th>
                                        <th rowspan="3">KELAS</th>
                                        <th rowspan="3">JUMLAH SANTRI</th>
                                        <th rowspan="3">SANTRI BOYONG</th>
                                        <th rowspan="3">KHATAM</th>
                                        <th rowspan="3">KHOTIMIN</th>
            
                                        <th class="text-center" colspan="4">TARGET ZIADAH</th>
                                        <th rowspan="2" colspan="2">TARGET DERESAN</th> 
                                    </tr>
                                    <tr class="sticky-2">
                                        <th colspan="2" class="text-center">POJOKAN</th>
                                        <th colspan="2" class="text-center">LEMBAGA</th>
                                    </tr>
                                    <tr class="sticky-3">
                                        <th>Y</th>
                                        <th>N</th>

                                        <th>Y</th>
                                        <th>N</th>

                                        <th>Y</th>
                                        <th>N</th>
                                    </tr>
                                </thead>
                                <tbody id="table-body">
                                    {{-- Simple DataTable --}}
                                {{-- </tbody>
                            </table>
                        </div>
                    </div> --}}
                    {{-- /Kondisi Halaqoh --}}
                </div>
            @endif

            @if (session('idRole') == 3 && isset($dataSantri))
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body px-3 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="stats-icon blue">
                                                <i class="iconly-boldProfile"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Nama</h6>
                                            <h6 class="font-extrabold mb-0">{{ $dataSantri->nama }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body px-3 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="stats-icon green">
                                                <svg id="Home" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" class="text-light">
                                                    <path d="M9.07874 16.1354H14.8937" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M2.40002 13.713C2.40002 8.082 3.01402 8.475 6.31902 5.41C7.76502 4.246 10.015 2 11.958 2C13.9 2 16.195 4.235 17.654 5.41C20.959 8.475 21.572 8.082 21.572 13.713C21.572 22 19.613 22 11.986 22C4.35903 22 2.40002 22 2.40002 13.713Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Kelas</h6>
                                            <h6 class="font-extrabold mb-0">{{ $dataSantri->namaKelas }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body px-3 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="stats-icon red">
                                                <i class="iconly-boldBookmark"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Capaian Juz Sekarang</h6>
                                            <h6 class="font-extrabold mb-0">{{ $dataSantri->juzAkhir }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-3 col-md-4">
                            <div class="card">
                                <div class="card-body px-3 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="stats-icon red">
                                                <svg id="Edit" width="24px" height="24px" viewBox="0 0 24 24" fill="currentColor" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="text-white">
                                                    <title>Iconly/Bold/Edit</title>
                                                    <g id="Iconly/Bold/Edit" stroke="currentColor" stroke-width="1.5" fill="none" fill-rule="evenodd">
                                                        <g id="Edit" transform="translate(3.000000, 3.000000)" fill="#FFFFFF" fill-rule="nonzero">
                                                            <path d="M16.989778,15.9532516 C17.5468857,15.9532516 18,16.412265 18,16.9766258 C18,17.5420615 17.5468857,18 16.989778,18 L16.989778,18 L11.2796888,18 C10.7225811,18 10.2694668,17.5420615 10.2694668,16.9766258 C10.2694668,16.412265 10.7225811,15.9532516 11.2796888,15.9532516 L11.2796888,15.9532516 Z M13.0298561,0.699063657 L14.5048652,1.87078412 C15.109725,2.34377219 15.5129649,2.96725648 15.6509154,3.62298994 C15.810089,4.34429676 15.6403038,5.0527039 15.1627829,5.66543845 L6.37639783,17.027902 C5.97315794,17.543889 5.37890967,17.8341317 4.74221511,17.8448814 L1.24039498,17.8878803 C1.04938661,17.8878803 0.890212963,17.7588836 0.847766658,17.5761382 L0.051898447,14.1254752 C-0.086052043,13.4912412 0.051898447,12.8355077 0.455138341,12.3302704 L6.68413354,4.26797368 C6.7902493,4.13897694 6.98125767,4.11855245 7.10859659,4.21422504 L9.7296559,6.29967247 C9.89944111,6.43941894 10.1328958,6.51466705 10.376962,6.48241786 C10.8969293,6.41791948 11.2471113,5.94493141 11.1940534,5.43969415 C11.1622187,5.18170065 11.0348798,4.96670607 10.8650945,4.80546013 C10.8120367,4.76246122 8.31831627,2.76301162 8.31831627,2.76301162 C8.15914263,2.63401488 8.1273079,2.39752084 8.25464681,2.23734988 L9.24152339,0.957057153 C10.1541189,-0.214663308 11.7458554,-0.322160598 13.0298561,0.699063657 Z"></path>
                                                        </g>
                                                    </g>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Jumlah Pojok Deresan A</h6>
                                            <h6 class="font-extrabold mb-0">{{ $dataSantri->totalDeresan }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 col-md-4">
                            <div class="card">
                                <div class="card-body px-3 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="stats-icon green">
                                                <svg id="Edit" width="24px" height="24px" viewBox="0 0 24 24" fill="currentColor" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="text-white">
                                                    <title>Iconly/Bold/Edit</title>
                                                    <g id="Iconly/Bold/Edit" stroke="currentColor" stroke-width="1.5" fill="none" fill-rule="evenodd">
                                                        <g id="Edit" transform="translate(3.000000, 3.000000)" fill="#FFFFFF" fill-rule="nonzero">
                                                            <path d="M16.989778,15.9532516 C17.5468857,15.9532516 18,16.412265 18,16.9766258 C18,17.5420615 17.5468857,18 16.989778,18 L16.989778,18 L11.2796888,18 C10.7225811,18 10.2694668,17.5420615 10.2694668,16.9766258 C10.2694668,16.412265 10.7225811,15.9532516 11.2796888,15.9532516 L11.2796888,15.9532516 Z M13.0298561,0.699063657 L14.5048652,1.87078412 C15.109725,2.34377219 15.5129649,2.96725648 15.6509154,3.62298994 C15.810089,4.34429676 15.6403038,5.0527039 15.1627829,5.66543845 L6.37639783,17.027902 C5.97315794,17.543889 5.37890967,17.8341317 4.74221511,17.8448814 L1.24039498,17.8878803 C1.04938661,17.8878803 0.890212963,17.7588836 0.847766658,17.5761382 L0.051898447,14.1254752 C-0.086052043,13.4912412 0.051898447,12.8355077 0.455138341,12.3302704 L6.68413354,4.26797368 C6.7902493,4.13897694 6.98125767,4.11855245 7.10859659,4.21422504 L9.7296559,6.29967247 C9.89944111,6.43941894 10.1328958,6.51466705 10.376962,6.48241786 C10.8969293,6.41791948 11.2471113,5.94493141 11.1940534,5.43969415 C11.1622187,5.18170065 11.0348798,4.96670607 10.8650945,4.80546013 C10.8120367,4.76246122 8.31831627,2.76301162 8.31831627,2.76301162 C8.15914263,2.63401488 8.1273079,2.39752084 8.25464681,2.23734988 L9.24152339,0.957057153 C10.1541189,-0.214663308 11.7458554,-0.322160598 13.0298561,0.699063657 Z"></path>
                                                        </g>
                                                    </g>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Jumlah Pojok Murojaah</h6>
                                            <h6 class="font-extrabold mb-0">{{ $dataSantri->totalMurojaah }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 col-md-4">
                            <div class="card">
                                <div class="card-body px-3 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="stats-icon blue">
                                                <svg id="Edit" width="24px" height="24px" viewBox="0 0 24 24" fill="currentColor" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="text-white">
                                                    <title>Iconly/Bold/Edit</title>
                                                    <g id="Iconly/Bold/Edit" stroke="currentColor" stroke-width="1.5" fill="none" fill-rule="evenodd">
                                                        <g id="Edit" transform="translate(3.000000, 3.000000)" fill="#FFFFFF" fill-rule="nonzero">
                                                            <path d="M16.989778,15.9532516 C17.5468857,15.9532516 18,16.412265 18,16.9766258 C18,17.5420615 17.5468857,18 16.989778,18 L16.989778,18 L11.2796888,18 C10.7225811,18 10.2694668,17.5420615 10.2694668,16.9766258 C10.2694668,16.412265 10.7225811,15.9532516 11.2796888,15.9532516 L11.2796888,15.9532516 Z M13.0298561,0.699063657 L14.5048652,1.87078412 C15.109725,2.34377219 15.5129649,2.96725648 15.6509154,3.62298994 C15.810089,4.34429676 15.6403038,5.0527039 15.1627829,5.66543845 L6.37639783,17.027902 C5.97315794,17.543889 5.37890967,17.8341317 4.74221511,17.8448814 L1.24039498,17.8878803 C1.04938661,17.8878803 0.890212963,17.7588836 0.847766658,17.5761382 L0.051898447,14.1254752 C-0.086052043,13.4912412 0.051898447,12.8355077 0.455138341,12.3302704 L6.68413354,4.26797368 C6.7902493,4.13897694 6.98125767,4.11855245 7.10859659,4.21422504 L9.7296559,6.29967247 C9.89944111,6.43941894 10.1328958,6.51466705 10.376962,6.48241786 C10.8969293,6.41791948 11.2471113,5.94493141 11.1940534,5.43969415 C11.1622187,5.18170065 11.0348798,4.96670607 10.8650945,4.80546013 C10.8120367,4.76246122 8.31831627,2.76301162 8.31831627,2.76301162 C8.15914263,2.63401488 8.1273079,2.39752084 8.25464681,2.23734988 L9.24152339,0.957057153 C10.1541189,-0.214663308 11.7458554,-0.322160598 13.0298561,0.699063657 Z"></path>
                                                        </g>
                                                    </g>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Jumlah Pojok Tahsin Binnadhor</h6>
                                            <h6 class="font-extrabold mb-0">{{ $dataSantri->totalTahsin }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 col-md-4">
                            <div class="card">
                                <div class="card-body px-3 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="stats-icon cyan">
                                                <svg id="Edit" width="24px" height="24px" viewBox="0 0 24 24" fill="currentColor" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="text-white">
                                                    <title>Iconly/Bold/Edit</title>
                                                    <g id="Iconly/Bold/Edit" stroke="currentColor" stroke-width="1.5" fill="none" fill-rule="evenodd">
                                                        <g id="Edit" transform="translate(3.000000, 3.000000)" fill="#FFFFFF" fill-rule="nonzero">
                                                            <path d="M16.989778,15.9532516 C17.5468857,15.9532516 18,16.412265 18,16.9766258 C18,17.5420615 17.5468857,18 16.989778,18 L16.989778,18 L11.2796888,18 C10.7225811,18 10.2694668,17.5420615 10.2694668,16.9766258 C10.2694668,16.412265 10.7225811,15.9532516 11.2796888,15.9532516 L11.2796888,15.9532516 Z M13.0298561,0.699063657 L14.5048652,1.87078412 C15.109725,2.34377219 15.5129649,2.96725648 15.6509154,3.62298994 C15.810089,4.34429676 15.6403038,5.0527039 15.1627829,5.66543845 L6.37639783,17.027902 C5.97315794,17.543889 5.37890967,17.8341317 4.74221511,17.8448814 L1.24039498,17.8878803 C1.04938661,17.8878803 0.890212963,17.7588836 0.847766658,17.5761382 L0.051898447,14.1254752 C-0.086052043,13.4912412 0.051898447,12.8355077 0.455138341,12.3302704 L6.68413354,4.26797368 C6.7902493,4.13897694 6.98125767,4.11855245 7.10859659,4.21422504 L9.7296559,6.29967247 C9.89944111,6.43941894 10.1328958,6.51466705 10.376962,6.48241786 C10.8969293,6.41791948 11.2471113,5.94493141 11.1940534,5.43969415 C11.1622187,5.18170065 11.0348798,4.96670607 10.8650945,4.80546013 C10.8120367,4.76246122 8.31831627,2.76301162 8.31831627,2.76301162 C8.15914263,2.63401488 8.1273079,2.39752084 8.25464681,2.23734988 L9.24152339,0.957057153 C10.1541189,-0.214663308 11.7458554,-0.322160598 13.0298561,0.699063657 Z"></path>
                                                        </g>
                                                    </g>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Jumlah Pojok Ziyadah</h6>
                                            <h6 class="font-extrabold mb-0">{{ $dataSantri->totalZiyadah }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-12 col-md-6">
                            <div class="card">
                                <div class="card-body px-3 py-4-5">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <h6 class="text-muted font-semibold">Ustad Tahfidz</h6>
                                            <h6 class="font-extrabold mb-0">{{ $deresana->first()->namaUstad ?? '-' }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12">
                            <div class="card">
                                <div class="card-content">
                                    <h4 class="p-3">Deresan A</h4>
                                    <div class="card-body">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                                                Surat(pojok) | Kehadiran | Catatan Ustad | Tanggal | Jumlah Pojok
                                            </li>
                                        </ul>
                                        <div class="list-group-container">
                                            <ul class="list-group">
                                                @foreach ($deresana as $row)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span class="badge bg-primary badge-pill badge-round">
                                                            {{ $row->namaSuratAwal }}({{ $row->capaian_awal }}) - {{ $row->namaSuratAkhir }}({{ $row->capaian_akhir }})
                                                        </span>
                                                        <span class="badge {{ $row->kehadiran == 0 || $row->kehadiran == 3 ? 'bg-danger' : ($row->kehadiran == 1 ? 'bg-success' : 'bg-warning') }} badge-pill badge-round">
                                                            {{ $row->kehadiran == 0 ? 'Tidak Setor' : ($row->kehadiran == 1 ? 'Ngaji Kitab' : ($row->kehadiran == 2 ? 'Izin' : 'Alpha')) }}
                                                        </span>
                                                        <span>{{ $row->catatan }}</span>
                                                        <span>{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d F Y') }}</span>
                                                        <span class="badge bg-secondary badge-pill badge-round ml-1">
                                                            {{ $row->jumlah ?? "-" }}
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <div class="card">
                                <div class="card-content">
                                    <h4 class="p-3">Murojaah</h4>
                                    <div class="card-body">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                                                Surat(pojok) | Kehadiran | Catatan Ustad | Tanggal | Jumlah Pojok
                                            </li>
                                        </ul>
                                        <div class="list-group-container">
                                            <ul class="list-group">
                                                @foreach ($murojaah as $row)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span class="badge bg-primary badge-pill badge-round">
                                                            {{ $row->namaSuratAwal }}({{ $row->capaian_awal }}) - {{ $row->namaSuratAkhir }}({{ $row->capaian_akhir }})
                                                        </span>
                                                        <span class="badge {{ $row->kehadiran == 0 || $row->kehadiran == 3 ? 'bg-danger' : ($row->kehadiran == 1 ? 'bg-success' : 'bg-warning') }} badge-pill badge-round">
                                                            {{ $row->kehadiran == 0 ? 'Tidak Setor' : ($row->kehadiran == 1 ? 'Ngaji Kitab' : ($row->kehadiran == 2 ? 'Izin' : 'Alpha')) }}
                                                        </span>
                                                        <span>{{ $row->catatan }}</span>
                                                        <span>{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d F Y') }}</span>
                                                        <span class="badge bg-secondary badge-pill badge-round ml-1">
                                                            {{ $row->jumlah ?? "-" }}
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <div class="card">
                                <div class="card-content">
                                    <h4 class="p-3">Tahsin Binnadhor</h4>
                                    <div class="card-body">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                                                Surat(pojok) | Kehadiran | Catatan Ustad | Tanggal | Jumlah Pojok
                                            </li>
                                        </ul>
                                        <div class="list-group-container">
                                            <ul class="list-group">
                                                @foreach ($tahsin as $row)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span class="badge bg-primary badge-pill badge-round">
                                                            {{ $row->namaSuratAwal }}({{ $row->capaian_awal }}) - {{ $row->namaSuratAkhir }}({{ $row->capaian_akhir }})
                                                        </span>
                                                        <span class="badge {{ $row->kehadiran == 0 || $row->kehadiran == 3 ? 'bg-danger' : ($row->kehadiran == 1 ? 'bg-success' : 'bg-warning') }} badge-pill badge-round">
                                                            {{ $row->kehadiran == 0 ? 'Tidak Setor' : ($row->kehadiran == 1 ? 'Ngaji Kitab' : ($row->kehadiran == 2 ? 'Izin' : 'Alpha')) }}
                                                        </span>
                                                        <span>{{ $row->catatan }}</span>
                                                        <span>{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d F Y') }}</span>
                                                        <span class="badge bg-secondary badge-pill badge-round ml-1">
                                                            {{ $row->jumlah ?? "-" }}
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <div class="card">
                                <div class="card-content">
                                    <h4 class="p-3">Ziyadah</h4>
                                    <div class="card-body">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                                                Surat(pojok) | Kehadiran | Catatan Ustad | Tanggal | Jumlah Pojok
                                            </li>
                                        </ul>
                                        <div class="list-group-container">
                                            <ul class="list-group">
                                                @foreach ($ziyadah as $row)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span class="badge bg-primary badge-pill badge-round">
                                                            {{ $row->namaSuratAwal }}({{ $row->capaian_awal }}) - {{ $row->namaSuratAkhir }}({{ $row->capaian_akhir }})
                                                        </span>
                                                        <span class="badge {{ $row->kehadiran == 0 || $row->kehadiran == 3 ? 'bg-danger' : ($row->kehadiran == 1 ? 'bg-success' : 'bg-warning') }} badge-pill badge-round">
                                                            {{ $row->kehadiran == 0 ? 'Tidak Setor' : ($row->kehadiran == 1 ? 'Ngaji Kitab' : ($row->kehadiran == 2 ? 'Izin' : 'Alpha')) }}
                                                        </span>
                                                        <span>{{ $row->catatan }}</span>
                                                        <span>{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d F Y') }}</span>
                                                        <span class="badge bg-secondary badge-pill badge-round ml-1">
                                                            {{ $row->jumlah ?? "-" }}
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script>
    $(document).ready(function () {
        let chartZiyadah;
        let graphZiyadah;
        let chartDeresan;
        let graphDeresan;

        function toRoman(num) 
        {
            const romanNumerals = [
                { value: 1000, numeral: "M" },
                { value: 900, numeral: "CM" },
                { value: 500, numeral: "D" },
                { value: 400, numeral: "CD" },
                { value: 100, numeral: "C" },
                { value: 90, numeral: "XC" },
                { value: 50, numeral: "L" },
                { value: 40, numeral: "XL" },
                { value: 10, numeral: "X" },
                { value: 9, numeral: "IX" },
                { value: 5, numeral: "V" },
                { value: 4, numeral: "IV" },
                { value: 1, numeral: "I" }
            ];
            
            let result = "";
            for (const { value, numeral } of romanNumerals) {
                while (num >= value) {
                    result += numeral;
                    num -= value;
                }
            }
            return result;
        }

        let formData = null;
        formData += '&_token=' + $('meta[name="csrf-token"]').attr('content');
        let url = baseUrl + '{{ $url }}' + '/diagram-ziyadah';
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function (response) {
                // Ziyadah

                let optionsDiagramZiyadah  = {
                    series: [response.persentaseTargetZiyadah, response.persentaseTidakTargetZiyadah, response.persentaseKhatamZiyadah],
                    labels: ['Target', 'Tidak Target', 'Khatam'],
                    colors: ['#5B913B','#BE3144', '#FFD65A'],
                    chart: {
                        type: 'donut',
                        width: '100%',
                        height:'350px'
                    },
                    legend: {
                        position: 'bottom'
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '30%'
                            }
                        }
                    }
                }
                
                chartZiyadah = new ApexCharts($('#chart-ziyadah')[0], optionsDiagramZiyadah);
                chartZiyadah.render();

                let graphContentZiyadah = Object.keys(response.dataGraphZiyadah).map(Number).map(toRoman);

                var dataGraphZiyadah = response.dataGraphZiyadah; // Pastikan dataChart adalah objek yang valid
                var categories = Object.keys(dataGraphZiyadah);

                var graphOptionsZiyadah = 
                {
                    series: [
                        {
                            name: "Target",
                            data: categories.map(key => dataGraphZiyadah[key].totalTarget), // Ambil totalTarget untuk setiap tingkatan
                        },
                        {
                            name: "Tidak Target",
                            data: categories.map(key => dataGraphZiyadah[key].totalTidakTarget), // Ambil totalTidakTarget untuk setiap tingkatan
                        },
                        {
                            name: "Khatam",
                            data: categories.map(key => dataGraphZiyadah[key].totalKhatam), // Ambil totalKhatam untuk setiap tingkatan
                        },
                    ],
                    chart: {
                        type: "bar",
                        height: 350,
                    },
                    xaxis: {
                        labels: {
                            show: true,
                        }
                    },
                    plotOptions: {
                        bar: {
                        horizontal: false,
                        columnWidth: "55%",
                        endingShape: "rounded",
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ["transparent"],
                    },
                    xaxis: {
                        categories: graphContentZiyadah,
                        labels: {
                            show: true,
                            // Anda dapat mengurangi ukuran font atau memodifikasi tampilan label
                        }
                    },
                    yaxis: {
                        title: {
                        text: "Santri",
                        },
                    },
                    fill: {
                        opacity: 1,
                    },
                    tooltip: {
                        y: {
                        formatter: function(val) {
                            return val + " Santri";
                        },
                        },
                    },
                    colors: ['#5B913B','#BE3144', '#FFD65A'],
                };

                graphZiyadah = new ApexCharts($("#graph-ziyadah")[0], graphOptionsZiyadah);
                graphZiyadah.render();

                // Deresan

                let optionsDiagramDeresan  = {
                    series: [response.persentaseTargetDeresan, response.persentaseTidakTargetDeresan, response.persentaseTidakTertulisDeresan],
                    labels: ['Target', 'Tidak Target', 'Belum Terisi'],
                    colors: ['#5B913B','#BE3144', '#FFD65A'],
                    chart: {
                        type: 'donut',
                        width: '100%',
                        height:'350px'
                    },
                    legend: {
                        position: 'bottom'
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '30%'
                            }
                        }
                    }
                }
                
                chartDeresan = new ApexCharts($('#chart-deresan')[0], optionsDiagramDeresan);
                chartDeresan.render();

                let graphContentDeresan = Object.keys(response.dataGraphDeresan).map(Number).map(toRoman);

                var dataGraphDeresan = response.dataGraphDeresan;
                var categoriesDeresan = Object.keys(dataGraphDeresan);

                var graphOptionsDeresan = {
                    series: [
                        {
                            name: "Target",
                            data: categoriesDeresan.map(key => dataGraphDeresan[key].totalTarget),
                        },
                        {
                            name: "Tidak Target",
                            data: categoriesDeresan.map(key => dataGraphDeresan[key].totalTidakTarget),
                        },
                        {
                            name: "Belum Terisi",
                            data: categoriesDeresan.map(key => dataGraphDeresan[key].totalTidakTertulis),
                        },
                    ],
                    chart: {
                        type: "bar",
                        height: 350,
                    },
                    plotOptions: {
                        bar: {
                        horizontal: false,
                        columnWidth: "55%",
                        endingShape: "rounded",
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ["transparent"],
                    },
                    xaxis: {
                        categories: graphContentDeresan,
                    },
                    yaxis: {
                        title: {
                        text: "Santri",
                        },
                    },
                    fill: {
                        opacity: 1,
                    },
                    tooltip: {
                        y: {
                        formatter: function(val) {
                            return val + " Santri";
                        },
                        },
                    },
                    colors: ['#5B913B','#BE3144', '#FFD65A'],
                };

                graphDeresan = new ApexCharts($("#graph-deresan")[0], graphOptionsDeresan);
                graphDeresan.render();

                hideLoading();
            },
            error: function (xhr) {
                console.log(xhr.responseText)
                hideLoading();
            },
        });

        $('#form-range-waktu').on('submit', function (event) 
        {
            showLoading();

            event.preventDefault();

            let formData = $(this).serialize();
            formData += '&_token=' + $('meta[name="csrf-token"]').attr('content');
            
            let url = baseUrl + '{{ $url }}' + '/form-blangko';
            
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function (response) {
                    let tableElement = document.querySelector("#tabel-blangko");

                    // Cek apakah DataTable sudah ada, lalu hapus dulu
                    if (tableElement.dataTable) {
                        tableElement.dataTable.destroy(); // Hapus instance sebelumnya
                        tableElement.innerHTML = ''; // Kosongkan tabel
                    }

                    // Kosongkan isi tabel sebelum dirender ulang
                    $("#tabel-blangko tbody").empty();

                    // Inisialisasi ulang DataTable dengan data dari response
                    tableElement.dataTable = new simpleDatatables.DataTable(tableElement, {
                        data: {
                            data: Object.values(response.dataBlangko).map(item => [
                                item.no, 
                                item.namaSantri,
                                item.juzAwalZiyadah,
                                item.pojokAwalZiyadah,
                                item.juzAkhirZiyadah,
                                item.pojokAkhirZiyadah,
                                item.jmlZiyadah,
                                item.juzAwalDeresanA,
                                item.pojokAwalDeresanA,
                                item.juzAkhirDeresanA,
                                item.pojokAkhirDeresanA,
                                item.jmlDeresanA,
                                item.juzAwalMurojaah,
                                item.pojokAwalMurojaah,
                                item.juzAkhirMurojaah,
                                item.pojokAkhirMurojaah,
                                item.jmlMurojaah,
                                item.totalSeluruhPojok,
                                item.lvlDeresan,
                                item.juzAkhirTahsinBinnadhor,
                                item.pojokAkhirTahsinBinnadhor,
                                item.tidakSetor,
                                item.izin,
                                item.alpha,
                                item.setor
                            ])
                        }
                    });

                    // Perbarui tampilan teks tanggal
                    $('#text-tgl-awal-blangko').text(response.txtTglAwal);
                    $('#text-emote-blangko').text('➡️');
                    $('#text-tgl-akhir-blangko').text(response.txtTglAkhir);
                    $('#blangko-wrapper').removeClass('d-none');

                    // Perbarui input tanggal
                    $("#tanggal-awal-blangko").val(response.formattedAwal);
                    $("#tanggal-akhir-blangko").val(response.formattedAkhir);
                    $('#form-cetak-blangko').removeClass('d-none');

                    hideLoading();
                },
                error: function (xhr) {
                    console.log(xhr.responseText)
                    hideLoading();
                },
            });
        });

        $('#form-diagram-ziyadah').on('submit', function (event) 
        {
            showLoading();

            event.preventDefault();

            let formData = $(this).serialize();
            formData += '&_token=' + $('meta[name="csrf-token"]').attr('content');
            let url = baseUrl + '{{ $url }}' + '/diagram-ziyadah';
            
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function (response) {
                    chartZiyadah.destroy();
                    graphZiyadah.destroy();
                    chartDeresan.destroy();
                    graphDeresan.destroy();

                    // Ziyadah

                    let optionsDiagramZiyadah  = {
                        series: [response.persentaseTargetZiyadah, response.persentaseTidakTargetZiyadah, response.persentaseKhatamZiyadah],
                        labels: ['Target', 'Tidak Target', 'Khatam'],
                        colors: ['#5B913B','#BE3144', '#FFD65A'],
                        chart: {
                            type: 'donut',
                            width: '100%',
                            height:'350px'
                        },
                        legend: {
                            position: 'bottom'
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '30%'
                                }
                            }
                        }
                    }
                    
                    chartZiyadah = new ApexCharts($('#chart-ziyadah')[0], optionsDiagramZiyadah);
                    chartZiyadah.render();

                    let graphContentZiyadah = Object.keys(response.dataGraphZiyadah).map(Number).map(toRoman);

                    var dataGraphZiyadah = response.dataGraphZiyadah; // Pastikan dataChart adalah objek yang valid
                    var categories = Object.keys(dataGraphZiyadah);

                    var graphOptionsZiyadah = {
                        series: [
                            {
                                name: "Target",
                                data: categories.map(key => dataGraphZiyadah[key].totalTarget), // Ambil totalTarget untuk setiap tingkatan
                            },
                            {
                                name: "Tidak Target",
                                data: categories.map(key => dataGraphZiyadah[key].totalTidakTarget), // Ambil totalTidakTarget untuk setiap tingkatan
                            },
                            {
                                name: "Khatam",
                                data: categories.map(key => dataGraphZiyadah[key].totalKhatam), // Ambil totalKhatam untuk setiap tingkatan
                            },
                        ],
                        chart: {
                            type: "bar",
                            height: 350,
                        },
                        plotOptions: {
                            bar: {
                            horizontal: false,
                            columnWidth: "55%",
                            endingShape: "rounded",
                            },
                        },
                        dataLabels: {
                            enabled: false,
                        },
                        xaxis: {
                            labels: {
                                show: true,
                                // Anda dapat mengurangi ukuran font atau memodifikasi tampilan label
                            }
                        },
                        stroke: {
                            show: true,
                            width: 2,
                            colors: ["transparent"],
                        },
                        xaxis: {
                            categories: graphContentZiyadah,
                            labels: {
                                show: true,
                                // Anda dapat mengurangi ukuran font atau memodifikasi tampilan label
                            }
                        },
                        yaxis: {
                            title: {
                            text: "Santri",
                            },
                        },
                        fill: {
                            opacity: 1,
                        },
                        tooltip: {
                            y: {
                            formatter: function(val) {
                                return val + " Santri";
                            },
                            },
                        },
                        colors: ['#5B913B','#BE3144', '#FFD65A'],
                    };

                    graphZiyadah = new ApexCharts($("#graph-ziyadah")[0], graphOptionsZiyadah);
                    graphZiyadah.render();

                    // Deresan

                    let optionsDiagramDeresan  = {
                        series: [response.persentaseTargetDeresan, response.persentaseTidakTargetDeresan, response.persentaseTidakTertulisDeresan],
                        labels: ['Target', 'Tidak Target', 'Belum Terisi'],
                        colors: ['#5B913B','#BE3144', '#FFD65A'],
                        chart: {
                            type: 'donut',
                            width: '100%',
                            height:'350px'
                        },
                        legend: {
                            position: 'bottom'
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '30%'
                                }
                            }
                        }
                    }
                    
                    chartDeresan = new ApexCharts($('#chart-deresan')[0], optionsDiagramDeresan);
                    chartDeresan.render();

                    let graphContentDeresan = Object.keys(response.dataGraphDeresan).map(Number).map(toRoman);

                    var dataGraphDeresan = response.dataGraphDeresan;
                    var categories = Object.keys(dataGraphDeresan);

                    var graphOptionsDeresan = {
                        series: [
                            {
                                name: "Target",
                                data: categories.map(key => dataGraphDeresan[key].totalTarget),
                            },
                            {
                                name: "Tidak Target",
                                data: categories.map(key => dataGraphDeresan[key].totalTidakTarget),
                            },
                            {
                                name: "Belum Terisi",
                                data: categories.map(key => dataGraphDeresan[key].totalTidakTertulis),
                            },
                        ],
                        chart: {
                            type: "bar",
                            height: 350,
                        },
                        plotOptions: {
                            bar: {
                            horizontal: false,
                            columnWidth: "55%",
                            endingShape: "rounded",
                            },
                        },
                        dataLabels: {
                            enabled: false,
                        },
                        stroke: {
                            show: true,
                            width: 2,
                            colors: ["transparent"],
                        },
                        xaxis: {
                            categories: graphContentDeresan,
                        },
                        yaxis: {
                            title: {
                            text: "Santri",
                            },
                        },
                        fill: {
                            opacity: 1,
                        },
                        tooltip: {
                            y: {
                            formatter: function(val) {
                                return val + " Santri";
                            },
                            },
                        },
                        colors: ['#5B913B','#BE3144', '#FFD65A'],
                    };

                    graphDeresan = new ApexCharts($("#graph-deresan")[0], graphOptionsDeresan);
                    graphDeresan.render();

                    $('#text-tgl-awal-ziyadah').text(response.txtTglAwal);
                    $('#text-emote-ziyadah').text('➡️');
                    $('#text-tgl-akhir-ziyadah').text(response.txtTglAkhir);
                    $('#text-waktu-ziyadah').removeClass("d-none");

                    hideLoading();
                },
                error: function (xhr) {
                    console.log(xhr.responseText)
                    hideLoading();
                },
            });
        });
    });
</script>
@endsection