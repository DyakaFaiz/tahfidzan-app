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
                            <tbody>
                                <input type="hidden" name="idWaktu" value="{{ $waktu->id }}">
                                @foreach ($tahfidzan as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->namaSantri }}</td>
                                        {{-- Ziadah --}}
                                        <td>
                                            <x-select
                                                name="z-j-awal"
                                                id="ziadah-juz-awal-{{ $row->id }}"
                                                :options="$juz"
                                                :selected="$row->ziadah_juz_awal ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>
                                        <td>
                                            <x-select
                                                name="z-p-awal"
                                                id="ziadah-pojok-awal-{{ $row->id }}"
                                                :options="$pojok"
                                                :selected="$row->ziadah_pojok_awal ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>

                                        <td>
                                            <x-select
                                                name="z-j-akhir"
                                                id="ziadah-juz-akhir-{{ $row->id }}"
                                                :options="$juz"
                                                :selected="$row->ziadah_juz_akhir ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>
                                        <td>
                                            <x-select
                                                name="z-p-akhir"
                                                id="ziadah-pojok-akhir-{{ $row->id }}"
                                                :options="$pojok"
                                                :selected="$row->ziadah_pojok_akhir ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>

                                        <td>
                                            <p id="z-jml-{{ $row->id }}" class="fw-bold"></p>
                                        </td>

                                        {{-- Deresan A --}}
                                        <td>
                                            <x-select
                                                name="d-a-j-awal"
                                                id="deresan-a-juz-awal-{{ $row->id }}"
                                                :options="$juz"
                                                :selected="$row->deresan_a_juz_awal ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>
                                        <td>
                                            <x-select
                                                name="d-a-p-awal"
                                                id="deresan-a-pojok-awal-{{ $row->id }}"
                                                :options="$pojok"
                                                :selected="$row->deresan_a_pojok_awal ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>

                                        <td>
                                            <x-select
                                                name="d-a-j-akhir"
                                                id="deresan-a-juz-akhir-{{ $row->id }}"
                                                :options="$juz"
                                                :selected="$row->deresan_a_juz_akhir ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>
                                        <td>
                                            <x-select
                                                name="d-a-p-akhir"
                                                id="deresan-a-pojok-akhir-{{ $row->id }}"
                                                :options="$pojok"
                                                :selected="$row->deresan_a_pojok_akhir ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>

                                        <td>
                                            <p id="d-a-jml-{{ $row->id }}" class="fw-bold"></p>
                                        </td>

                                        {{-- Deresan B --}}
                                        <td>
                                            <x-select
                                                name="d-b-j-awal"
                                                id="deresan-b-juz-awal-{{ $row->id }}"
                                                :options="$juz"
                                                :selected="$row->deresan_b_juz_awal ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>
                                        <td>
                                            <x-select
                                                name="d-b-p-awal"
                                                id="deresan-b-pojok-awal-{{ $row->id }}"
                                                :options="$pojok"
                                                :selected="$row->deresan_b_pojok_awal ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>

                                        <td>
                                            <x-select
                                                name="d-b-j-akhir"
                                                id="deresan-b-juz-akhir-{{ $row->id }}"
                                                :options="$juz"
                                                :selected="$row->deresan_b_juz_akhir ?? 'null'"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>
                                        <td>
                                            <x-select
                                                name="d-b-p-akhir"
                                                id="deresan-b-pojok-akhir-{{ $row->id }}"
                                                :options="$pojok"
                                                :selected="$row->deresan_b_pojok_akhir ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                                style="width: 32px"
                                            />
                                        </td>

                                        <td>
                                            <p id="d-b-jml-{{ $row->id }}" class="fw-bold"></p>
                                        </td>

                                        <td>
                                            <p id="total-deresan-{{ $row->id }}" class="fw-bold"></p>
                                        </td>

                                        <td>
                                            <x-select
                                                name="l-d"
                                                id="level-deresan-{{ $row->id }}"
                                                :options="$levelDeresan"
                                                :selected="$row->level_deresan ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>

                                        <td>
                                            <x-select
                                                name="b-j"
                                                id="bin-nadhor-juz-{{ $row->id }}"
                                                :options="$juz"
                                                :selected="$row->bin_nadhor_juz ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>
                                        <td>
                                            <x-select
                                                name="b-p"
                                                id="bin-nadhor-pojok-{{ $row->id }}"
                                                :options="$pojok"
                                                :selected="$row->bin_nadhor_pojok ?? null"
                                                data-id="{{ $row->id }}"
                                                data-id-ustad="{{ $row->id_ustad }}"
                                                class="inputDropdown"
                                            />
                                        </td>

                                        <td>
                                            <input type="number" class="border-0 inputNumber" data-id="{{ $row->id }}" data-id-ustad="{{ $row->id_ustad }}" name="hdr-ts" id="hdr-ts" value="{{ isset($row->hdr_ts) ? $row->hdr_ts : 0 }}" style="width: 58px">
                                        </td>
                                        <td>
                                            <input type="number" class="border-0 inputNumber" data-id="{{ $row->id }}" data-id-ustad="{{ $row->id_ustad }}" name="hdr-i" id="hdr-i" value="{{ isset($row->hdr_i) ? $row->hdr_i : 0 }}" style="width: 58px">
                                        </td>
                                        <td>
                                            <input type="number" class="border-0 inputNumber" data-id="{{ $row->id }}" data-id-ustad="{{ $row->id_ustad }}" name="hdr-a" id="hdr-a" value="{{ isset($row->hdr_a) ? $row->hdr_a : 0 }}" style="width: 58px">
                                        </td>
                                        <td>
                                            <input type="number" class="border-0 inputNumber" data-id="{{ $row->id }}" data-id-ustad="{{ $row->id_ustad }}" name="hdr-p" id="hdr-p" value="{{ isset($row->hdr_p) ? $row->hdr_p : 0 }}" style="width: 58px">
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
            let jmlZiadah = parseInt($('#ziadah-pojok-awal-' + rowId).val() || 0) +
                            parseInt($('#ziadah-pojok-akhir-' + rowId).val() || 0);
            let jmlDeresanA = parseInt($('#deresan-a-pojok-awal-' + rowId).val() || 0) +
                            parseInt($('#deresan-a-pojok-akhir-' + rowId).val() || 0);
            let jmlDeresanB = parseInt($('#deresan-b-pojok-awal-' + rowId).val() || 0) +
                            parseInt($('#deresan-b-pojok-akhir-' + rowId).val() || 0);
            let totalDeresan = jmlZiadah + jmlDeresanA + jmlDeresanB;

            return { jmlZiadah, jmlDeresanA, jmlDeresanB, totalDeresan };
        }

        function updateDisplayedValues(rowId, values) {
            $('#z-jml-' + rowId).text(values.jmlZiadah);
            $('#d-a-jml-' + rowId).text(values.jmlDeresanA);
            $('#d-b-jml-' + rowId).text(values.jmlDeresanB);
            $('#total-deresan-' + rowId).text(values.totalDeresan);
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

            sendDataToServer(baseUrl + '{{ $url }}' + '/update-value/' + id, data);
        });

        let timeout;
        $(document).on('change', '.inputNumber', function () {
            let id = $(this).data('id');
            let idUstad = $(this).data('id-ustad');
            let idWaktu = parseFloat($('input[name="idWaktu"]').val());

            let data = {
                idTahfidzan: id,
                idUstad: idUstad,
                idWaktu: idWaktu,
                field: $(this).attr('name'),
                value: $(this).val(),
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'PUT',
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