import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.querySelector('#signature-pad');
    if (canvas) {
        const signaturePad = new SignaturePad(canvas);

        document.querySelector('#clear-signature').addEventListener('click', () => {
            signaturePad.clear();
        });

        document.querySelector('#save-signature').addEventListener('click', () => {
            const dataUrl = signaturePad.toDataURL('image/png');
            Livewire.emit('signatureSaved', dataUrl);
        });
    }
});

import SignaturePad from 'signature-pad';
import Quill from 'quill';

window.Quill = Quill;
window.SignaturePad = SignaturePad;
