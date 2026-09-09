const nextButtons = document.querySelectorAll('.next_button');
const backButtons = document.querySelectorAll('.back_button');
const sections = document.querySelectorAll('.main');
const steps = document.querySelectorAll('.progress-bar li');
const stepNumber = document.querySelector('.step-number');
const stepContents = document.querySelectorAll('.step-number-content');
let currentStep = 0;

nextButtons.forEach((button) => {
    button.addEventListener('click', () => {
        if (!validateActiveStep()) {
            return;
        }

        currentStep = Math.min(currentStep + 1, sections.length - 1);
        updateStep();
    });
});

backButtons.forEach((button) => {
    button.addEventListener('click', () => {
        currentStep = Math.max(currentStep - 1, 0);
        updateStep();
    });
});

document.querySelector('form')?.addEventListener('submit', (event) => {
    if (!validateActiveStep()) {
        event.preventDefault();
    }
});

function updateStep() {
    sections.forEach((section, index) => section.classList.toggle('active', index === currentStep));
    steps.forEach((step, index) => step.classList.toggle('active', index <= currentStep));
    stepContents.forEach((content, index) => {
        content.classList.toggle('active', index === currentStep);
        content.classList.toggle('d-none', index !== currentStep);
    });

    if (stepNumber) {
        stepNumber.textContent = currentStep + 1;
    }
}

function validateActiveStep() {
    let valid = true;
    let firstInvalid = null;
    const activeSection = document.querySelector('.main.active');
    const inputs = activeSection.querySelectorAll('input, select, textarea');

    inputs.forEach((input) => {
        clearFieldWarning(input);

        if (!isFieldValid(input)) {
            markFieldWarning(input);
            valid = false;
            firstInvalid = firstInvalid || input;
        }
    });

    if (firstInvalid) {
        focusInvalidField(firstInvalid);
    }

    return valid;
}

function isFieldValid(input) {
    if (input.disabled) {
        return true;
    }

    if (input.type === 'checkbox' && input.hasAttribute('required')) {
        return input.checked;
    }

    if (input.type === 'file' && input.hasAttribute('required')) {
        return isFileInputValid(input);
    }

    if (input.name === 'cf-turnstile-response') {
        return Boolean(input.value);
    }

    if (input.hasAttribute('data-currency-input')) {
        return !input.hasAttribute('required') || input.value.replace(/\D/g, '').length > 0;
    }

    return input.checkValidity();
}

function markFieldWarning(input) {
    const message = getFieldValidationMessage(input);
    const field = input.closest('.input-div') || input.parentElement;

    input.classList.add('warning');
    field?.classList.add('has-error');
    input.closest('.custom-select')?.querySelector('.custom-select-trigger')?.classList.add('warning');

    if (input.type === 'file') {
        document.querySelector(`label[for="${input.id}"]`)?.classList.add('warning');
    }

    let error = field.querySelector('.field-error');

    if (!error) {
        error = document.createElement('small');
        error.className = 'field-error';
        field.appendChild(error);
    }

    error.textContent = message;
}

function clearFieldWarning(input) {
    const field = input.closest('.input-div') || input.parentElement;

    input.classList.remove('warning');
    field?.classList.remove('has-error');
    input.closest('.custom-select')?.querySelector('.custom-select-trigger')?.classList.remove('warning');

    if (input.type === 'file') {
        document.querySelector(`label[for="${input.id}"]`)?.classList.remove('warning');
    }

    field?.querySelector('.field-error')?.remove();
}

function getFieldValidationMessage(input) {
    if (input.type === 'checkbox') {
        return 'Field required';
    }

    if (input.type === 'file') {
        return getFileValidationMessage(input);
    }

    if (input.tagName === 'SELECT') {
        return 'Please select a valid state';
    }

    if (input.name === 'cf-turnstile-response') {
        return 'Field required';
    }

    if (input.name === 'portfolio_web_address') {
        if (!input.value) {
            return 'Field required';
        }

        return 'Gunakan URL valid dengan awalan https://';
    }

    if (input.name === 'phone') {
        if (!input.value) {
            return 'Field required';
        }

        return 'Nomor handphone maksimal 13 digit angka.';
    }

    return 'Field required';
}

