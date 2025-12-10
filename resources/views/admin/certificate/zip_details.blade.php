<div class="card w-100">
    <div class="card-body p-4">
        <div class="d-flex justify-between align-items-center">
            <h5 class="card-title fw-semibold">Certificates {{ $zip->zip_name }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table text-nowrap mb-0 align-middle">
                <thead class="text-dark fs-4">
                    <tr>
                        <th class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">No.</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">Certificate Name</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">Created At</h6>
                        </th>
                        <th colspan="2" class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">Actions</h6>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $cer)
                        <tr>
                            <td class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">{{ $loop->iteration }}</h6>
                            </td>
                            <td class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">
                                    {{ strlen($cer->certificate_name) > 60 ? substr($cer->certificate_name, 0, 60) . '...' : $cer->certificate_name }}
                                </h6>
                            </td>
                            <td class="border-bottom-0">
                                <p class="mb-0 fw-normal">{{ substr($cer->created_at, 0, 10) }}</p>
                            </td>
                            <td class="border-bottom-0">
                                <a href="/download-certificate/{{ encrypt($cer->id) }}" class="btn btn-primary mb-0 shadow"><i
                                        class="ti ti-download me-2"></i>Download PDF</a>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>