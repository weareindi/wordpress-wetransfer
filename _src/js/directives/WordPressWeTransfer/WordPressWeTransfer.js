import FormTemplate from '../../templates/FormTemplate.html';
import FileItemTemplate from '../../templates/FileItemTemplate.html';
import SuccessTemplate from '../../templates/SuccessTemplate.html';
import FailedTemplate from '../../templates/FailedTemplate.html';
import TransferringTemplate from '../../templates/TransferringTemplate.html';

import ErrorService from '../../Services/ErrorService/ErrorService';
import ValidationService from '../../Services/ValidationService/ValidationService';

import qs from 'qs';
import closest from 'closest';

export class WordPressWeTransfer {
    constructor(surface) {
        this.error = new ErrorService();
        this.validate = new ValidationService();
        this.surface = surface;
        this.run();
    }

    async run() {
        try {
            await this.error.variablesAvailable();
            await this.error.featuresAvailable();
            await this.prepareElements();
            await this.initalise();
        } catch(error) {
            console.error(error);
        }
    }

    prepareElements() {
        this.elements = {};
        this.elements.surface = this.surface;
        this.elements.content = this.surface.querySelector('.wordpress-wetransfer__content');
    }

    async initalise() {
        try {
            await this.elements.surface.classList.add('wordpress-wetransfer-enhanced');
            await this.loadStylesheet();
            await this.replaceContent();
            await this.registerForm();
            await this.registerBinds();
            await this.resetFiles();
            await this.updateStatus('ready');
        } catch(error) {
            console.error(error);
        }
    }

    loadStylesheet() {
        const element = document.createElement('link');
        element.rel = 'stylesheet';
        element.type = 'text/css';
        element.href = wordpresswetransfer.pluginDir + '/assets/css/enhanced.css';
        document.body.appendChild(element);
    }

    replaceContent() {
        this.elements.content.innerHTML = FormTemplate;
    }

    registerForm() {
        this.elements.form = this.elements.surface.querySelector('.wordpress-wetransfer-form');
        this.elements.input = this.elements.form.querySelector('.wordpress-wetransfer-form__input--file');
        this.elements.list = this.elements.form.querySelector('.wordpress-wetransfer-form__list');
        this.elements.submit = this.elements.form.querySelector('.wordpress-wetransfer-form__button--submit');
    }

    fireSuccessEvent() {
        const event = new CustomEvent('wordpress-wetransfer-success', {
            detail: {
                id: this.transfer.id,
                url: this.transfer.shortened_url
            }
        });

        document.dispatchEvent(event);
    }

    registerBinds() {
        this.elements.form.addEventListener('submit', (event) => {
            event.preventDefault();

            this.submit();
        }, false);

        this.elements.input.addEventListener('change', (event) => {
            event.stopPropagation();
            event.preventDefault();

            this.disableSubmit();
            this.processFiles(this.elements.input.files);
            this.populateFilesList();
            this.processStatus();
            this.resetInput();
        }, false);

        window.addEventListener('dragover', function(event) {
            event.preventDefault();
        },false);

        window.addEventListener('drop', function(event) {
            event.preventDefault();
        },false);

        this.elements.surface.addEventListener('drop', (event) => {
            event.preventDefault();

            this.disableSubmit();
            this.processFiles(event.dataTransfer.files);
            this.populateFilesList();
            this.processStatus();
        }, false);

        this.elements.list.addEventListener('click', (event) => {
            event.stopPropagation();
            event.preventDefault();

            if (!event.target.classList.contains('wordpress-wetransfer-item__button--delete') && !event.target.parentElement.classList.contains('wordpress-wetransfer-item__button--delete')) {
                return false;
            }

            this.removeFile(closest(event.target, '.wordpress-wetransfer-item').getAttribute('wordpress-wetransfer-item'));
            this.disableSubmit();
            this.processFiles();
            this.populateFilesList();
            this.processStatus();
        }, false);
    }

    removeFile(index) {
        this.files.splice(index, 1);
    }

    resetInput() {
        this.elements.input.value = '';
    }

    resetFiles() {
        this.files = [];
    }

    disableSubmit() {
        this.elements.submit.disabled = true;
    }

    enableSubmit() {
        this.elements.submit.disabled = false;
    }

    processFiles(filelist) {
        const a = this.files.length === 0 ? [] : this.files;
        const b = (!filelist || filelist.length === 0) ? [] : Array.from(filelist);
        this.files = a.concat(b);
    }

