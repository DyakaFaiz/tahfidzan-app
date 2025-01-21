@extends('layout.main')

@section('custom-header-css')
<link rel="stylesheet" href="{{ url('') }}/assets/vendors/simple-datatables/style.css">

<!-- Include Choices CSS -->
<link rel="stylesheet" href="assets/vendors/choices.js/choices.min.css" />

<style>
    .table-wrapper {
        max-height: 78vh; /* Tinggi area scroll */
        overflow-y: auto; /* Scroll hanya pada konten */
        position: relative;
    }

    .sticky-row, .sticky-2, .sticky-3, .sticky-no, .sticky-nama {
        position: sticky;
        background: #f8f9fa;
        z-index: 2;
    }

    .sticky-row {
        top: 0;
    }

    .sticky-2 {
        top: 64px; /* Atur sesuai dengan tinggi baris pertama */
    }

    .sticky-3 {
        top: 130px; /* Atur sesuai dengan tinggi baris pertama + kedua */
    }

    /* .sticky-no {
        left: 0;
        z-index: 1;
        background-color: #007bff;
    }
    
    .sticky-nama {
        left: 42px;
        z-index: 1;
        background-color: #007bff;
    } */
</style>
@endsection

@section('content')
    <div class="col-12 col-md-12">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <nav>
                        <div class="nav nav-tabs gap-1" id="nav-tab" role="tablist">
                          <button class="nav-link active" id="nav-deresan-a-tab" data-bs-toggle="tab" data-bs-target="#nav-deresan-a" type="button" role="tab" aria-controls="nav-deresan-a" aria-selected="true">Deresan A</button>
                          <button class="nav-link" id="nav-murojaah-tab" data-bs-toggle="tab" data-bs-target="#nav-murojaah" type="button" role="tab" aria-controls="nav-murojaah" aria-selected="false">Murojaah</button>
                          <button class="nav-link" id="nav-tahsin-binnadhor-tab" data-bs-toggle="tab" data-bs-target="#nav-tahsin-binnadhor" type="button" role="tab" aria-controls="nav-tahsin-binnadhor" aria-selected="false">Tahsin Binnadhor</button>
                          <button class="nav-link" id="nav-ziyadah-tab" data-bs-toggle="tab" data-bs-target="#nav-ziyadah" type="button" role="tab" aria-controls="nav-ziyadah" aria-selected="false">Ziyadah</button>
                        </div>
                      </nav>
                      @php
                          use Carbon\Carbon;
                      @endphp
                      <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-deresan-a" role="tabpanel" aria-labelledby="nav-deresan-a-tab">
                            {{-- Tab Evaluasi Deresan A --}}
                            <div class="mt-3">
                                <ul class="nav nav-tabs d-flex justify-content-center" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active btn btn-danger" id="deresan-a-evaluasi-tab" data-bs-toggle="tab" data-bs-target="#deresan-a-evaluasi" type="button" role="tab" aria-controls="deresan-a-evaluasi" aria-selected="true">Evaluasi</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link btn btn-success" id="deresan-a-selesai-tab" data-bs-toggle="tab" data-bs-target="#deresan-a-selesai" type="button" role="tab" aria-controls="deresan-a-selesai" aria-selected="false">Selesai</button>
                                    </li>
                                </ul>
                                {{-- Tab Content --}}
                                <div class="tab-content" id="myTabContent">
                                    {{-- Content Evaluasi Deresan A --}}
                                    <div class="tab-pane fade show active" id="deresan-a-evaluasi" role="tabpanel" aria-labelledby="deresan-a-evaluasi-tab">
                                        <div class="table-responsive table-wrapper py-4">
                                            <table class="table table-lg">
                                                <thead>
                                                    <tr class="sticky-row">
                                                        <th>No</th>
                                                        <th>Hari, Tanggal</th>
                                                        <th>Nama Santri</th>
                                                        <th>Catatan</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $deresanAEvaluasi = $dataDeresanA->filter(function ($item) {
                                                            return $item->evaluasi == 0;
                                                        })->count();

                                                        $deresanASelesai = $dataDeresanA->filter(function ($item) {
                                                            return $item->evaluasi == 1;
                                                        })->count();
                                                    @endphp

                                                    @if ($deresanAEvaluasi > 0)
                                                        @foreach ($dataDeresanA as $row)
                                                            @if ($row->evaluasi == 0)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $row->hariSetor }}, {{ Carbon::parse($row->tglSetor)->translatedFormat('d F Y') }}</td>
                                                                    <td>{{ $row->namaSantri }}</td>
                                                                    <td>{{ $row->catatan }}</td>
                                                                    <td>
                                                                        <button class="btn btn-success btn-sm btn-action" data-kode="1" data-id="{{ $row->id }}">
                                                                            <i class="bi bi-check-lg"></i>✔
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                                                        </tr>
                                                    @endif
                                                
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    {{-- /Content Evaluasi Deresan A --}}

                                    {{-- Content Selesai Evaluasi Deresan A --}}
                                    <div class="tab-pane fade" id="deresan-a-selesai" role="tabpanel" aria-labelledby="deresan-a-selesai-tab">
                                        <div class="table-responsive table-wrapper py-4">
                                            <table class="table table-lg">
                                                <thead>
                                                    <tr class="sticky-row">
                                                        <th>No</th>
                                                        <th>Hari, Tanggal</th>
                                                        <th>Nama Santri</th>
                                                        <th>Catatan</th>
                                                        <th>Evaluasi Pada</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if ($deresanASelesai > 0)
                                                        @foreach ($dataDeresanA as $row)
                                                            @if ($row->evaluasi == 1)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $row->hariSetor }}, {{ Carbon::parse($row->tglSetor)->translatedFormat('d F Y') }}</td>
                                                                    <td>{{ $row->namaSantri }}</td>
                                                                    <td>{{ $row->catatan }}</td>
                                                                    <td>{{ Carbon::parse($row->tglEvaluasi)->translatedFormat('l, d F Y') }}</td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    {{-- /Content Selesai Evaluasi Deresan A --}}

                                </div>
                                {{-- /Tab Content --}}
                            </div>
                            {{-- /Tab Evaluasi Deresan A --}}
                        </div>
                        <div class="tab-pane fade" id="nav-murojaah" role="tabpanel" aria-labelledby="nav-murojaah-tab">
                            {{-- Tab Evaluasi Murojaah --}}
                            <div class="mt-3">
                                <ul class="nav nav-tabs d-flex justify-content-center" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active btn btn-danger" id="murojaah-evaluasi-tab" data-bs-toggle="tab" data-bs-target="#murojaah-evaluasi" type="button" role="tab" aria-controls="murojaah-evaluasi" aria-selected="true">Evaluasi</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link btn btn-success" id="murojaah-selesai-tab" data-bs-toggle="tab" data-bs-target="#murojaah-selesai" type="button" role="tab" aria-controls="murojaah-selesai" aria-selected="false">Selesai</button>
                                    </li>
                                </ul>
                                {{-- Tab Content --}}
                                <div class="tab-content" id="myTabContent">
                                    {{-- Content Evaluasi Murojaah --}}
                                    <div class="tab-pane fade show active" id="murojaah-evaluasi" role="tabpanel" aria-labelledby="murojaah-evaluasi-tab">
                                        <div class="table-responsive table-wrapper py-4">
                                            <table class="table table-lg">
                                                <thead>
                                                    <tr class="sticky-row">
                                                        <th>No</th>
                                                        <th>Hari, Tanggal</th>
                                                        <th>Nama Santri</th>
                                                        <th>Catatan</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $murojaahEvalausi = $dataMurojaah->filter(function ($item) {
                                                            return $item->evaluasi == 0;
                                                        })->count();

                                                        $murojaahSelesai = $dataMurojaah->filter(function ($item) {
                                                            return $item->evaluasi == 1;
                                                        })->count();
                                                    @endphp

                                                    @if ($murojaahEvalausi > 0)
                                                        @foreach ($dataMurojaah as $row)
                                                            @if ($row->evaluasi == 0)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $row->hariSetor }}, {{ Carbon::parse($row->tglSetor)->translatedFormat('d F Y') }}</td>
                                                                    <td>{{ $row->namaSantri }}</td>
                                                                    <td>{{ $row->catatan }}</td>
                                                                    <td>
                                                                        <button class="btn btn-success btn-sm btn-action" data-kode="1" data-id="{{ $row->id }}">
                                                                            <i class="bi bi-check-lg"></i>✔
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                                                        </tr>
                                                    @endif
                                                
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    {{-- /Content Evaluasi Murojaah --}}

                                    {{-- Content Selesai Evaluasi Murojaah --}}
                                    <div class="tab-pane fade" id="murojaah-selesai" role="tabpanel" aria-labelledby="murojaah-selesai-tab">
                                        <div class="table-responsive table-wrapper py-4">
                                            <table class="table table-lg">
                                                <thead>
                                                    <tr class="sticky-row">
                                                        <th>No</th>
                                                        <th>Hari, Tanggal</th>
                                                        <th>Nama Santri</th>
                                                        <th>Catatan</th>
                                                        <th>Evaluasi Pada</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if ($murojaahSelesai > 0)
                                                        @foreach ($dataMurojaah as $row)
                                                            @if ($row->evaluasi == 1)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $row->hariSetor }}, {{ Carbon::parse($row->tglSetor)->translatedFormat('d F Y') }}</td>
                                                                    <td>{{ $row->namaSantri }}</td>
                                                                    <td>{{ $row->catatan }}</td>
                                                                    <td>{{ Carbon::parse($row->tglEvaluasi)->translatedFormat('d F Y') }}</td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    {{-- /Content Selesai Evaluasi Murojaah --}}

                                </div>
                                {{-- /Tab Content --}}
                            </div>
                            {{-- /Tab Evaluasi Murojaah --}}
                        </div>
                        <div class="tab-pane fade" id="nav-tahsin-binnadhor" role="tabpanel" aria-labelledby="nav-tahsin-binnadhor-tab">
                            {{-- Tab Evaluasi Tahsin --}}
                            <div class="mt-3">
                                <ul class="nav nav-tabs d-flex justify-content-center" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active btn btn-danger" id="tahsin-evaluasi-tab" data-bs-toggle="tab" data-bs-target="#tahsin-evaluasi" type="button" role="tab" aria-controls="tahsin-evaluasi" aria-selected="true">Evaluasi</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link btn btn-success" id="tahsin-selesai-tab" data-bs-toggle="tab" data-bs-target="#tahsin-selesai" type="button" role="tab" aria-controls="tahsin-selesai" aria-selected="false">Selesai</button>
                                    </li>
                                </ul>
                                {{-- Tab Content --}}
                                <div class="tab-content" id="myTabContent">
                                    {{-- Content Evaluasi Tahsin --}}
                                    <div class="tab-pane fade show active" id="tahsin-evaluasi" role="tabpanel" aria-labelledby="tahsin-evaluasi-tab">
                                        <div class="table-responsive table-wrapper py-4">
                                            <table class="table table-lg">
                                                <thead>
                                                    <tr class="sticky-row">
                                                        <th>No</th>
                                                        <th>Hari, Tanggal</th>
                                                        <th>Nama Santri</th>
                                                        <th>Catatan</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $tahsinEvaluasi = $dataTahsin->filter(function ($item) {
                                                            return $item->evaluasi == 0;
                                                        })->count();

                                                        $tahsinSelesai = $dataTahsin->filter(function ($item) {
                                                            return $item->evaluasi == 1;
                                                        })->count();
                                                    @endphp

                                                    @if ($tahsinEvaluasi > 0)
                                                        @foreach ($dataTahsin as $row)
                                                            @if ($row->evaluasi == 0)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $row->hariSetor }}, {{ Carbon::parse($row->tglSetor)->translatedFormat('d F Y') }}</td>
                                                                    <td>{{ $row->namaSantri }}</td>
                                                                    <td>{{ $row->catatan }}</td>
                                                                    <td>
                                                                        <button class="btn btn-success btn-sm btn-action" data-kode="1" data-id="{{ $row->id }}">
                                                                            <i class="bi bi-check-lg"></i>✔
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                                                        </tr>
                                                    @endif
                                                
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    {{-- /Content Evaluasi Tahsin --}}

                                    {{-- Content Selesai Evaluasi Tahsin --}}
                                    <div class="tab-pane fade" id="tahsin-selesai" role="tabpanel" aria-labelledby="tahsin-selesai-tab">
                                        <div class="table-responsive table-wrapper py-4">
                                            <table class="table table-lg">
                                                <thead>
                                                    <tr class="sticky-row">
                                                        <th>No</th>
                                                        <th>Hari, Tanggal</th>
                                                        <th>Nama Santri</th>
                                                        <th>Catatan</th>
                                                        <th>Evaluasi Pada</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if ($tahsinSelesai > 0)
                                                        @foreach ($dataTahsin as $row)
                                                            @if ($row->evaluasi == 1)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $row->hariSetor }}, {{ Carbon::parse($row->tglSetor)->translatedFormat('d F Y') }}</td>
                                                                    <td>{{ $row->namaSantri }}</td>
                                                                    <td>{{ $row->catatan }}</td>
                                                                    <td>{{ Carbon::parse($row->tglEvaluasi)->translatedFormat('d F Y') }}</td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    {{-- /Content Selesai Evaluasi Tahsin --}}

                                </div>
                                {{-- /Tab Content --}}
                            </div>
                            {{-- /Tab Evaluasi Tahsin --}}
                        </div>
                        <div class="tab-pane fade" id="nav-ziyadah" role="tabpanel" aria-labelledby="nav-ziyadah-tab">
                            {{-- Tab Evaluasi Ziyadah --}}
                            <div class="mt-3">
                                <ul class="nav nav-tabs d-flex justify-content-center" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active btn btn-danger" id="ziyadah-evaluasi-tab" data-bs-toggle="tab" data-bs-target="#ziyadah-evaluasi" type="button" role="tab" aria-controls="ziyadah-evaluasi" aria-selected="true">Evaluasi</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link btn btn-success" id="ziyadah-selesai-tab" data-bs-toggle="tab" data-bs-target="#ziyadah-selesai" type="button" role="tab" aria-controls="ziyadah-selesai" aria-selected="false">Selesai</button>
                                    </li>
                                </ul>
                                {{-- Tab Content --}}
                                <div class="tab-content" id="myTabContent">
                                    {{-- Content Evaluasi Ziyadah --}}
                                    <div class="tab-pane fade show active" id="ziyadah-evaluasi" role="tabpanel" aria-labelledby="ziyadah-evaluasi-tab">
                                        <div class="table-responsive table-wrapper py-4">
                                            <table class="table table-lg">
                                                <thead>
                                                    <tr class="sticky-row">
                                                        <th>No</th>
                                                        <th>Hari, Tanggal</th>
                                                        <th>Nama Santri</th>
                                                        <th>Catatan</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $ziyadahEvaluasi = $dataZiyadah->filter(function ($item) {
                                                            return $item->evaluasi == 0;
                                                        })->count();

                                                        $ziyadahSelesai = $dataZiyadah->filter(function ($item) {
                                                            return $item->evaluasi == 1;
                                                        })->count();
                                                    @endphp

                                                    @if ($ziyadahEvaluasi > 0)
                                                        @foreach ($dataZiyadah as $row)
                                                            @if ($row->evaluasi == 0)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $row->hariSetor }}, {{ Carbon::parse($row->tglSetor)->translatedFormat('d F Y') }}</td>
                                                                    <td>{{ $row->namaSantri }}</td>
                                                                    <td>{{ $row->catatan }}</td>
                                                                    <td>
                                                                        <button class="btn btn-success btn-sm btn-action" data-kode="1" data-id="{{ $row->id }}">
                                                                            <i class="bi bi-check-lg"></i>✔
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                                                        </tr>
                                                    @endif
                                                
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    {{-- /Content Evaluasi Ziyadah --}}

                                    {{-- Content Selesai Evaluasi Ziyadah --}}
                                    <div class="tab-pane fade" id="ziyadah-selesai" role="tabpanel" aria-labelledby="ziyadah-selesai-tab">
                                        <div class="table-responsive table-wrapper py-4">
                                            <table class="table table-lg">
                                                <thead>
                                                    <tr class="sticky-row">
                                                        <th>No</th>
                                                        <th>Hari, Tanggal</th>
                                                        <th>Nama Santri</th>
                                                        <th>Catatan</th>
                                                        <th>Evaluasi Pada</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if ($ziyadahSelesai > 0)
                                                        @foreach ($dataZiyadah as $row)
                                                            @if ($row->evaluasi == 1)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $row->hariSetor }}, {{ Carbon::parse($row->tglSetor)->translatedFormat('d F Y') }}</td>
                                                                    <td>{{ $row->namaSantri }}</td>
                                                                    <td>{{ $row->catatan }}</td>
                                                                    <td>{{ Carbon::parse($row->tglEvaluasi)->translatedFormat('d F Y') }}</td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    {{-- /Content Selesai Evaluasi Ziyadah --}}

                                </div>
                                {{-- /Tab Content --}}
                            </div>
                            {{-- /Tab Evaluasi Ziyadah --}}
                        </div>
                      </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
<!-- Include Choices JavaScript -->
<script src="{{ url('') }}/assets/vendors/choices.js/choices.min.js"></script>

<script>
    $(document).ready(function () {
        $('.btn-action').on('click', function () {
            const dataId = $(this).data('id');
            const kodeTahfidzan = $(this).data('kode');
            
            console.log(dataId);
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Apakah Santri sudah berhasil dievalusi!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                    url: baseUrl + 'ketahfidzan/evaluasi/tahfidzan', // Sesuaikan dengan rute Anda
                    type: 'POST', // Menggunakan metode POST
                    data: {
                        id: dataId, // ID data
                        kdTahfidzan: kodeTahfidzan, // Kode tahfidzan
                        _token: $('meta[name="csrf-token"]').attr('content') // CSRF token Laravel
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message || 'Aksi berhasil dilakukan!',
                        });
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan, coba lagi nanti.',
                        });
                    }
                });
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Dibatalkan!',
                        text: 'Batal.',
                    });
                }
            });
        });

    });
</script>
@endsection