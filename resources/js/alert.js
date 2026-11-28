const toast = document.getElementById('app-alert-toast');
const dialog = document.getElementById('action-confirmation');
const pendingAlert = document.getElementById('session-alert');
const toastMessage = toast?.querySelector('[data-alert-message]');
const cancelButton = dialog?.querySelector('[data-alert-cancel]');
const confirmButton = dialog?.querySelector('[data-alert-confirm]');
const dialogTitle = dialog?.querySelector('#action-confirmation-title');
const dialogMessage = dialog?.querySelector('#action-confirmation-message');

let pendingForm = null;
let pendingSubmitter = null;
let confirmedForm = null;
let toastTimeout;

const actionMessages = {
    create: ['Create record?', 'Create this record?'],
    update: ['Save changes?', 'Save these changes?'],
    delete: ['Delete record?', 'This action cannot be undone. Delete this record?'],
    logout: ['Log out?', 'Are you sure you want to log out?'],
};

function showToast(message, type = 'success') {
    if (!toast || !toastMessage || !message) {
        return;
    }

    const isError = type === 'error';

    toastMessage.textContent = message;
    toast.setAttribute('role', isError ? 'alert' : 'status');
    toast.setAttribute('aria-live', isError ? 'assertive' : 'polite');
    toast.classList.toggle('border-rose-200', isError);
    toast.classList.toggle('border-emerald-200', !isError);
    const successIcon = toast.querySelector('[data-alert-icon="success"]');
    const errorIcon = toast.querySelector('[data-alert-icon="error"]');

    if (successIcon) {
        successIcon.hidden = isError;
        successIcon.classList.toggle('hidden', isError);
    }

    if (errorIcon) {
        errorIcon.hidden = !isError;
        errorIcon.classList.toggle('hidden', !isError);
    }
    toast.hidden = false;
    toast.classList.remove('hidden');
    toast.classList.add('flex');

    window.clearTimeout(toastTimeout);
    toastTimeout = window.setTimeout(() => {
        toast.hidden = true;
        toast.classList.add('hidden');
        toast.classList.remove('flex');
    }, 5000);
}

if (pendingAlert) {
    showToast(
        pendingAlert.dataset.alertMessage,
        pendingAlert.dataset.alertType,
    );
}

document.addEventListener('invalid', (event) => {
    if (confirmedForm === event.target.form) {
        confirmedForm = null;
    }

    showToast('Please review the highlighted fields.', 'error');
}, true);

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement) || !form.dataset.alertAction) {
        return;
    }

    if (confirmedForm === form) {
        confirmedForm = null;

        return;
    }

    const [title, message] = actionMessages[form.dataset.alertAction] ?? [];

    if (!title || !message || !dialog || !dialogTitle || !dialogMessage || !confirmButton) {
        return;
    }

    event.preventDefault();
    pendingForm = form;
    pendingSubmitter = event.submitter;
    dialogTitle.textContent = title;
    dialogMessage.textContent = message;
    confirmButton.textContent = form.dataset.alertAction === 'delete' ? 'Delete' : 'Continue';

    if (typeof dialog.showModal === 'function') {
        dialog.showModal();
    } else {
        pendingForm = null;
        pendingSubmitter = null;

        if (window.confirm(message)) {
            confirmedForm = form;
            event.submitter
                ? form.requestSubmit(event.submitter)
                : form.requestSubmit();
        }
    }
});

cancelButton?.addEventListener('click', () => {
    pendingForm = null;
    pendingSubmitter = null;
    dialog?.close();
});

confirmButton?.addEventListener('click', () => {
    const form = pendingForm;
    const submitter = pendingSubmitter;
    pendingForm = null;
    pendingSubmitter = null;
    dialog?.close();

    if (form) {
        confirmedForm = form;
        submitter ? form.requestSubmit(submitter) : form.requestSubmit();
    }
});

dialog?.addEventListener('cancel', () => {
    pendingForm = null;
    pendingSubmitter = null;
});

toast?.querySelector('[data-alert-dismiss]')?.addEventListener('click', () => {
    window.clearTimeout(toastTimeout);
    toast.hidden = true;
    toast.classList.add('hidden');
    toast.classList.remove('flex');
});
