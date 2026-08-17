import Swal from 'sweetalert2';

window.Swal = Swal;

/**
 * Shared SweetAlert2 theme matching the app's brand palette.
 */
const brandSwal = Swal.mixin({
    confirmButtonColor: '#003D99',
    cancelButtonColor: '#99C2FF',
    background: '#ffffff',
    color: '#001433',
    customClass: {
        popup: 'rounded-2xl',
        confirmButton: 'rounded-xl',
    },
});
window.brandSwal = brandSwal;

/**
 * Render server-side flash messages (status / errors) as toasts on page load.
 * Reads data attributes set on <body> by the Blade layout.
 */
document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const status = body.dataset.flashStatus;
    const error = body.dataset.flashError;

    if (status) {
        brandSwal.fire({
            icon: 'success',
            title: status,
            toast: true,
            position: 'top-end',
            timer: 4000,
            showConfirmButton: false,
        });
    }

    if (error) {
        brandSwal.fire({
            icon: 'error',
            title: error,
        });
    }

    initOtpInputs();
    initResendCountdown();
    initFormSubmitSpinners();
});

/**
 * Wire up the 6 individual OTP boxes: auto-advance, backspace, paste, and
 * keep a hidden `code` input in sync for submission.
 */
function initOtpInputs() {
    const boxes = document.querySelectorAll('.otp-input');
    if (!boxes.length) return;

    const hidden = document.getElementById('otp-hidden-code');

    const sync = () => {
        hidden.value = Array.from(boxes).map((b) => b.value).join('');
    };

    boxes.forEach((box, index) => {
        box.addEventListener('input', () => {
            box.value = box.value.replace(/\D/g, '').slice(0, 1);
            if (box.value && boxes[index + 1]) {
                boxes[index + 1].focus();
            }
            sync();
        });

        box.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !box.value && boxes[index - 1]) {
                boxes[index - 1].focus();
            }
        });

        box.addEventListener('paste', (e) => {
            e.preventDefault();
            const digits = (e.clipboardData.getData('text') || '').replace(/\D/g, '').split('');
            boxes.forEach((b, i) => (b.value = digits[i] || ''));
            sync();
            const next = boxes[Math.min(digits.length, boxes.length - 1)];
            if (next) next.focus();
        });
    });

    boxes[0].focus();
}

/**
 * Disable the resend button and count down until the server-side cooldown
 * (passed via data-cooldown-until, a Unix timestamp in ms) has elapsed.
 */
function initResendCountdown() {
    const btn = document.getElementById('resend-btn');
    if (!btn) return;

    const until = parseInt(btn.dataset.cooldownUntil || '0', 10);

    const tick = () => {
        const remaining = Math.ceil((until - Date.now()) / 1000);
        if (remaining > 0) {
            btn.disabled = true;
            btn.textContent = `Resend code in ${remaining}s`;
            setTimeout(tick, 1000);
        } else {
            btn.disabled = false;
            btn.textContent = 'Resend code';
        }
    };

    if (until > Date.now()) {
        tick();
    }
}

/**
 * Show a small loading state on primary form submissions so users get
 * feedback while the request is in flight.
 */
function initFormSubmitSpinners() {
    document.querySelectorAll('form[data-loading-text]').forEach((form) => {
        form.addEventListener('submit', () => {
            const btn = form.querySelector('button[type="submit"]');
            if (!btn) return;
            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> ${form.dataset.loadingText}`;
        });
    });
}
