import FormTemplate from '../../templates/FormTemplate.html';
import FileItemTemplate from '../../templates/FileItemTemplate.html';
import SuccessTemplate from '../../templates/SuccessTemplate.html';
import FailedTemplate from '../../templates/FailedTemplate.html';
import TransferringTemplate from '../../templates/TransferringTemplate.html';

import AjaxService from '../../Services/AjaxService/AjaxService';
import ErrorService from '../../Services/ErrorService/ErrorService';
import ValidationService from '../../Services/ValidationService/ValidationService';

import qs from 'qs';
import closest from 'closest';

export class WPWeTransfer {
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
        this.elements.content = this.surface.querySelector('.ozpital-wpwetransfer__content');
    }

    async initalise() {
        try {
            await this.elements.surface.classList.add('ozpital-wpwetransfer-enhanced');
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
        element.href = owpwt.pluginDir + '/assets/css/enhanced.css';
        document.body.appendChild(element);
    }

    replaceContent() {
        this.elements.content.innerHTML = FormTemplate;
    }

    registerForm() {
        this.elements.form = this.elements.surface.querySelector('.ozpital-wpwetransfer-form');
        this.elements.input = this.elements.form.querySelector('.ozpital-wpwetransfer-form__input--file');
        this.elements.list = this.elements.form.querySelector('.ozpital-wpwetransfer-form__list');
        this.elements.submit = this.elements.form.querySelector('.ozpital-wpwetransfer-form__button--submit');
    }

    fireSuccessEvent() {
        const event = new CustomEvent('ozpital-wpwetransfer-success', {
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

            if (!event.target.classList.contains('ozpital-wpwetransfer-item__button--delete') && !event.target.parentElement.classList.contains('ozpital-wpwetransfer-item__button--delete')) {
                return false;
            }

            this.removeFile(closest(event.target, '.ozpital-wpwetransfer-item').getAttribute('ozpital-wpwetransfer-item'));
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
        await (document.querySelector('.ozpital-wpwetransfer-transfering__amount').innerHTML = Math.round(progress));
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
                        local_identifier: 'owpwt--' + index
                    }
                }
            });
        });
    }

    async getToken() {
        const ajaxSettings = {
            data: {
                action: 'owpwt--auth'
            }
        };

        const ajax = new AjaxService(ajaxSettings);

        await ajax.post()
            .then((response) => {
                this.error.success(response);

                return this.token = response.token;
            });
    }

    async createTransfer() {
        const ajaxSettings = {
            data: {
                action: 'owpwt--transfer',
                token: this.token
            }
        };

        const ajax = new AjaxService(ajaxSettings);

        await ajax.post()
            .then((transferObject) => {
                this.error.transfer(transferObject);

                return this.transfer = transferObject;
            });
    }

    async addItems() {
        const transferItems = [];
        Array.forEach(this.items, (item) => {
            transferItems.push(item.wetransfer.item);
        });

        const ajaxSettings = {
            data: {
                action: 'owpwt--items',
                token: this.token,
                transferId: this.transfer.id,
                items: transferItems
            }
        };

        const ajax = new AjaxService(ajaxSettings);

        await ajax.post()
            .then((transferItems) => {
                this.error.transferItems(transferItems);

                Array.forEach(transferItems, (transferItem, transferItemIndex) => {
                    const index = this.items.findIndex((item) => {
                        return item.wetransfer.item.local_identifier === transferItem.local_identifier;
                    });

                    return this.items[index].wetransfer.transfer = transferItem;
                });
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

        const ajaxSettings = {
            data: {
                action: 'owpwt--url',
                token: this.token,
                transferId: transfer.id,
                partNumber: partNumber,
                multipartUploadId: multipartUploadId
            }
        };

        const ajax = new AjaxService(ajaxSettings);

        return await ajax.post()
            .then((uploadData) => {
                this.error.uploadData(uploadData);

                return uploadData;
            });
    }

    async uploadChunk(chunk, uploadData) {
        this.error.chunk(chunk);
        this.error.uploadData(uploadData);

        const ajaxSettings = {
            url: uploadData.upload_url,
            data: chunk
        };

        const ajax = new AjaxService(ajaxSettings);

        return await ajax.put()
            .then((response) => {
                return response;
            });
    }

    async setUploadComplete(transfer) {
        this.error.token(this.token);
        this.error.transfer(transfer);

        const ajaxSettings = {
            data: {
                action: 'owpwt--complete-transfer',
                token: this.token,
                transferId: transfer.id
            }
        };

        const ajax = new AjaxService(ajaxSettings);

        await ajax.post()
            .then((response) => {
                if (!this.validate.transfer(response)) {
                    this.updateContent(FailedTemplate);
                    return false;
                }

                return true;
            });
    }

    async finished() {
        this.updateContent(SuccessTemplate, [
            {'${url}': this.transfer.shortened_url}
        ]);

        this.fireSuccessEvent();
    }


}