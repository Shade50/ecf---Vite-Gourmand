// console.log('Script compression chargé');
// alert("SCRIPT CHARGÉ");
// console.log("SCRIPT CHARGÉ");

document.addEventListener('DOMContentLoaded', () => {
    const photoInput = document.querySelector(
        'input[name$="[photoFile]"]'
    );

    if (!photoInput) {
        return;
    }

    const message = document.createElement('small');
    message.className = 'd-block mt-2 text-muted';
    photoInput.insertAdjacentElement('afterend', message);

    photoInput.addEventListener('change', async () => {
        const originalFile = photoInput.files[0];

        if (!originalFile) {
            return;
        }

        if (!originalFile.type.startsWith('image/')) {
            message.textContent = 'Le fichier sélectionné n’est pas une image.';
            return;
        }

        try {
            message.textContent = 'Optimisation de la photo en cours…';

            const compressedFile = await compressImage(originalFile);

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(compressedFile);
            photoInput.files = dataTransfer.files;

            const originalSize = formatFileSize(originalFile.size);
            const compressedSize = formatFileSize(compressedFile.size);

            message.textContent =
                `Photo optimisée : ${originalSize} → ${compressedSize}`;
        } catch (error) {
            console.error(error);
            message.textContent =
                'La photo n’a pas pu être optimisée. Choisis une image plus petite.';
        }
    });
});

async function compressImage(file) {
    const image = await createImageBitmap(file);

    const maximumWidth = 1600;
    const maximumHeight = 1200;

    let width = image.width;
    let height = image.height;

    const ratio = Math.min(
        maximumWidth / width,
        maximumHeight / height,
        1
    );

    width = Math.round(width * ratio);
    height = Math.round(height * ratio);

    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;

    const context = canvas.getContext('2d');

    if (!context) {
        throw new Error('Canvas indisponible.');
    }

    context.drawImage(image, 0, 0, width, height);
    image.close();

    let quality = 0.82;
    let blob = await canvasToBlob(canvas, quality);

    // On vise moins de 1,8 Mo pour rester sous la limite PHP de 2 Mo.
    while (blob.size > 1800000 && quality > 0.4) {
        quality -= 0.1;
        blob = await canvasToBlob(canvas, quality);
    }

    if (blob.size > 1800000) {
        throw new Error('Image encore trop volumineuse.');
    }

    const filenameWithoutExtension = file.name.replace(/\.[^/.]+$/, '');

    return new File(
        [blob],
        `${filenameWithoutExtension}.webp`,
        {
            type: 'image/webp',
            lastModified: Date.now()
        }
    );
}

function canvasToBlob(canvas, quality) {
    return new Promise((resolve, reject) => {
        canvas.toBlob(
            blob => {
                if (blob) {
                    resolve(blob);
                } else {
                    reject(new Error('Échec de la compression.'));
                }
            },
            'image/webp',
            quality
        );
    });
}

function formatFileSize(size) {
    return `${(size / 1024 / 1024).toFixed(2)} Mo`;
}