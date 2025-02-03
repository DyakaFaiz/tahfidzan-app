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
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col">
        <div class="">
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
                        stroke: {
                            show: true,
                            width: 2,
                            colors: ["transparent"],
                        },
                        xaxis: {
                            categories: graphContentZiyadah,
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