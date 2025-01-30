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

    /* .sticky-row {
        top: 0;
    }

    .sticky-2 {
        top: 64px;
    }

    .sticky-3 {
        top: 130px;
    } */

    input[type="date"]:invalid {
        background-color: #f0f0f0;
        color: #aaa;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Ziyadah Chart</h4>
                            </div>
                            <div class="card-body">
                                <div id="bar"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="px-2">
                            <h4>Diagram Ziyadah Hafalan Santri</h4>
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
                                <button type="submit" class="btn btn-sm btn-primary float-end">
                                    <i class="bi bi-file-earmark-ruled-fill"></i>
                                </button>
                                <div class="row mt-4">
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
                            </form>
                            <div id="chart-ziyadah"></div>
                        </div>
                    </div>
                </div>

                {{-- Blangko --}}
                <form id="form-range-waktu">
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
                <div class="col-12">
                    <div class="table-responsive table-wrapper">
                        <table class="table table-lg d-none" id="tabel-blangko">
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
                        <form action="{{ route('dashboard.export-blangko') }}" id="form-cetak-blangko" class="d-none" method="POST">
                            @csrf
                            <input type="hidden" name="tglAwalBlangko" id="tanggal-awal-blangko">
                            <input type="hidden" name="tglAkhirBlangko" id="tanggal-akhir-blangko">
                            <button type="submit" id="btn-cetak-blangko" class="btn icon icon-left btn-success"><i data-feather="check-circle"></i> Cetak</button>
                        </form>
                    </div>
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
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script>
    $(document).ready(function () {
        let chartZiyadah;
        let chartZiyadahKelas;

        let formData = null;
        formData += '&_token=' + $('meta[name="csrf-token"]').attr('content');
        let url = baseUrl + '{{ $url }}' + '/diagram-ziyadah';
        $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function (response) {
                    let optionsDiagramZiyadah  = {
                        series: [response.persentaseTarget, response.persentaseTidakTarget, response.persentaseKhatam],
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

                    function toRoman(num) {
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

                    let classStick = Object.keys(response.dataChart).map(Number).map(toRoman);

                    chartZiyadah = new ApexCharts($('#chart-ziyadah')[0], optionsDiagramZiyadah);
                    chartZiyadah.render();

                    var dataChart = response.dataChart; // Pastikan dataChart adalah objek yang valid
                    var categories = Object.keys(dataChart);

                    var barOptions = {
                        series: [
                            {
                                name: "Target",
                                data: categories.map(key => dataChart[key].totalTarget), // Ambil totalTarget untuk setiap tingkatan
                            },
                            {
                                name: "Tidak Target",
                                data: categories.map(key => dataChart[key].totalTidakTarget), // Ambil totalTidakTarget untuk setiap tingkatan
                            },
                            {
                                name: "Khatam",
                                data: categories.map(key => dataChart[key].totalKhatam), // Ambil totalKhatam untuk setiap tingkatan
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
                            categories: classStick,
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

                    chartZiyadahKelas = new ApexCharts($("#bar")[0], barOptions);
                    chartZiyadahKelas.render();
                    
                    // $('#text-tgl-awal').text(response.txtTglAwal)
                    // $('#text-emote').text('➡️')
                    // $('#text-tgl-akhir').text(response.txtTglAkhir)
                    // $('#tabel-blangko').removeClass('d-none');
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
                    const table = new simpleDatatables.DataTable("#tabel-blangko", {
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
                    $('#text-tgl-awal').text(response.txtTglAwal)
                    $('#text-emote').text('➡️')
                    $('#text-tgl-akhir').text(response.txtTglAkhir)
                    $('#tabel-blangko').removeClass('d-none');
                    
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

        $('#form-diagram-ziyadah').on('submit', function (event) {
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
                    chartZiyadahKelas.destroy();

                    let optionsDiagramZiyadah  = {
                        series: [response.persentaseTarget, response.persentaseTidakTarget, response.persentaseKhatam],
                        labels: ['Target', 'Tidak Target', 'Khatam'],
                        colors: ['#A9C46C','#BE3144', '#5D8736'],
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

                    var dataChart = response.dataChart; // Pastikan dataChart adalah objek yang valid
                    var categories = Object.keys(dataChart);

                    function toRoman(num) {
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

                    let classStick = Object.keys(response.dataChart).map(Number).map(toRoman);

                    var barOptions = {
                        series: [
                            {
                                name: "Target",
                                data: categories.map(key => dataChart[key].totalTarget), // Ambil totalTarget untuk setiap tingkatan
                            },
                            {
                                name: "Tidak Target",
                                data: categories.map(key => dataChart[key].totalTidakTarget), // Ambil totalTidakTarget untuk setiap tingkatan
                            },
                            {
                                name: "Khatam",
                                data: categories.map(key => dataChart[key].totalKhatam), // Ambil totalKhatam untuk setiap tingkatan
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
                            categories: classStick,
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

                    chartZiyadahKelas = new ApexCharts($("#bar")[0], barOptions);
                    chartZiyadahKelas.render();

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