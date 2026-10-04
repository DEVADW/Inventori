// Ganti status

// Upload photo
function setupPhotoPreviewBindings() {
    const panels = Array.from(document.querySelectorAll('.photo-upload-panel'));

    panels.forEach((panel) => {
        const previewBox = panel.querySelector('#previewBox');
        const inputs = Array.from(panel.querySelectorAll('input[type="file"][name="photos[]"]'));

        if (!previewBox || inputs.length === 0) {
            return;
        }

        const state = new Map();
        inputs.forEach((input) => state.set(input, []));

        const renderPreview = (input, files) => {
            previewBox.innerHTML = '';

            if (files.length === 0) {
                previewBox.className = 'photo-preview-empty';
                previewBox.innerHTML = '<i class="bi bi-image text-body-secondary"></i><span>Belum ada foto dipilih</span>';
                return;
            }

            previewBox.className = '';
            files.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (event) => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'photo-preview-card';

                    const image = document.createElement('img');
                    image.className = 'photo-preview-image';
                    image.src = event.target.result;
                    image.alt = `Preview ${file.name}`;

                    const details = document.createElement('div');
                    details.className = 'photo-preview-details';

                    const fileName = document.createElement('div');
                    fileName.className = 'photo-preview-name';
                    fileName.textContent = file.name;

                    const uploadStatus = document.createElement('span');
                    uploadStatus.className = 'badge text-bg-success';
                    uploadStatus.textContent = 'Siap diunggah';
                    details.append(fileName, uploadStatus);

                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'photo-remove-button';
                    removeButton.title = 'Hapus foto';
                    removeButton.setAttribute('aria-label', 'Hapus foto');
                    removeButton.innerHTML = '<i class="bi bi-trash3"></i>';
                    removeButton.addEventListener('click', () => removePhoto(input, index));

                    wrapper.append(image, details, removeButton);
                    previewBox.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });
        };

        const removePhoto = (input, index) => {
            const files = state.get(input) || [];
            files.splice(index, 1);
            state.set(input, files);
            renderPreview(input, files);
            syncFilesToInput(input, files);
        };

        const syncFilesToInput = (input, files) => {
            const dataTransfer = new DataTransfer();
            files.forEach((file) => dataTransfer.items.add(file));
            input.files = dataTransfer.files;
        };

        inputs.forEach((input) => {
            input.addEventListener('change', (e) => {
                const files = Array.from(e.target.files || []).slice(0, 1);
                state.set(input, files);
                renderPreview(input, files);
                syncFilesToInput(input, files);
            });
        });
    });
}

setupPhotoPreviewBindings();

const unitSearchInput = document.querySelector('[data-unit-select2]');
const unitSearchDropdown = document.getElementById('unitSearchDropdown');
const unitSearchResults = document.getElementById('unitSearchResults');
const typeUnitField = document.getElementById('type_unit');
const serialNumberModulField = document.getElementById('serial_number_modul');

if (unitSearchInput && unitSearchDropdown && unitSearchResults) {
    let timer = null;

    const renderEmpty = () => {
        unitSearchResults.innerHTML = '<div class="list-group-item text-muted small">Tidak ada data unit</div>';
    };

    const runSearch = (keyword = '') => {
        const q = encodeURIComponent(keyword.trim());

        fetch(`/api/units/select2?q=${q}`, {
            headers: {
                Accept: 'application/json',
            },
        })
            .then((response) => response.json())
            .then((payload) => {
                const results = payload.results ?? [];

                unitSearchResults.innerHTML = '';

                if (!results.length) {
                    renderEmpty();
                    return;
                }

                results.forEach((unit) => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action';
                    item.innerHTML = `<span class="fw-semibold">${unit.code_unit ?? unit.text}</span><span class="d-block small text-muted">${unit.text}</span>`;
                    item.addEventListener('click', () => {
                        if (unitSearchInput) {
                            unitSearchInput.value = unit.code_unit ?? unit.code_unit ?? '';
                        }

                        if (typeUnitField) {
                            typeUnitField.value = unit.type_unit ?? unit.model ?? '';
                        }

                        if (serialNumberModulField) {
                            serialNumberModulField.value = unit.serial_number ?? '';
                        }

                        unitSearchDropdown.style.display = 'none';
                    });

                    unitSearchResults.appendChild(item);
                });
            })
            .catch(() => {
                renderEmpty();
            });
    };

    unitSearchInput.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => runSearch(unitSearchInput.value), 250);

        if (unitSearchInput.value.trim().length > 0) {
            unitSearchDropdown.style.display = 'block';
        } else {
            unitSearchDropdown.style.display = 'none';
        }
    });

    unitSearchInput.addEventListener('focus', () => {
        if (unitSearchInput.value.trim().length > 0) {
            runSearch(unitSearchInput.value);
            unitSearchDropdown.style.display = 'block';
        }
    });

    document.addEventListener('click', (event) => {
        if (!unitSearchDropdown.contains(event.target) && event.target !== unitSearchInput) {
            unitSearchDropdown.style.display = 'none';
        }
    });
}
