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
                            <div class="table-responsive table-wrapper py-4">
                                <table class="table table-lg">
                                    <thead>
                                        <tr class="sticky-row">
                                            <th>No</th>
                                            <th>Hari, Tanggal</th>
                                            <th>Nama Santri</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!$dataDeresanA->isEmpty())
                                            @foreach ($dataDeresanA as $row)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $row->hariSetor }}, {{ Carbon::parse($row->tglSetor)->translatedFormat('d F Y') }}</td>
                                                    <td>{{ $row->namaSantri }}</td>
                                                    <td>{{ $row->catatan }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Tidak ada data</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-murojaah" role="tabpanel" aria-labelledby="nav-murojaah-tab">
                            <div class="table-responsive table-wrapper py-4">
                                <table class="table table-lg">
                                    <thead>
                                        <tr class="sticky-row">
                                            <th>No</th>
                                            <th>Hari, Tanggal</th>
                                            <th>Nama Santri</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!$dataMurojaah->isEmpty())
                                            @foreach ($dataMurojaah as $row)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $row->hariSetor }}, {{ Carbon::parse($row->tglSetor)->translatedFormat('d F Y') }}</td>
                                                    <td>{{ $row->namaSantri }}</td>
                                                    <td>{{ $row->catatan }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Tidak ada data</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-tahsin-binnadhor" role="tabpanel" aria-labelledby="nav-tahsin-binnadhor-tab">
                            <div class="table-responsive table-wrapper py-4">
                                <table class="table table-lg">
                                    <thead>
                                        <tr class="sticky-row">
                                            <th>No</th>
                                            <th>Hari, Tanggal</th>
                                            <th>Nama Santri</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!$dataTahsinBinnadhor->isEmpty())
                                            @foreach ($dataTahsinBinnadhor as $row)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $row->hariSetor }}, {{ Carbon::parse($row->tglSetor)->translatedFormat('d F Y') }}</td>
                                                    <td>{{ $row->namaSantri }}</td>
                                                    <td>{{ $row->catatan }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Tidak ada data</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-ziyadah" role="tabpanel" aria-labelledby="nav-ziyadah-tab">
                            <div class="table-responsive table-wrapper py-4">
                                <table class="table table-lg">
                                    <thead>
                                        <tr class="sticky-row">
                                            <th>No</th>
                                            <th>Hari, Tanggal</th>
                                            <th>Nama Santri</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!$dataZiyadah->isEmpty())
                                            @foreach ($dataZiyadah as $row)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $row->hariSetor }}, {{ Carbon::parse($row->tglSetor)->translatedFormat('d F Y') }}</td>
                                                    <td>{{ $row->namaSantri }}</td>
                                                    <td>{{ $row->catatan }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Tidak ada data</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
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
        
    });
</script>
@endsection