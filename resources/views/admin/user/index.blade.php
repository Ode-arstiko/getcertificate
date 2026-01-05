<button class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
    + Tambah User
</button>
<div class="card w-100">
    <!-- Modal Tambah User -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addUserModalLabel">Tambah User Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form action="/admin/user/store" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" name="name" class="form-control" placeholder="Masukkan nama"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Masukkan email"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control"
                                    placeholder="Masukkan password" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select">
                                    <option value="receiver">Receiver</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>

                </form>
            </div>
        </div>
    </div>


    <div class="card-body p-4">
        <h5 class="card-title fw-semibold mb-4">Data User</h5>
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif
        <div class="table-responsive">
            <table class="table text-nowrap mb-0 align-middle">
                <thead class="text-dark fs-4">
                    <tr>
                        <th class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">No</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">Nama</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">Email</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">Actions</h6>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $u)
                        <tr>
                            <td class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">{{ $loop->iteration }}</h6>
                            </td>
                            <td class="border-bottom-0">
                                <h6 class="mb-0 fw-normal">{{ $u->name }}</h6>
                            </td>
                            <td class="border-bottom-0">
                                <p class="mb-0 fw-normal">{{ $u->email }}</p>
                            </td>
                            <td class="border-bottom-0">
                                <button class="btn btn-primary mb-0 shadow" data-bs-toggle="modal"
                                    data-bs-target="#editUserModal{{ $u->id }}">
                                    <i class="ti ti-pencil me-2"></i>Edit
                                </button>
                                <!-- Modal Edit User -->
                                <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1"
                                    aria-labelledby="editUserModalLabel{{ $u->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">

                                            <div class="modal-header bg-warning text-white">
                                                <h5 class="modal-title" id="editUserModalLabel{{ $u->id }}">
                                                    Edit User
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <form action="/admin/user/update/{{ $u->id }}" method="POST">
                                                @csrf
                                                @method('PUT')

                                                <div class="modal-body">
                                                    <div class="row">

                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Nama</label>
                                                            <input type="text" name="name" value="{{ $u->name }}"
                                                                class="form-control" required>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Email</label>
                                                            <input type="email" name="email" value="{{ $u->email }}"
                                                                class="form-control" required>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Password (Opsional)</label>
                                                            <input type="password" name="password" class="form-control"
                                                                placeholder="Kosongkan jika tidak ganti">
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Role</label>
                                                            <select name="role" class="form-select">
                                                                <option value="receiver" {{ $u->role == 'receiver' ? 'selected' : '' }}>
                                                                    Receiver
                                                                </option>
                                                                <option value="admin" {{ $u->role == 'admin' ? 'selected' : '' }}>
                                                                    Admin
                                                                </option>
                                                            </select>
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-warning text-white">Update</button>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </td>
                            <td class="border-bottom-0">
                                <form action="/admin/user/delete/{{ $u->id }}" method="POST">
                                    @csrf
                                    @method('delete')
                                    <button onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')"
                                        type="submit" class="btn btn-danger mb-0 shadow"><i
                                            class="ti ti-trash me-2"></i>Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>