function focusInvalidField(input) {
    const customSelectTrigger = input.closest('.custom-select')?.querySelector('.custom-select-trigger');

    if (customSelectTrigger) {
        customSelectTrigger.focus();
        return;
    }

    if (input.type === 'file') {
        document.querySelector(`label[for="${input.id}"]`)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    input.focus();
}

document.querySelector('.add-education-btn')?.addEventListener('click', (event) => {
    event.preventDefault();
    const template = document.querySelector('#education-template');
    const fields = template.content.cloneNode(true);

    fields.querySelectorAll('select').forEach(setupCustomSelect);
    document.querySelector('#show-input')?.appendChild(fields);
});

document.addEventListener('click', (event) => {
    if (event.target.classList.contains('remove-education-btn')) {
        event.preventDefault();
        event.target.closest('.input-text')?.remove();
    }

    if (event.target.classList.contains('remove-work-btn')) {
        event.preventDefault();
        event.target.closest('.input-text')?.remove();
    }
});

document.querySelector('.add-work-btn')?.addEventListener('click', (event) => {
    event.preventDefault();
    const template = document.querySelector('#work-template');
    const fields = template.content.cloneNode(true);

    fields.querySelectorAll('select').forEach(setupCustomSelect);
    document.querySelector('#show-input-work')?.appendChild(fields);
});

const photoInput = document.querySelector('#input-img');
const photoChosen = document.querySelector('#file-chosen-img');
const photoPreview = document.querySelector('#img-preview');

photoInput?.addEventListener('change', () => {
    const file = photoInput.files?.[0];

    if (!file) {
        if (photoChosen) {
            photoChosen.textContent = 'No file chosen';
        }

        if (photoPreview) {
            photoPreview.src = '/assets/img/choose.png';
        }

        return;
    }

    if (photoChosen) {
        photoChosen.textContent = file.name;
    }

    if (photoPreview && isAllowedExtension(photoInput, file)) {
        photoPreview.src = URL.createObjectURL(file);
    }
});

const cvInput = document.querySelector('#doc');
const cvChosen = document.querySelector('#file-chosen-doc');
const cvPreview = document.querySelector('#doc-preview');

cvInput?.addEventListener('change', () => {
    const file = cvInput.files?.[0];

    if (!file) {
        if (cvChosen) {
            cvChosen.textContent = 'No file chosen';
        }

        if (cvPreview) {
            cvPreview.src = '/assets/img/pdf-add.png';
        }

        return;
    }

    if (cvChosen) {
        cvChosen.textContent = file.name;
    }

    if (cvPreview) {
        const extension = getFileExtension(file.name);

        if (extension === 'pdf') {
            cvPreview.src = '/assets/img/pdf-file.svg';
        } else if (extension === 'doc' || extension === 'docx') {
            cvPreview.src = '/assets/img/word-file.svg';
        } else {
            cvPreview.src = '/assets/img/pdf-add.png';
        }
    }
});

function isFileInputValid(input) {
    const file = input.files?.[0];

    if (!file) {
        return false;
    }

    return isAllowedExtension(input, file) && file.size <= 2 * 1024 * 1024;
}

function isAllowedExtension(input, file) {
    const allowedExtensions = (input.dataset.allowedExtensions || '')
        .split(',')
        .map((extension) => extension.trim().toLowerCase())
        .filter(Boolean);

    return allowedExtensions.includes(getFileExtension(file.name));
}

function getFileExtension(filename) {
    return filename.split('.').pop().toLowerCase();
}

function getFileValidationMessage(input) {
    const file = input.files?.[0];
    const allowedExtensions = input.dataset.allowedExtensions?.toUpperCase().replaceAll(',', ', ') || 'file valid';

    if (!file) {
        return 'Example invalid form file feedback';
    }

    if (!isAllowedExtension(input, file)) {
        return 'Example invalid form file feedback';
    }

    if (file.size > 2 * 1024 * 1024) {
        return 'Example invalid form file feedback';
    }

    return 'Example invalid form file feedback';
}

document.querySelectorAll('[data-phone-input]').forEach((input) => {
    input.addEventListener('input', () => {
        input.value = input.value.replace(/\D/g, '').slice(0, 13);
    });
});

document.querySelectorAll('[data-currency-input]').forEach((input) => {
    const formatCurrency = () => {
        const digits = input.value.replace(/\D/g, '');

        if (!digits) {
            input.value = '';
            return;
        }

        input.value = `Rp ${new Intl.NumberFormat('id-ID').format(Number(digits))}`;
    };

    input.addEventListener('input', formatCurrency);
    input.form?.addEventListener('submit', () => {
        input.value = input.value.replace(/\D/g, '');
    });
    formatCurrency();
});

function setupCustomSelect(select) {
    if (select.dataset.customSelectReady === 'true') {
        return;
    }

    select.dataset.customSelectReady = 'true';
    select.classList.add('native-select-hidden');

    const wrapper = document.createElement('div');
    wrapper.className = 'custom-select';
    select.parentNode.insertBefore(wrapper, select);
    wrapper.appendChild(select);

    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'custom-select-trigger';
    wrapper.appendChild(trigger);

    const menu = document.createElement('div');
    menu.className = 'custom-select-menu';
    wrapper.appendChild(menu);

    const syncTrigger = () => {
        const selectedOption = select.options[select.selectedIndex];
        trigger.textContent = selectedOption?.textContent || 'Pilih';
        trigger.classList.toggle('is-placeholder', !select.value);
    };

    const rebuildOptions = () => {
        menu.innerHTML = '';

        Array.from(select.options).forEach((option) => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'custom-select-option';
            item.textContent = option.textContent;
            item.dataset.value = option.value;

            if (option.value === select.value) {
                item.classList.add('is-selected');
            }

            item.addEventListener('click', () => {
                select.value = option.value;
                select.dispatchEvent(new Event('change', { bubbles: true }));
                clearFieldWarning(select);
                syncTrigger();
                rebuildOptions();
                wrapper.classList.remove('is-open');
            });

            menu.appendChild(item);
        });
    };

    trigger.addEventListener('click', () => {
        document.querySelectorAll('.custom-select.is-open').forEach((openSelect) => {
            if (openSelect !== wrapper) {
                openSelect.classList.remove('is-open');
            }
        });

        wrapper.classList.toggle('is-open');
    });

    select.addEventListener('change', () => {
        syncTrigger();
        rebuildOptions();
    });

    syncTrigger();
    rebuildOptions();
}

document.querySelectorAll('form select').forEach(setupCustomSelect);

document.querySelectorAll('form input, form textarea, form select').forEach((input) => {
    const eventName = input.tagName === 'SELECT' || input.type === 'file' || input.type === 'checkbox' ? 'change' : 'input';

    input.addEventListener(eventName, () => {
        clearFieldWarning(input);
    });
});

document.addEventListener('click', (event) => {
    if (!event.target.closest('.custom-select')) {
        document.querySelectorAll('.custom-select.is-open').forEach((select) => {
            select.classList.remove('is-open');
        });
    }
});
