@extends('layout.main')

@section('custom-header-css')
<link rel="stylesheet" href="{{ url('') }}/assets/vendors/simple-datatables/style.css">
<style>
    .table-wrapper {
        max-height: 78vh;
        overflow-y: auto;
        position: relative;
    }
    #tabel-blangko {
        width: 100%;
        table-layout: auto;
    }

    #tabel-blangko th,
    #tabel-blangko td {
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
            <div class="card-header">
                <h4>Rentang Waktu</h4>
            </div>
            <div class="card-body">
                <form id="form-range-waktu">
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
                            <h3 id="text-tgl-awal"></h3>
                        </div>
                        <div class="col-md-4 text-center">
                            <h3 id="text-emote"></h3>
                        </div>
                        <div class="col-md-4 text-center">
                            <h3 id="text-tgl-akhir"></h3>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script>
    $(document).ready(function () {
        $('#form-range-waktu').on('submit', function (event) {
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
                    hideLoading();
                },
                error: function (xhr) {
                    console.log(xhr.responseText)
                    hideLoading();
                },
            });
        });
    });
    // var barOptions = {
    //         series: [
    //             {
    //             name: "Net Profit",
    //             data: [44, 55, 57, 56, 61, 58, 63, 60, 66],
    //             },
    //             {
    //             name: "Revenue",
    //             data: [76, 85, 101, 98, 87, 105, 91, 114, 94],
    //             },
    //             {
    //             name: "Free Cash Flow",
    //             data: [35, 41, 36, 26, 45, 48, 52, 53, 41],
    //             },
    //         ],
    //         chart: {
    //             type: "bar",
    //             height: 350,
    //         },
    //         plotOptions: {
    //             bar: {
    //             horizontal: false,
    //             columnWidth: "55%",
    //             endingShape: "rounded",
    //             },
    //         },
    //         dataLabels: {
    //             enabled: false,
    //         },
    //         stroke: {
    //             show: true,
    //             width: 2,
    //             colors: ["transparent"],
    //         },
    //         xaxis: {
    //             categories: ["Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct"],
    //         },
    //         yaxis: {
    //             title: {
    //             text: "$ (thousands)",
    //             },
    //         },
    //         fill: {
    //             opacity: 1,
    //         },
    //         tooltip: {
    //             y: {
    //             formatter: function(val) {
    //                 return "$ " + val + " thousands";
    //             },
    //             },
    //         },
    //         };

    //     var bar = new ApexCharts(document.querySelector("#bar"), barOptions);
    //     bar.render();
</script>
@endsection