@include('layouts.admin.head')
<div class="card-body">
    <h5 class="card-title fw-semibold mb-4"></h5>
    <div class="card">
        <div class="card-body">
            <form action="/admin/ctemplate/store" method="POST" id="form">
                @csrf
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Certificate Name</label>
                    <input type="text" name="template_name" class="form-control" id="template_name">
                    <input type="text" name="elements" id="elements" value="" hidden>
                </div>
                <div class="mb-2">
                    <label class="form-label mt-3">Make ur own template here</label>
                </div>
                <div class="mb-4">
                    <button type="button" class="btn btn-primary me-2" onclick="addNewText()"><i
                            class="ti ti-plus me-2"></i>Add text</button>

                    <input type="file" onchange="uploadImage(event)" name="" class="hidden-file"
                        id="imgUpload" hidden>
                    <button type="button" id="uploadButton" class="btn btn-primary me-2"><i
                            class="ti ti-plus me-2"></i>Add
                        image</button>

                    <input type="file" onchange="changeBackground(event)" class="hidden-bg" name=""
                        id="bgUpload" hidden>
                    <button type="button" id="bgButton" class="btn btn-primary"><i
                            class="ti ti-refresh me-2"></i>Change
                        background</button>
                </div>
                <div class="mb-2 d-flex justify-between">
                    <div class="me-4">
                        <label for="" class="">Font Size</label>
                        <select name="" class="form-control" style="width: 100px;"
                            onchange="changeFontSize(this.value)" id="fontSizeSelect">
                            <option id="8" value="8">8</option>
                            <option id="10" value="10">10</option>
                            <option id="12" value="12">12</option>
                            <option id="14" value="14">14</option>
                            <option id="16" value="16">16</option>
                            <option id="18" value="18">18</option>
                            <option id="20" value="20">20</option>
                            <option id="24" value="24">24</option>
                            <option id="28" value="28">28</option>
                            <option id="32" value="32">32</option>
                            <option id="36" value="36">36</option>
                            <option id="48" value="48">48</option>
                            <option id="72" value="72">72</option>
                        </select>
                    </div>
                    <div class="me-4">
                        <label for="" class="">Font Family</label>
                        <select name="" class="form-control" style="width: 150px;"
                            onchange="changeFontFamily(this.value)" id="fontFamilySelect">
                            <option value="Sans-serif" id="sansserif">Sans Serif</option>
                            <option value="Montserrat" id="montserrat">Montserrat</option>
                            <option value="Great Vibes" id="greatvibes">Great Vibes</option>
                        </select>
                    </div>
                    <div class="me-4">
                        <label for="" class="">Text Align</label>
                        <div class="mt-2">
                            <button type="button" onclick="changeAlignLeft()" class="btn btn-primary"><i
                                    class="ti ti-align-left"></i></button>
                            <button type="button" onclick="changeAlignCenter()" class="btn btn-primary"><i
                                    class="ti ti-align-center"></i></button>
                            <button type="button" onclick="changeAlignRight()" class="btn btn-primary"><i
                                    class="ti ti-align-right"></i></button>
                        </div>
                    </div>
                    <div class="me-4">
                        <label for="">Properties</label>
                        <div class="">
                            <button type="button" onclick="fontBold()" class="btn btn-primary"><b>B</b></button>
                            <button type="button" onclick="fontItalic()" class="btn btn-primary"><i
                                    style="font-family: monospace;">I</i></button>
                            <button type="button" onclick="fontUnderline()"
                                class="btn btn-primary"><u>U</u></button>
                        </div>
                    </div>
                    <div>
                        <label for="">Font Color</label>
                        <input type="color" class="form-control form-control-color"
                            oninput="changeFontColor(this.value)" name="" id="colorPicker">
                    </div>
                </div>
                <div class="mb-3">
                    <canvas class="border border-1 border-dark rounded shadow-sm" id="c" height="595"
                        width="842"></canvas>
                    <button type="button" onclick="saveTemplate()"
                        class="btn btn-primary shadow mt-3">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const canvas = new fabric.Canvas('c');
    const colorPicker = document.getElementById('colorPicker');
    const uploadButton = document.getElementById('uploadButton');
    const bgButton = document.getElementById('bgButton');
    let txtpositionTop = 30;
    let txtpositionLeft = 30;
    let undoStack = [];
    let redoStack = [];
    let isLoading = false;

    function saveState() {
        if (isLoading) return;

        undoStack.push(canvas.toJSON());
        redoStack = [];
        // console.log("STATE SAVED: ", undoStack.length);
    }

    function undo() {
        if (undoStack.length < 2) return;

        isLoading = true;

        redoStack.push(undoStack.pop());

        const prevJSON = undoStack[undoStack.length - 1];

        canvas.loadFromJSON(prevJSON, () => {
            canvas.renderAll();
            isLoading = false;
        });
    }

    function redo() {
        if (redoStack.length === 0) return;

        isLoading = true;

        const state = redoStack.pop();
        undoStack.push(state);

        canvas.loadFromJSON(state, () => {
            canvas.renderAll();
            isLoading = false;
        });
    }

    canvas.on('object:added', function() {
        // if (!even.target._restoreState) return;
        saveState();
    });

    canvas.on('object:modified', function() {
        saveState();
    });

    canvas.on('object:removed', function() {
        saveState();
    });

    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'z') {
            // console.log("UNDO BERHASIL");
            undo();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'y') {
            // console.log("REDO BERHASIL");
            redo();
        }
    })

    function addNewText() {
        const newtext = new fabric.Textbox('New text', {
            left: txtpositionLeft,
            top: txtpositionTop,
            width: 150,
            fontSize: 32,
            fill: '#000000',
            fontFamily: 'Montserrat',
            borderColor: 'blue',
            cornerColor: 'blue',
            cornerSize: 8
        });

        canvas.add(newtext);
        canvas.setActiveObject(newtext);
        canvas.renderAll();
        if (txtpositionTop == 200) {
            txtpositionTop = 30;
            txtpositionLeft = txtpositionLeft + 10;
        } else {
            txtpositionTop = txtpositionTop + 10;
            txtpositionLeft = txtpositionLeft + 10;
        }
    }

    function changeFontSize(size) {
        const active = canvas.getActiveObject();
        if (active && active.type === 'textbox') {
            active.fontSize = size;
            canvas.renderAll();
        }
    }

    function changeFontFamily(font) {
        const active = canvas.getActiveObject();
        if (active && active.type === 'textbox') {
            active.set('fontFamily', font);
            canvas.renderAll();
        }
    }

    function changeAlignLeft() {
        const active = canvas.getActiveObject();
        if (active && active.type === 'textbox') {
            active.textAlign = 'left';
            canvas.renderAll();
        }
    }

    function changeAlignCenter() {
        const active = canvas.getActiveObject();
        if (active && active.type === 'textbox') {
            active.textAlign = 'center';
            canvas.renderAll();
        }
    }

    function changeAlignRight() {
        const active = canvas.getActiveObject();
        if (active && active.type === 'textbox') {
            active.textAlign = 'right';
            canvas.renderAll();
        }
    }

    function fontBold() {
        const active = canvas.getActiveObject();
        if (active && active.type === 'textbox') {
            if (active.fontWeight == 'bold') {
                active.fontWeight = 'normal';
                canvas.renderAll();
            } else {
                active.fontWeight = 'bold';
                canvas.renderAll();
            }
        }
    }

    function fontItalic() {
        const active = canvas.getActiveObject();
        if (active && active.type === 'textbox') {
            if (active.fontStyle == 'italic') {
                active.fontStyle = 'normal';
                canvas.renderAll();
            } else {
                active.fontStyle = 'italic';
                canvas.renderAll();
            }
        }
    }

    function fontUnderline() {
        const active = canvas.getActiveObject();
        if (active && active.type === 'textbox') {
            if (active.underline == true) {
                active.set('underline', false);
                canvas.renderAll();
            } else {
                active.set('underline', true);
                canvas.renderAll();
            }
        }
    }

    colorPicker.addEventListener('input', function() {
        const active = canvas.getActiveObject();

        if (active && active.type === 'textbox') {
            active.set('fill', this.value);
            canvas.renderAll();
        }
    });

    function updateColorPicker(color) {
        let hexColor = color;

        if (color && !color.startsWith('#')) {
            if (color.startsWith('rgb')) {
                const rgb = color.match(/\d+/g);
                if (rgb) {
                    const r = (+rgb[0]).toString(16).padStart(2, '0');
                    const g = (+rgb[1]).toString(16).padStart(2, '0');
                    const b = (+rgb[2]).toString(16).padStart(2, '0');
                    hexColor = `#${r}${g}${b}`;
                }
            }
        }

        colorPicker.value = hexColor;
    }

    canvas.on('selection:created', function(e) {
        const selectedObj = e.selected[0];

        if (selectedObj && selectedObj.type === 'textbox') {
            const textColor = selectedObj.fill;
            const fontSizeSlc = selectedObj.fontSize;
            const fontFamilySlc = selectedObj.fontFamily

            document.getElementById('fontSizeSelect').value = fontSizeSlc;
            document.getElementById('fontFamilySelect').value = fontFamilySlc;
            updateColorPicker(textColor);
        }
    });

    canvas.on('selection:updated', function(e) {
        const selectedObj = e.selected[0];

        if (selectedObj && selectedObj.type === 'textbox') {
            const textColor = selectedObj.fill;
            const fontSizeSlc = selectedObj.fontSize;
            const fontFamilySlc = selectedObj.fontFamily

            document.getElementById('fontSizeSelect').value = fontSizeSlc;
            document.getElementById('fontFamilySelect').value = fontFamilySlc;
            updateColorPicker(textColor);
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.keyCode === 46) {
            const active = canvas.getActiveObject();

            if (active) {
                canvas.remove(active);
                canvas.renderAll();
            }
        }
    })

    uploadButton.addEventListener('click', function() {
        document.querySelector('.hidden-file').click();
    })

    async function uploadImage(e) {
        let file = e.target.files[0];
        if (!file) return;

        // 1. Upload file ke server DULU
        const imageUrl = await uploadToServer(file);

        // 2. Setelah dapat URL dari server, baru tambah ke canvas
        fabric.Image.fromURL(imageUrl, function(img) {
            img.set({
                left: 100,
                top: 100,
                scaleX: 0.3,
                scaleY: 0.3,
                cornerColor: 'blue',
                cornerSize: 8
            });

            canvas.add(img);
            canvas.setActiveObject(img);
            canvas.renderAll();
        });

        // Reset input
        e.target.value = '';
    }

    bgButton.addEventListener('click', function() {
        document.querySelector('.hidden-bg').click();
    })

    async function changeBackground(e) {
        let file = e.target.files[0];
        if (!file) return;

        try {
            // 1. Upload ke server dulu
            const imageUrl = await uploadToServer(file);

            // 2. Set background dengan URL dari server
            fabric.Image.fromURL(imageUrl, function(img) {
                // Sesuaikan ukuran gambar dengan canvas
                img.scaleToWidth(canvas.width);
                img.scaleToHeight(canvas.height);

                // Set sebagai background
                canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));

                console.log('Background berhasil diubah');
            });

        } catch (error) {
            console.error('Gagal upload background:', error);

            // Fallback: pakai base64 lokal jika upload gagal
            let reader = new FileReader();
            reader.onload = function(f) {
                fabric.Image.fromURL(f.target.result, function(img) {
                    img.scaleToWidth(canvas.width);
                    img.scaleToHeight(canvas.height);
                    canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
                });
            };
            reader.readAsDataURL(file);
        }

        // Reset input
        e.target.value = '';
    }

    // Fungsi upload ke server
    async function uploadToServer(file) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('_token', '{{ csrf_token() }}');

        const response = await fetch('/upload-image', {
            method: 'POST',
            credentials: "include",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: formData
        });

        if (!response.ok) {
            throw new Error('Upload gagal: ' + response.status);
        }

        const result = await response.json();
        return result.url; // URL ke gambar di server
    }

    function saveTemplate() {
        // Sekarang canvas sudah pakai URL, bukan base64
        const canvasJSON = JSON.stringify(canvas.toJSON());
        document.getElementById('elements').value = canvasJSON;
        document.getElementById('form').submit();
    }
</script>
