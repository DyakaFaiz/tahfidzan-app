<div class="modal fade text-left modal-borderless" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
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
                                    <button id="btn-delete" data-id="" class="btn btn-sm btn-danger" type="submit" title="Hapus User"><i class="bi bi-trash2-fill"></i></button>
                                </div>
                                <form class="form form-horizontal" id="form-edit">
                                    @csrf
                                    <input type="hidden" name="idUser" id="idEdit">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Username</label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <input type="text" id="username-edit" class="form-control"
                                                    name="username" placeholder="Username">
                                            </div>
                                            <div class="col-md-4">
                                                <label>Nama</label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <input type="text" id="nama-edit" class="form-control"
                                                    name="nama" placeholder="Nama">
                                            </div>
                                            <div class="col-md-4">
                                                <label>Password</label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <input type="password" id="password-edit" class="form-control"
                                                    name="password" placeholder="Password">
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

<div class="modal fade text-left modal-borderless" id="modal-tambah-user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah User</h5>
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
                                <form class="form form-horizontal" action="{{ route('user.store') }}" method="POST" id="form-tambah">
                                    @csrf
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Username</label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <input type="text" id="tambah-username" class="form-control"
                                                    name="username" placeholder="Username">
                                            </div>
                                            <div class="col-md-4">
                                                <label>Nama</label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <input type="text" id="tambah-nama" class="form-control"
                                                    name="nama" placeholder="Nama">
                                            </div>
                                            <div class="col-md-4">
                                                <label>Password</label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <input type="password" id="tambah-password" class="form-control"
                                                    name="password" placeholder="Password">
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