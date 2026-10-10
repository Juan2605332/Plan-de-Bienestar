import QRCode from 'qrcode';

document.querySelectorAll('[data-qr-toggle]').forEach((button) => {
    button.addEventListener('click', async () => {
        const canvas = button.parentElement.querySelector('[data-qr-canvas]');
        if (!canvas) {
            return;
        }

        if (canvas.hidden) {
            canvas.hidden = false;
            if (!canvas.dataset.generated) {
                try {
                    await QRCode.toCanvas(canvas, button.dataset.qrUrl, {
                        width: 144,
                        margin: 1,
                        errorCorrectionLevel: 'M',
                    });
                    canvas.dataset.generated = 'true';
                } catch (error) {
                    canvas.hidden = true;
                    console.error('No se pudo generar el QR de asistencia.', error);
                    button.textContent = 'Error al generar QR';
                    button.disabled = true;
                }
            }
            button.textContent = 'Ocultar QR';
        } else {
            canvas.hidden = true;
            button.textContent = 'Mostrar QR';
        }
    });
});
