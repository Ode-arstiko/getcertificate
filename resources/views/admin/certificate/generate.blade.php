<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap">

<div id="status" style="font-family: Montserra">Generating certificates...</div>

<canvas id="canvas" width="842" height="595" style="display:none;"></canvas>

<script>
    const data = @json($result);
    const canvas = new fabric.Canvas('canvas');

    let index = 0;
    generateNext();

    function generateNext() {

        if (index >= data.length) {
            document.getElementById('status').innerText = "DONE!";
            return;
        }

        let item = data[index];

        canvas.loadFromJSON(item.json, () => {

            let img = canvas.toDataURL("image/png");

            let formData = new FormData();
            formData.append("image", img);
            formData.append("name", item.name);
            formData.append("sertificate_name", item.sertificate_name);

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
