<div class="card w-100">
    <div class="card-body p-4">
        <div class="d-flex justify-between align-items-center">
            <h5 class="card-title fw-semibold">Zipped Certificates</h5>
            <button type="button" class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <i class="ti ti-plus me-2"></i>Make Certificate
            </button>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Select Template</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <table class="table text-nowrap mb-0 align-middle">
                                <thead class="text-dark fs-4">
                                    <tr>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">No.</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Template Name</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Actions</h6>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($template as $temp)
                                        <tr>
                                            <td class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">{{ $loop->iteration }}</h6>
                                            </td>
                                            <td class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">
                                                    {{ strlen($temp->zip_name) > 60 ? substr($temp->template_name, 0, 60) . '...' : $temp->template_name }}
                                                </h6>
                                            </td>
                                            <td class="border-bottom-0">
                                                <a href="/admin/certificate/create/{{ encrypt($temp->id) }}"
                                                    class="btn btn-primary mb-0 shadow"><i
                                                        class="ti ti-send me-2"></i>Select</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table text-nowrap mb-0 align-middle">
                <thead class="text-dark fs-4">
                    <tr>
                        <th class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">No.</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">Zip Name</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">Created At</h6>
                        </th>
                        <th colspan="3" class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">Actions</h6>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($zips as $zip)
                        <tr>
                            <td class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">{{ $loop->iteration }}</h6>
                            </td>
                            <td class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">
                                    {{ strlen($zip->zip_name) > 30 ? substr($zip->zip_name, 0, 30) . '...' : $zip->zip_name }}
                                </h6>
                            </td>
                            <td class="border-bottom-0">
                                <p class="mb-0 fw-normal">{{ substr($zip->created_at, 0, 10) }}</p>
                            </td>
                            <td class="border-bottom-0">
                                <a href="/admin/certificate/detail/{{ encrypt($zip->id) }}"
                                    class="btn btn-primary mb-0 shadow"><i class="ti ti-pencil me-2"></i>Details</a>
                            </td>
                            <td class="border-bottom-0">
                                <form action="/admin/certificate/delete/{{ encrypt($zip->id) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-danger" type="submit"><i class="ti ti-trash me-2"></i>Delete</button>
                                </form>
                            </td>
                            <td class="border-bottom-0">
                                <a href="/downlaod-certificate-zip/{{ encrypt($zip->id) }}"
                                    class="btn btn-success mb-0 shadow"><i class="ti ti-download me-2"></i>Download
                                    Zip</a>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
