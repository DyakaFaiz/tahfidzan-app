<div class="modal fade text-left modal-borderless" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Santri</h5>
                <button type="button" class="close rounded-pill"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="col-12 text-end mb-2">
                                    <button id="btn-delete" data-id="" class="btn btn-sm btn-danger" type="submit" title="Hapus Santri"><i class="bi bi-trash2-fill"></i></button>
                                </div>
                                <form class="form form-horizontal" id="form-edit">
                                    @csrf
                                    <input type="hidden" name="idSantri" id="idEdit">
                                    <div class="form-body">
                                        <div class="row">

                                            <div class="col-md-4">
                                                <label>Nama</label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <input type="text" id="nama" class="form-control"
                                                    name="nama" placeholder="Nama">
                                            </div>

                                            <div class="col-md-4">
                                                <label>Kelas</label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <select class="choices form-select" id="kelas">
                                                    @foreach ($masterKelas as $row)
                                                        <option value="{{ $row->id }}">{{ $row->kelas }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label>Status</label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <select class="choices form-select" id="status">
                                                    <option value="0">BOYONG</option>
                                                    <option value="1">MASIH ZIYADAH</option>
                                                    <option value="2">Khatam</option>
                                                    <option value="3">Khotimin</option>
                                                </select>
                                            </div>

                                            <div class="col-sm-12 d-flex justify-content-end">
                                                <button type="submit"
                                                    class="btn btn-primary me-1 mb-1">Submit</button>
                                                <button data-bs-dismiss="modal"
                                                    class="btn btn-light-secondary me-1 mb-1">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left modal-borderless" id="modal-tambah-santri" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Santri</h5>
                <button type="button" class="close rounded-pill"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <form class="form form-horizontal" action="{{ route('santri.store') }}" method="POST" id="form-tambah">
                                    @csrf
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Nama</label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <input type="text" id="tambah-nama" class="form-control"
                                                    name="nama" placeholder="Nama">
                                            </div>

                                            <div class="col-md-4">
                                                <label>Kelas</label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <input type="text" id="tambah-kelas" class="form-control"
                                                    name="kelas" placeholder="Kelas">
                                            </div>

                                            <div class="col-md-4">
                                                <label>Target Pojok</label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <input type="text" id="tambah-trgt-pjk" class="form-control"
                                                    name="targetPojok" placeholder="Target Pojok">
                                            </div>

                                            <div class="col-md-4">
                                                <label>Target Lembaga</label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <input type="text" id="tambah-trgt-lbg" class="form-control"
                                                    name="targetLembaga" placeholder="Target Lembaga">
                                            </div>

                                            <div class="col-sm-12 d-flex justify-content-end">
                                                <button type="submit"
                                                    class="btn btn-primary me-1 mb-1">Submit</button>
                                                <button data-bs-dismiss="modal"
                                                    class="btn btn-light-secondary me-1 mb-1">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>