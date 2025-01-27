@extends('layout.main')

@section('custom-header-css')
{{-- Choices Style --}}
<link rel="stylesheet" href="{{ url('') }}/assets/vendors/choices.js/choices.min.css" />
@endsection

@section('content')
<div class="card">
    <div class="card-content">
        <div class="card-header">
            <div class="col-6">
                <p>
                    Tambah Santri
                </p>
                <div class="form-group">
                    <select class="choices form-select" id="select-santri">
                        @if (!$santri->isEmpty())
                            <option value="#" selected>Pilih Santri</option>
                            @foreach ($santri as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        @else
                            <option value="" disabled selected>Data tidak tersedia</option>
                        @endif
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Table with outer spacing -->
            <div class="table-responsive">
                <table class="table table-lg">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>Santri</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $row->namaSantri }}</td>
                                <td><i class="bi bi-x-octagon-fill text-danger cursor-pointer btn-delete" data-id="{{ $row->id }}"></i></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
{{-- Select Choices JS --}}
<script src="{{ url('') }}/assets/vendors/choices.js/choices.min.js"></script>

<script>
    $(document).ready(function (){
        $('#select-santri').on('change', function() {
            var santriId = $(this).val();
            var idUstad = "{{ $idUstad }}";

            // Pastikan hanya mengirimkan data jika id yang dipilih bukan '#'
            if (santriId && santriId != '#') {
                $.ajax({
                    url: '{{ route("ketahfidzan.ustad-tahfidz.store-santri-ketahfidzan") }}', // URL route yang sesuai
                    method: 'POST',
                    data: {
                        idSantri: santriId,
                        idUstad: idUstad, 
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        Toastify({
                            text: response.message || 'Data berhasil diperbarui.',
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#4fbe87",
                        }).showToast();

                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    },
                    error: function(xhr, status, error) {
                        Toastify({
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan!',
                        duration: 10000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#F72C5B",
                    }).showToast();
                    }
                });
            }
        });
        
        $('.btn-delete').on('click', function(){
                let idKetahfidzan = $(this).data('id');
                var idUstad = "{{ $idUstad }}";
                let url = `{{ route('ketahfidzan.ustad-tahfidz.delete', ['id' => ':id']) }}`.replace(':id', idKetahfidzan);
                
                
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'Anda akan menghapus santri dari daftar ketahdfidzan ustad',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url, 
                            method: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}",
                                idUstad : idUstad,
                            },
                            success: function(response) {
                                Toastify({
                                    text: response.message || 'Data berhasil dihapus.',
                                    duration: 3000,
                                    close: true,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "#4fbe87",
                                }).showToast();


                                setTimeout(function() {
                                    location.reload();
                                }, 3000);
                            },
                            error: function(xhr, status, error) {
                                Toastify({
                                    text: xhr.responseJSON?.message || 'Terjadi kesalahan!',
                                    duration: 10000,
                                    close: true,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "#F72C5B",
                                }).showToast();
                                
                                setTimeout(function() {
                                    location.reload();
                                }, 10000);
                            }
                        });
                    }else{
                        console.log('canceled')
                    }
                });
        });
    });
</script>
@endsection