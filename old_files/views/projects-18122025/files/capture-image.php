<style>
    .camera-container {
        position: relative;
        background: #0d0d0d;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 260px;
        border: 2px solid #1f1f1f;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.4);
    }

    video, canvas, img {
        width: 100%;
        height: auto;
        object-fit: contain;
        border-radius: 10px;
    }

    /* Camera Status Dot */
    .camera-status {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
        background: rgba(0, 0, 0, 0.55);
        color: #fff;
        padding: 3px 8px;
        border-radius: 12px;
    }

    .camera-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: gray;
    }

    .camera-dot.active {
        background: #00ff55;
        box-shadow: 0 0 6px #00ff55;
    }

    /* Animation for captured photo */
    @keyframes zoomIn {
        0% {
            transform: scale(1);
            opacity: 0.6;
        }
        100% {
            transform: scale(1.05);
            opacity: 1;
        }
    }

    .captured-photo {
        animation: zoomIn 0.6s ease-in-out;
    }

    @media (max-width: 500px) {
        .modal {
            z-index: 1060;
            overflow-y: scroll !important;
        }
    }
</style>

<?php echo form_open(get_uri("projects/save_captured_image"), ["id" => "file-form", "enctype" => "multipart/form-data", "class" => "general-form", "role" => "form"]); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">

        <?php echo view("custom_fields/form/prepare_context_fields", ["custom_fields" => $custom_fields, "label_column" => "col-md-3", "field_column" => "col-md-9"]); ?>

        <input type="hidden" name="id" value="<?php echo $model_info->id ?? ''; ?>" />
        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
        <input type="hidden" name="folder_id" value="<?php echo $folder_id; ?>" />

        <!-- Camera Section -->
        <div class="form-group mt-2">
            <label class="col-md-12 mb-2 fw-bold">Capture from Camera</label>
            <div class="camera-container">
                <div id="cameraStatus" class="camera-status">
                    <span class="camera-dot" id="cameraIndicator"></span>
                    <span id="cameraText">Camera Off</span>
                </div>
                <video id="camera" autoplay playsinline style="display:none;"></video>
                <canvas id="snapshot" style="display:none;"></canvas>
                <img id="preview" src="" style="display:none;" />
            </div>

            <div class="mt-3 d-flex justify-content-center flex-wrap gap-2">
                <button type="button" id="startCamera" class="btn btn-success">
                    ▶️ Start Camera
                </button>
                <button type="button" id="capture" class="btn btn-primary" disabled>
                    📸 Capture
                </button>
                <button type="button" id="flipCamera" class="btn btn-secondary" disabled>
                    🔄 Flip Camera
                </button>
                <button type="button" id="restartCamera" class="btn btn-outline-info" style="display:none;">
                    🔁 Retake
                </button>
            </div>
            <input type="hidden" name="image" id="imageData">
        </div>

        <!-- File Details -->
        <div class="form-group mt-3">
            <div class="row">
                <label for="category_id" class="col-md-3"><?php echo app_lang('category'); ?></label>
                <div class="col-md-9">
                    <?php echo form_dropdown("category_id", $file_categories_dropdown, [$model_info->category_id ?? ''], "class='select2' id='category_id'"); ?>
                </div>
            </div>
        </div>

        <div class="form-group mt-3">
            <div class="row">
                <label for="description" class="col-md-3"><?php echo app_lang('description'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input([
                        "id" => "description",
                        "name" => "description",
                        "value" => $model_info->description ?? "",
                        "class" => "form-control description-field",
                        "placeholder" => app_lang('description'),
                        "autofocus" => true,
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal">
        <span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?>
    </button>
    <button type="submit" class="btn btn-primary" id="file-save-button">
        <span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?>
    </button>
</div>
<?php echo form_close(); ?>


<script>
    let stream = null;
    let currentCamera = 'user'; // front by default

    const camera = document.getElementById('camera');
    const captureBtn = document.getElementById('capture');
    const flipBtn = document.getElementById('flipCamera');
    const startBtn = document.getElementById('startCamera');
    const restartBtn = document.getElementById('restartCamera');
    const canvas = document.getElementById('snapshot');
    const preview = document.getElementById('preview');
    const imageData = document.getElementById('imageData');
    const indicator = document.getElementById('cameraIndicator');
    const cameraText = document.getElementById('cameraText');

    const startCamera = async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: currentCamera }
            });
            camera.srcObject = stream;
            camera.style.display = 'block';
            preview.style.display = 'none';
            captureBtn.disabled = false;
            flipBtn.disabled = false;
            indicator.classList.add('active');
            cameraText.innerText = 'Camera Active';
        } catch (err) {
            alert("Camera not accessible: " + err.message);
            indicator.classList.remove('active');
            cameraText.innerText = 'Camera Off';
        }
    };

    const stopCamera = () => {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        camera.srcObject = null;
        captureBtn.disabled = true;
        flipBtn.disabled = true;
        indicator.classList.remove('active');
        cameraText.innerText = 'Camera Off';
    };

    const switchCamera = async () => {
        stopCamera();
        currentCamera = (currentCamera === 'user') ? 'environment' : 'user';
        await startCamera();
    };

    // Capture Image
    captureBtn.addEventListener('click', () => {
        if (!stream) return;
        canvas.width = camera.videoWidth;
        canvas.height = camera.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(camera, 0, 0);
        const dataURL = canvas.toDataURL('image/png');
        preview.src = dataURL;
        preview.classList.add('captured-photo');
        preview.style.display = 'block';
        camera.style.display = 'none';
        imageData.value = dataURL;

        stopCamera();
        restartBtn.style.display = 'inline-block';
    });

    startBtn.addEventListener('click', () => {
        startCamera();
        startBtn.style.display = 'none';
        restartBtn.style.display = 'none';
    });

    restartBtn.addEventListener('click', () => {
        preview.style.display = 'none';
        preview.classList.remove('captured-photo');
        imageData.value = '';
        startBtn.style.display = 'none';
        restartBtn.style.display = 'none';
        startCamera();
    });

    flipBtn.addEventListener('click', switchCamera);

    // Stop camera when modal closes
    $('#ajaxModal').on('hidden.bs.modal', function() {
        stopCamera();
        startBtn.style.display = 'inline-block';
        preview.style.display = 'none';
        camera.style.display = 'none';
        preview.classList.remove('captured-photo');
    });

    // Handle form submission
    $(document).ready(function() {
        $("#file-form").appForm({
            onSuccess: function(result) {
                if ($("#file-manager-container-card").is(":visible")) {
                    location.reload();
                }
                $("#project-file-table").appTable({ reload: true });
            }
        });
        $("#file-form .select2").select2();
    });
</script>
