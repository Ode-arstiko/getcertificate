<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Loading...</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .page-loader {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            z-index: 9999;
        }

        .loader-text {
            margin-top: .75rem;
            font-weight: 600;
            color: #495057;
        }
    </style>
</head>

<body>
    <div id="pageLoader" class="page-loader">
        <div class="text-center">
            <div class="spinner-border" role="status" style="width:4rem; height:4rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="loader-text">Generating, We will send a massage to your email when the process is done...</div>
        </div>
    </div>
</body>

<canvas id="canvas" width="842" height="595" style="display:none;"></canvas>

</html>

<script>
    const data = @json($result);
    const canvas = new fabric.Canvas('canvas');

    let index = 0;
    generateNext();

    function generateNext() {

        if (index >= data.length) {
            window.location.href="/admin/certificate";
        }

        let item = data[index];

        canvas.loadFromJSON(item.json, () => {

            let img = canvas.toDataURL("image/png");

            let formData = new FormData();
            formData.append("image", img);
            formData.append("name", item.name);
            formData.append("sertificate_name", item.sertificate_name);
            formData.append("zip_id", item.zip_id);

            fetch("{{ url('admin/certificate/save') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: formData
                })
                .then(res => res.text())
                .then(res => {
                    console.log("Saved", item.name);
                    index++;
                    generateNext();
                })
                .catch(err => console.error(err));
        });
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
