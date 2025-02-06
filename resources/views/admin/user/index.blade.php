@extends('layout.main')

@section('custom-header-css')
    <link rel="stylesheet" href="{{ url('') }}/assets/vendors/simple-datatables/style.css">
    <!-- CSS tambahan untuk penyesuaian tampilan tabel di perangkat mobile -->
    <style>
        @media (max-width: 767px) {
            /* Mengurangi ukuran font tabel untuk layar kecil */
            #table-1 {
                font-size: 12px;
            }
            /* Jika perlu, Anda bisa mengurangi padding sel juga */
            #table-1 td, #table-1 th {
                padding: 0.5rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="text-end p-3">
                <button id="btn-tambah-user" data-bs-toggle='modal' data-bs-target='#modal-tambah-user' class="btn btn-primary"><i class="bi bi-person-plus"></i> Tambah User</button>
            </div>
            <div class="table-responsive">
                    <table class="table table-lg" id="table-1">
                        {{-- DataTable --}}
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('admin.user.modal')
@endsection

@section('custom-js')
<script>
    $(document).ready(function () {
        fetch(baseUrl + '{{ $url }}' + '/get-data')
            .then(response => response.json())
            .then(data => {
                const table = new simpleDatatables.DataTable("#table-1", {
                    data: {
                        headings: ["No", "Nama", "Username", "Role", "Aksi"],
                        data: data.data,
                    },
                });
            })
            .catch(error => console.error("Error fetching data:", error));


        $('#table-1').on('click', '#btn-input-password', async function () {
            const id = $(this).data('id');

            const { value: password } = await Swal.fire({
                title: 'Masukkan Password User',
                input: 'password',
                inputLabel: 'Password',
                inputPlaceholder: 'Password',
                inputAttributes: {
                    maxlength: 10,
                    autocapitalize: 'off',
                    autocorrect: 'off',
                },
                showCancelButton: true, // Tambahkan tombol batal
            });

            if (password) {
                $.ajax({
                    url: baseUrl + '{{ $url }}' + '/store-password', // Ganti dengan endpoint Laravel Anda
                    method: 'POST',
                    data: {
                        id: id,
                        password: password,
                        _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token
                    },
                    success: function (response) {
                        Toastify({
                            text: response.message || 'Password sent successfully!',
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#4fbe87",
                        }).showToast();

                        setTimeout(function() {
                            location.reload();
                        }, 4000); 
                    },
                    error: function (xhr) {
                        Toastify({
                            text: xhr.responseJSON?.message || 'Something went wrong!',
                            duration: 10000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#F72C5B",
                        }).showToast();
                    },
                });
            }
        });

        $('#table-1').on('click', '#btn-edit', function () {
            const id = $(this).data('id');

            // untuk button delete
            $('#btn-delete').data('id', id);

            $('#id-delete').val(id);

            $('#username').val('');
            $('#nama').val('');
            $('#idEdit').val('');

            $.ajax({
                url: baseUrl + '{{ $url }}'+ '/edit/' +  id,
                method: 'GET',
                success: function (response) {
                    $('#idEdit').val(response.id);
                    $('#username-edit').val(response.username);
                    $('#nama-edit').val(response.nama);
                },
                error: function (xhr, status, error) {
                    Toastify({
                        text: 'Failed to load data, please try again.',
                        duration: 5000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#F72C5B",
                    }).showToast();
                },
            });

            $('#username').val('');
            $('#nama').val('');
            $('#idEdit').val('');
        });

        $('#form-edit').on('submit', function(event) {
            event.preventDefault();

            let id = $('#idEdit').val();
            let formData = {
                _method: 'PUT',
                _token: '{{ csrf_token() }}',
                nama: $('#nama-edit').val(),
                username: $('#username-edit').val(),
                password: $('#password-edit').val(),
            };

            $.ajax({
                url: baseUrl + '{{ $url }}' +  '/update/' + id,
                type: 'POST',
                data: formData,
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
                    }, 2000); 
                },
                error: function(xhr) {
                    Toastify({
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan!',
                        duration: 10000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#F72C5B",
                    }).showToast();
                },
            });
            $('#username').val('');
            $('#nama').val('');
            $('#idEdit').val('');
        });

        $('#btn-delete').on('click', function(){
            let id = $(this).data('id');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('user.delete', '') }}/" + id, 
                        method: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id
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

                            $('#modal-edit').modal('hide');

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
                            $('#modal-edit').modal('hide');
                        }
                    });
                }
            });
            $('#username').val('');
            $('#nama').val('');
            $('#idEdit').val('');
        });

        $('#btn-tambah-user').on('click', function(){
            $('#tambah-username').val('');
            $('#tambah-nama').val('');
            $('#tambah-password').val('');
        });
    });
</script>
@endsection