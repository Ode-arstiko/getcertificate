<div class="card-body">
    <h5 class="card-title fw-semibold mb-4"></h5>
    <div class="card">
        <div class="card-body">
            <h3 class="form-label mb-5">Make Certificate</h3>
            <form action="/admin/certificate/store" method="POST">
                @csrf
                <input type="text" value="{{ $template_id }}" name="template_id" id="" hidden>
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Input Name</label>
                    <textarea name="nama" class="form-control" id="" cols="30" rows="10"
                        placeholder="*example
John Doe
Jane Doe
Mark Marchus">
</textarea>
                </div>
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">As a</label>
                    <textarea name="juara" class="form-control" id="" cols="30" rows="10"
                        placeholder="*example
Champion number 1
Champion number 2
Champion number 3">
</textarea>
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary shadow mt-3">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