    populateFilesList() {
        this.elements.list.innerHTML = '';
        Array.forEach(this.files, (file, index) => {
            let template = FileItemTemplate;
            template = template.split('${index}').join(index);
            template = template.split('${filename}').join(file.name);
            this.elements.list.innerHTML += template;
        });
    }

    processStatus() {
        this.updateStatus('ready');
        if (!this.validate.uploadLimit(this.files)) {
            this.updateStatus('limit-exceeded');
        }

        if (this.files.length > 0 && this.validate.uploadLimit(this.files)) {
            this.enableSubmit();
        }
    }

    async updateStatus(status) {
        await this.elements.surface.setAttribute('status', status);
    }

    async updateUploadProgress(totalUploadedBytes) {
        let progress = (totalUploadedBytes / this.totalUploadableBytes) * 100;
        await (document.querySelector('.wordpress-wetransfer-transfering__amount').innerHTML = Math.round(progress));
    }

    async updateContent(template, replacements) {
        if ((typeof replacements !== 'undefined') && (replacements.length > 0)) {
            Array.forEach(replacements, (replacement) => {
                for (const find in replacement) {
                    const replace = replacement[find];
                    template = template.split(find).join(replace);
                }
            });
        }

        this.elements.content.innerHTML = template;
    }

    async submit() {
        try {
            await this.disableSubmit();
            await this.error.files(this.files);
            await this.prepareItems();
            await this.updateStatus('transfering');
            await this.updateContent(TransferringTemplate);
            await this.getToken();
            await this.createTransfer();
            await this.addItems();
            await this.uploadItems();
            await this.finished();
        } catch(error) {
            console.error(error);
            this.updateStatus('error');
            await this.updateContent(FailedTemplate);
        }
    }

    async prepareItems() {
        // Prepare total uploadable bytes counter
        this.totalUploadableBytes = 0;

        this.items = [];
        Array.forEach(this.files, (fileinfo, index) => {
            // Add to total uploadable bytes counter
            this.totalUploadableBytes += fileinfo.size;

            // Prepare and add item to array
            this.items.push({
                fileinfo: fileinfo,
                wetransfer: {
                    item: {
                        filename: fileinfo.name,
                        filesize: fileinfo.size,
                        content_identifier: 'file',
                        local_identifier: 'wordpresswetransfer--' + index
                    }
                }
            });
        });
    }

    async getToken() {
        // Prepare data
        const data = {
            action: 'wordpresswetransfer--auth'
        };

        // Prepare URL
        const url = wordpresswetransfer.ajaxUrl;

        // Prepare Headers
        const headers = new Headers({
            'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
        });

        // Prepare Headers
        const options = {
            method: 'POST',
            headers: headers,
            body: qs.stringify(data)
        };

        // Prepare Request
        const request = new Request(url, options);

        // Fetch
        return await fetch(request)
            .then((response) => {
                this.error.response(response);

                return response.json();
            })
            .then((response) => {
                this.error.success(response);

                return this.token = response.token;
            })
            .catch((error) => {
                throw new Error(error.message);
            });
    }

    async createTransfer() {
        // Prepare data
        const data = {
            action: 'wordpresswetransfer--transfer',
            token: this.token
        };

        // Prepare URL
        const url = wordpresswetransfer.ajaxUrl;

        // Prepare Headers
        const headers = new Headers({
            'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
        });

        // Prepare Headers
        const options = {
            method: 'POST',
            headers: headers,
            body: qs.stringify(data)
        };

        // Prepare Request
        const request = new Request(url, options);

        // Fetch
        return await fetch(request)
            .then((response) => {
                this.error.response(response);

                return response.json();
            })
            .then((transfer) => {
                this.error.transfer(transfer);

                return this.transfer = transfer;
            })
            .catch((error) => {
                throw new Error(error.message);
            });
    }

    async addItems() {
        const transferItems = [];
        Array.forEach(this.items, (item) => {
            transferItems.push(item.wetransfer.item);
        });

        // Prepare data
        const data = {
            action: 'wordpresswetransfer--items',
            token: this.token,
            transferId: this.transfer.id,
            items: transferItems
        };

        // Prepare URL
        const url = wordpresswetransfer.ajaxUrl;

        // Prepare Headers
        const headers = new Headers({
            'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
        });

        // Prepare Headers
        const options = {
            method: 'POST',
            headers: headers,
            body: qs.stringify(data)
        };

        // Prepare Request
        const request = new Request(url, options);

        // Fetch
        return await fetch(request)
            .then((response) => {
                this.error.response(response);

                return response.json();
            })
            .then((transferItems) => {
                this.error.transferItems(transferItems);

                Array.forEach(transferItems, (transferItem, transferItemIndex) => {
                    const index = this.items.findIndex((item) => {
                        return item.wetransfer.item.local_identifier === transferItem.local_identifier;
                    });

                    return this.items[index].wetransfer.transfer = transferItem;
                });
            })
            .catch((error) => {
                throw new Error(error.message);
            });
    }

