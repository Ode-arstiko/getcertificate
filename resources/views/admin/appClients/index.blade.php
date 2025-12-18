<button class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#createTokenModal">
    <i class="ti ti-plus me-2"></i>Add new app
</button>
<div id="alertContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="alert" class="alert alert-success alert-dismissible fade" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        Copied to clipboard.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<div class="card w-100">
    <!-- Modal Tambah User -->
    <div class="modal fade" id="createTokenModal" tabindex="-1" aria-labelledby="createTokenModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="createTokenModalLabel">Add new application</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form action="/admin/clients-app/store" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row p-2">
                            <label class="form-label">Application Name</label>
                            <input type="text" name="name" class="form-control"
                                placeholder="Input name of the application" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Store</button>
                    </div>

                </form>
            </div>
        </div>
    </div>


    <div class="card-body p-4">
        <h5 class="card-title fw-semibold mb-4">Clients App List</h5>
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
                            <h6 class="fw-semibold mb-0">App Name</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">App ID</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">App Secret</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">Actions</h6>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($appClients as $app)
                        <tr>
                            <td class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">{{ $loop->iteration }}</h6>
                            </td>
                            <td class="border-bottom-0">
                                <h6 class="mb-0 fw-normal">{{ $app->name }}</h6>
                            </td>
                            <td class="border-bottom-0">
                                <input type="text" name="" id="appId{{ $app->id }}"
                                    value="{{ $app->app_id }}" hidden>
                                <button type="button" class="btn btn-primary"
                                    onclick="copyId({{ $app->id }})">Copy
                                    App ID</button>
                            </td>
                            <td class="border-bottom-0">
                                <input type="text" name="" id="appSecret{{ $app->id }}"
                                    value="{{ $app->app_secret }}" hidden>
                                <button type="button" class="btn btn-primary"
                                    onclick="copySecret({{ $app->id }})">Copy
                                    App Secret</button>
                            </td>
                            <td class="border-bottom-0">
                                <form action="/admin/clients-app/delete/{{ encrypt($app->id) }}" method="POST">
                                    @csrf
                                    @method('delete')
                                    <button onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')"
                                        type="submit" class="btn btn-danger mb-0 shadow"><i
                                            class="ti ti-trash me-2"></i>Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function copyId(id) {
        const ids = document.getElementById('appId' + id).value; // Perbaikan: '+' bukan '.'

        navigator.clipboard.writeText(ids);
    }
    function copySecret(id2) {
        const secret = document.getElementById('appSecret' + id2).value; // Perbaikan: '+' bukan '.'

        navigator.clipboard.writeText(secret);
    }
</script>
