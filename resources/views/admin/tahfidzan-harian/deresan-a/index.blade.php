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
            <div class="px-5 mt-5">
                <p>
                    <i class="bi bi-calendar"></i> {{ $hari }}, {{ $tgl }}
                </p>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <!-- Table with outer spacing -->
                    <div class="table-responsive table-wrapper">
                        <table class="table table-lg">
                            <thead>
                                <tr class="sticky-row">
                                    <th rowspan="3">No</th>
                                    <th rowspan="3">Nama</th>

                                    <th colspan="3" class="text-center">Awal</th>
                                    <th colspan="3" class="text-center">Akhir</th>

                                    <th class="text-center" rowspan="2">Kehadiran</th>

                                    <th class="text-center" rowspan="2">Status</th>

                                    <th class="text-center" rowspan="2">Catatan</th>

                                    <th class="text-center" rowspan="2">Jumlah</th>
                                </tr>
                                <tr class="sticky-2">
                                    <th>Surat</th>
                                    <th>Capaian</th>
                                    <th>Juz</th>

                                    <th>Surat</th>
                                    <th>Capaian</th>
                                    <th>Juz</th>
                                </tr>
                            </thead>
                            <tbody>
                                <input type="hidden" name="idWaktu" value="{{ $waktu->id }}">
                                @foreach ($tahfidzan as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->namaSantri }}</td>

                                        {{-- Awal --}}
                                        <td>
                                            <x-select
                                                name="suratAwal"
                                                id="surat-awal-{{ $row->id }}"
                                                :options="$masterSurat"
                                                :selected="$row->id_surat_awal ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>

                                        <td>
                                            <x-select
                                                name="capaianAwal"
                                                id="capaian-awal-{{ $row->id }}"
                                                :options="$pojok"
                                                :selected="$row->capaian_awal ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>
                                        
                                        <td>
                                            <x-select
                                                name="juzAwal"
                                                id="juz-awal-{{ $row->id }}"
                                                :options="$juz"
                                                :selected="$row->juz_awal ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>

                                        {{-- Akhir --}}
                                        <td>
                                            <x-select
                                                name="suratAkhir"
                                                id="surat-akhir-{{ $row->id }}"
                                                :options="$masterSurat"
                                                :selected="$row->id_surat_akhir ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>

                                        <td>
                                            <x-select
                                                name="capaianAkhir"
                                                id="capaian-akhir-{{ $row->id }}"
                                                :options="$pojok"
                                                :selected="$row->capaian_akhir ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>
                                        
                                        <td>
                                            <x-select
                                                name="juzAkhir"
                                                id="juz-akhir-{{ $row->id }}"
                                                :options="$juz"
                                                :selected="$row->juz_akhir ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>

                                        {{-- Kehadiran --}}
                                        <td>
                                            <x-select
                                                name="kehadiran"
                                                id="kehadiran-{{ $row->id }}"
                                                :options="$kehadiran"
                                                :selected="$row->kehadiran ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>

                                        {{-- Status --}}
                                        <td>
                                            <x-select
                                                name="status"
                                                id="status-{{ $row->id }}"
                                                :options="$status"
                                                :selected="$row->status ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>
                                        
                                        <td>
                                            <input type="text" class="border-0 w-auto inputText" data-id="{{ $row->id }}" data-id-ustad="{{ $row->id_ustad }}" name="catatanDeresanA" id="catatan-deresan-a" value="{{ isset($row->catatan) ? $row->catatan : '' }}">
                                        </td>

                                        <td>
                                            <p id="jml-{{ $row->id }}"></p>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
        function calculateValues(rowId) {
            if (!$('#capaian-awal-' + rowId).length || !$('#capaian-akhir-' + rowId).length) {
                console.error("Elemen input tidak ditemukan untuk rowId:", rowId);
                return { jmlDeresanA: 0 };
            }

            let capaianAwal = parseInt($('#capaian-awal-' + rowId).val()) || 0;
            let capaianAkhir = parseInt($('#capaian-akhir-' + rowId).val()) || 0;

            let jmlDeresanA = capaianAwal + capaianAkhir;

            return { jmlDeresanA };
        }

        function updateDisplayedValues(rowId, values) {
            $('#jml-' + rowId).text(values.jmlDeresanA);
        }

        function sendDataToServer(url, data) {
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                success: function (response) {
                    Toastify({
                        text: response.message || 'Data berhasil diperbarui.',
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#4fbe87",
                    }).showToast();
                },
                error: function (xhr) {
                    console.log(xhr);
                }
            });
        }

        function jml() {
            let tahfidzan = @json($tahfidzan);
            tahfidzan.forEach(row => {
                let values = calculateValues(row.id);
                updateDisplayedValues(row.id, values);
            });
        }

        jml();

        $(document).on('change', '.inputDropdown', function () {
            let id = $(this).data('id');
            let idUstad = $(this).data('id-ustad');
            let idWaktu = parseFloat($('input[name="idWaktu"]').val());

            let values = calculateValues(id);
            updateDisplayedValues(id, values);

            let data = {
                idTahfidzan: id,
                idUstad: idUstad,
                idWaktu: idWaktu,
                field: $(this).attr('name'),
                value: $(this).val(),
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'PUT',
                ...values
            };

            if($(this).val() != '-'){
                sendDataToServer(baseUrl + '{{ $url }}' + '/update-value/' + id, data);
            }  
        });

        let timeout;
        $(document).on('change', '.inputText', function () {
            let id = $(this).data('id');
            let idUstad = $(this).data('id-ustad');
            let idWaktu = parseFloat($('input[name="idWaktu"]').val());

            let values = calculateValues(id);

            let data = {
                idTahfidzan: id,
                idUstad: idUstad,
                idWaktu: idWaktu,
                field: $(this).attr('name'),
                value: $(this).val(),
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'PUT',
                ...values
            };

            clearTimeout(timeout);
            timeout = setTimeout(() => {
                sendDataToServer(baseUrl + '{{ $url }}' + '/update-value/' + id, data);
            }, 1000);

            jml();
        });
    });
</script>
@endsection