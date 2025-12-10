<div class="card w-100">
    <div class="card-body p-4">
        <div class="d-flex justify-between align-items-center">
            <h5 class="card-title fw-semibold">Certificate Templates</h5>
            <a href="/admin/ctemplate/create" class="btn btn-primary shadow ms-3"><i class="ti ti-plus me-2"></i> Make Template</a>
        </div>
        <div class="table-responsive">
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
                            <h6 class="fw-semibold mb-0">Created At</h6>
                        </th>
                        <th colspan="2" class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">Actions</h6>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ctemplate as $tmp)
                    <tr>
                        <td class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">{{ $loop->iteration }}</h6>
                        </td>
                        <td class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">{{ strlen($tmp->template_name) > 60 ? substr($tmp->template_name, 0, 60) . '...' : $tmp->template_name }}</h6>
                        </td>
                        <td class="border-bottom-0">
                            <p class="mb-0 fw-normal">{{ substr($tmp->created_at, 0, 10) }}</p>
                        </td>
                        <td class="border-bottom-0">
                            <a href="" class="btn btn-primary mb-0 shadow"><i class="ti ti-pencil me-2"></i>Edit</a>
                        </td>
                        <td class="border-bottom-0">
                            <form action="/admin/ctemplate/delete/{{ encrypt($tmp->id) }}" method="POST">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn btn-danger mb-0 shadow"><i class="ti ti-trash me-2"></i>Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty

                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
