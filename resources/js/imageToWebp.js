// A phone camera writes 4-8 MB JPEGs at 4000+ px on the long edge, and the
// admin form used to send every one of them to the server untouched — slow on
// mobile data, heavy on disk, and far larger than anything the storefront
// renders. Re-encode to WebP in the browser first: usually a tenth of the
// bytes at a size the catalog actually uses.
//
// Nothing here is allowed to cost the admin an upload. A video, a file the
// browser cannot decode, a browser that cannot write WebP, a result that comes
// out heavier than the original — every one of those returns the original file
// and the form behaves exactly as it did before.

const MAX_EDGE = 2000;
const QUALITY = 0.82;

// PrimeVue's FileUpload hands the same File objects back on every select and
// remove event, so without this the whole gallery would be re-encoded each
// time the admin drops one photo.
const converted = new WeakMap();

const canEncodeWebp = () => {
    try {
        return document.createElement('canvas').toDataURL('image/webp').startsWith('data:image/webp');
    } catch {
        return false;
    }
};

const drawScaled = (bitmap) => {
    const scale = Math.min(1, MAX_EDGE / Math.max(bitmap.width, bitmap.height));
    const canvas = document.createElement('canvas');

    canvas.width = Math.round(bitmap.width * scale);
    canvas.height = Math.round(bitmap.height * scale);
    canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);

    return canvas;
};

const encode = (canvas) => new Promise((resolve) => canvas.toBlob(resolve, 'image/webp', QUALITY));

export const imageToWebp = async (file) => {
    if (converted.has(file)) return converted.get(file);
    if (!file?.type?.startsWith('image/') || !canEncodeWebp()) return file;

    let result = file;

    try {
        // 'from-image' applies the EXIF rotation a phone writes into the file
        // instead of rotating the pixels — without it portrait shots land on
        // their side.
        const bitmap = await createImageBitmap(file, { imageOrientation: 'from-image' });
        const blob = await encode(drawScaled(bitmap));

        bitmap.close();

        // An already-optimised WebP or a small flat PNG can come out heavier.
        if (blob && blob.size < file.size) {
            result = new File([blob], `${file.name.replace(/\.[^.]+$/, '')}.webp`, {
                type: 'image/webp',
                lastModified: Date.now(),
            });

            // The variation previews read .objectURL straight off the file
            // object, since that is what PrimeVue puts there — the replacement
            // needs one of its own.
            result.objectURL = URL.createObjectURL(blob);
        }
    } catch {
        result = file;
    }

    converted.set(file, result);

    return result;
};

export const imagesToWebp = (files) => Promise.all(Array.from(files ?? []).map(imageToWebp));
