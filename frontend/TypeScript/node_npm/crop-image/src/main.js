import Cropper from 'cropperjs';

const uploadButton = document.getElementById('upload-button');
const cropButton = document.getElementById('crop-button');
const downloadButton = document.getElementById('download-button');
const imageUpload = document.getElementById('image-upload');
const displayedImage = document.getElementById('displayed-image');
const croppedContainer = document.querySelector('.cropped-container');
const imageCropped = document.getElementById('image-cropped');

let cropper = null;

uploadButton.onclick = () => {
    imageUpload.click();
};

imageUpload.onchange = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    if (file.size > 300 * 1024) {
        alert("Ошибка: фото больше 300 КБ. Выберите меньше.");
        imageUpload.value = "";
        return;
    }

    if (!file.type.includes("jpeg") && !file.type.includes("png")) {
        alert("Только JPG или PNG!");
        imageUpload.value = "";
        return;
    }

    displayedImage.src = URL.createObjectURL(file);

    if (cropper) {
        cropper.destroy();
    }

    displayedImage.onload = () => {
        cropper = new Cropper(displayedImage, {
            aspectRatio: NaN,
            viewMode: 1,
            guides: true,
            background: true,
            cropBoxMovable: true,
            cropBoxResizable: true,
        });

        cropButton.disabled = false;
        downloadButton.disabled = true;
        croppedContainer.style.display = "none";
    };
};

cropButton.onclick = () => {
    if (!cropper) {
        alert("Сначала загрузите фото!");
        return;
    }

    const canvas = cropper.getCroppedCanvas({
        maxWidth: 600,
        maxHeight: 600,
        fillColor: "#fff",
    });

    if (!canvas) {
        alert("Не удалось обрезать.");
        return;
    }

    imageCropped.src = canvas.toDataURL("image/png");
    croppedContainer.style.display = "flex";

    downloadButton.disabled = false;
};

downloadButton.onclick = () => {
    if (croppedContainer.style.display === "none") {
        alert("Нечего скачивать. Сначала нажмите 'Crop image'.");
        return;
    }

    const a = document.createElement("a");
    a.href = imageCropped.src;
    a.download = "обрезанное-фото.png";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
};