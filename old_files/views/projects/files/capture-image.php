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
window.CameraModule = window.CameraModule || (function () {

    let stream = null;
    let currentCamera = "user";

    // Elements
    const getEl = (id) => document.getElementById(id);
    let camera, captureBtn, flipBtn, startBtn, restartBtn, canvas, preview, imageData, indicator, cameraText;


    /** -------------------------------
     * Initialize DOM Elements
     * ------------------------------- */
    const initElements = () => {
        camera = getEl("camera");
        captureBtn = getEl("capture");
        flipBtn = getEl("flipCamera");
        startBtn = getEl("startCamera");
        restartBtn = getEl("restartCamera");
        canvas = getEl("snapshot");
        preview = getEl("preview");
        imageData = getEl("imageData");
        indicator = getEl("cameraIndicator");
        cameraText = getEl("cameraText");
    };


    /** -------------------------------
     * Reset UI (on modal open)
     * ------------------------------- */
    const resetCameraUI = () => {
        stopCamera();

        startBtn.style.display = "inline-block";
        restartBtn.style.display = "none";

        preview.style.display = "none";
        preview.classList.remove("captured-photo");

        camera.style.display = "none";
        imageData.value = "";

        indicator.classList.remove("active");
        cameraText.innerText = "Camera Off";
    };


    /** -------------------------------
     * Start Camera
     * ------------------------------- */
    const startCamera = async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: currentCamera }
            });

            camera.srcObject = stream;
            camera.style.display = "block";
            preview.style.display = "none";

            captureBtn.disabled = false;
            flipBtn.disabled = false;

            indicator.classList.add("active");
            cameraText.innerText = "Camera Active";

        } catch (err) {
            alert("Camera not accessible: " + err.message);
        }
    };


    /** -------------------------------
     * Stop Camera (always stop fully)
     * ------------------------------- */
    const stopCamera = () => {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }

        camera.srcObject = null;
        captureBtn.disabled = true;
        flipBtn.disabled = true;

        indicator.classList.remove("active");
        cameraText.innerText = "Camera Off";
    };


    /** -------------------------------
     * Switch Camera (Front / Back)
     * ------------------------------- */
    const switchCamera = async () => {
        stopCamera();
        currentCamera = currentCamera === "user" ? "environment" : "user";
        await startCamera();
    };


    /** -------------------------------
     * Capture Image
     * ------------------------------- */
    const captureImage = () => {
        if (!stream) return;

        canvas.width = camera.videoWidth;
        canvas.height = camera.videoHeight;

        const ctx = canvas.getContext("2d");
        ctx.drawImage(camera, 0, 0);

        const dataURL = canvas.toDataURL("image/png");

        preview.src = dataURL;
        preview.style.display = "block";
        preview.classList.add("captured-photo");

        camera.style.display = "none";
        imageData.value = dataURL;

        stopCamera();
        restartBtn.style.display = "inline-block";
    };


    /** -------------------------------
     * Retake Image
     * ------------------------------- */
    const retakeImage = () => {
        preview.style.display = "none";
        preview.classList.remove("captured-photo");

        imageData.value = "";
        restartBtn.style.display = "none";

        startCamera();
    };


    /** -------------------------------
     * Bind Events Once (AJAX Safe)
     * ------------------------------- */
    const bindEventsOnce = () => {
        if (!startBtn.dataset.bound) {

            startBtn.addEventListener("click", () => {
                startCamera();
                startBtn.style.display = "none";
            });

            captureBtn.addEventListener("click", captureImage);
            flipBtn.addEventListener("click", switchCamera);
            restartBtn.addEventListener("click", retakeImage);

            startBtn.dataset.bound = true;
        }

        // Modal OPEN → Reset UI
        $("#ajaxModal").off("shown.bs.modal").on("shown.bs.modal", () => {
            resetCameraUI();
        });

        // Modal CLOSE → Stop camera completely
        $("#ajaxModal").off("hidden.bs.modal").on("hidden.bs.modal", () => {
            stopCamera();
            resetCameraUI();
        });
    };


    /** -------------------------------
     * Public Init
     * ------------------------------- */
    const init = () => {
        initElements();
        bindEventsOnce();

        $("#file-form").appForm({
            onSuccess: function () {
                if ($("#file-manager-container-card").is(":visible")) {
                    location.reload();
                }
                $("#project-file-table").appTable({ reload: true });
            }
        });

        $("#file-form .select2").select2();
    };


    return { init };

})();


// Initialize Camera Module on document load
$(document).ready(function () {
    if (window.CameraModule) {
        window.CameraModule.init();
    }
});
</script>