    async uploadItems() {
        // Validate items
        this.error.items(this.items);

        // Prepare variable for upload progress
        let totalUploadedBytes = 0;

        // Update upload progress
        await this.updateUploadProgress(totalUploadedBytes);

        for (const item of this.items) {
            for (let partNumber = 1; partNumber <= item.wetransfer.transfer.meta.multipart_parts; partNumber++) {
                // Get Chunk
                const chunk = await this.getChunk(item.fileinfo, partNumber, item.wetransfer.transfer.meta.multipart_parts);

                // Get upload Data
                const uploadData = await this.getUploadData(item.wetransfer.transfer, partNumber, item.wetransfer.transfer.meta.multipart_upload_id);

                // Validate data
                await this.error.uploadData(uploadData);

                // Upload Chunk
                await this.uploadChunk(chunk, uploadData);

                // Update current bytes uploades
                await (totalUploadedBytes += chunk.size);

                // Update upload progress
                await this.updateUploadProgress(totalUploadedBytes);
            }

            // Mark upload as complete
            await this.setUploadComplete(item.wetransfer.transfer);
        }
    }

    async getChunk(fileinfo, partNumber, totalParts) {
        this.error.fileinfo(fileinfo);
        this.error.partNumber(partNumber);
        this.error.totalParts(totalParts);

        // Min Chunk Size
        const minChunkSize = 5242880;

        // Required chunk size
        const requiredChunkSize = Math.ceil(fileinfo.size / totalParts);

        // Calculated chunk size
        const chunkSize = requiredChunkSize < minChunkSize ? minChunkSize : requiredChunkSize;

        // Get chunk
        const chunk = fileinfo.slice(
            (partNumber - 1) * chunkSize,
            partNumber * chunkSize
        );

        return chunk;
    }

    async getUploadData(transfer, partNumber, multipartUploadId) {
        this.error.token(this.token);
        this.error.transfer(transfer);
        this.error.partNumber(partNumber);
        this.error.multipartUploadId(partNumber);

        // Prepare item data
        const data = {
            action: 'wordpresswetransfer--url',
            token: this.token,
            transferId: transfer.id,
            partNumber: partNumber,
            multipartUploadId: multipartUploadId
        };

        // Prepare URL
        const url = wordpresswetransfer.ajaxUrl;

        // Prepare Headers
        const headers = new Headers({
            'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
        });

        // Prepare Headers
        const options = {
            method: 'POST',
            headers: headers,
            body: qs.stringify(data)
        };

        // Prepare Request
        const request = new Request(url, options);

        // Fetch
        return await fetch(request)
            .then((response) => {
                this.error.response(response);

                return response.json();
            })
            .then((uploadData) => {
                this.error.uploadData(uploadData);

                return uploadData;
            })
            .catch((error) => {
                throw new Error(error.message);
            });
    }

    async uploadChunk(chunk, uploadData) {
        this.error.chunk(chunk);
        this.error.uploadData(uploadData);

        // Prepare Headers
        const options = {
            method: 'PUT',
            body: chunk
        };

        // Prepare Request
        const request = new Request(uploadData.upload_url, options);

        return await fetch(request)
            .then((response) => {
                this.error.response(response);
                return true;
            })
            .catch((error) => {
                throw new Error(error.message);
            });
    }

    async setUploadComplete(transfer) {
        this.error.token(this.token);
        this.error.transfer(transfer);

        // Prepare item data
        const data = {
            action: 'wordpresswetransfer--complete-transfer',
            token: this.token,
            transferId: transfer.id
        };

        // Prepare URL
        const url = wordpresswetransfer.ajaxUrl;

        // Prepare Headers
        const headers = new Headers({
            'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
        });

        // Prepare Headers
        const options = {
            method: 'POST',
            headers: headers,
            body: qs.stringify(data)
        };

        // Prepare Request
        const request = new Request(url, options);

        return await fetch(request)
            .then((response) => {
                this.error.response(response);

                return response.json();
            })
            .then((response) => {
                if (!this.validate.transfer(response)) {
                    this.updateContent(FailedTemplate);
                }

                return true;
            })
            .catch((error) => {
                throw new Error(error.message);
            });
    }

    async finished() {
        this.updateContent(SuccessTemplate, [
            {'${url}': this.transfer.shortened_url}
        ]);

        this.fireSuccessEvent();
    }


}
