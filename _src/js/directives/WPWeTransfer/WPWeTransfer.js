import FormTemplate from '../../templates/FormTemplate.html';
import FileItemTemplate from '../../templates/FileItemTemplate.html';
import SuccessTemplate from '../../templates/SuccessTemplate.html';
import FailedTemplate from '../../templates/FailedTemplate.html';
import TransferringTemplate from '../../templates/TransferringTemplate.html';

import ErrorService from '../../Services/ErrorService/ErrorService';
import ValidationService from '../../Services/ValidationService/ValidationService';

import qs from 'qs';
import axios from 'axios';
import closest from 'closest';
import filesize from 'filesize';
import 'custom-event-polyfill';

export class WPWeTransfer {
    constructor(surface) {
        this.error = new ErrorService();
        this.validate = new ValidationService();

        this.settings = {
            maximumBytes: 2147483648 // 2GB
        };

        this.surface = surface;
        this.run();
    }

    async run() {
        try {
            await this.error.checkPluginVariables();
            await this.error.checkPluginFeaturesAvailable();
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
            await this.elements.surface.classList.add('ozpital-wpwetransfer--enhanced');
            await this.loadStylesheet();
            await this.replaceTemplate(FormTemplate);
            await this.registerForm();
            await this.updateFormTemplate();
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

    registerForm() {
        this.elements.form = this.elements.surface.querySelector('.ozpital-wpwetransfer-form');
        this.elements.formLabel = this.elements.form.querySelector('.ozpital-wpwetransfer-form__label-text');
        this.elements.formNotice = this.elements.form.querySelector('.ozpital-wpwetransfer-form__notice-text');
        this.elements.input = this.elements.form.querySelector('.ozpital-wpwetransfer-form__input--file');
        this.elements.list = this.elements.form.querySelector('.ozpital-wpwetransfer-form__list');
        this.elements.submit = this.elements.form.querySelector('.ozpital-wpwetransfer-form__button--submit');
    }

    registerProgressBar() {
        this.elements.progress = this.elements.surface.querySelector('.ozpital-wpwetransfer-progress');
        this.elements.progressBackground = this.elements.surface.querySelector('.ozpital-wpwetransfer-progress__background');
        this.elements.progressAmount = this.elements.surface.querySelector('.ozpital-wpwetransfer-progress__amount');
    }

    updateProgressBar(percent) {
        const length = this.elements.progressAmount.getTotalLength();
        const percentageToOffset = length * ((100 - percent) / 100);
        this.elements.progressAmount.style.strokeDasharray = length
        this.elements.progressAmount.style.strokeDashoffset = percentageToOffset
    }

    triggerTransferProgressEvent(progress) {
        const event = new CustomEvent('ozpital-wpwetransfer-transferring', {
            detail: {
                progress: progress
            }
        });

        document.dispatchEvent(event);
    }

    triggerFilesChangeEvent() {
        const event = new CustomEvent('ozpital-wpwetransfer-change', {
            detail: {
                files: this.files
            }
        });

        document.dispatchEvent(event);
    }

    triggerSuccessEvent() {
        const event = new CustomEvent('ozpital-wpwetransfer-success', {
            detail: {
                id: this.transfer.id,
                url: this.transfer.url,
                transfer: this.transfer
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
            this.updateFormTemplate();
            this.populateFilesList();
            this.processStatus();
            this.resetInput();
            this.triggerFilesChangeEvent();
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
            this.updateFormTemplate();
            this.populateFilesList();
            this.processStatus();
            this.triggerFilesChangeEvent();
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
            this.updateFormTemplate();
            this.populateFilesList();
            this.processStatus();
            this.triggerFilesChangeEvent();
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

    bytesToHuman(bytes) {
        return filesize(bytes);
    }

    populateFilesList() {
        this.elements.list.innerHTML = '';

        for (var i = 0; i < this.files.length; i++) {
            const file = this.files[i];

            let template = FileItemTemplate;
            template = template.split('${index}').join(i);
            template = template.split('${filename}').join(file.name);
            template = template.split('${size}').join(this.bytesToHuman(file.size));
            template = template.split('${type}').join(file.name.split('.').pop());
            this.elements.list.innerHTML += template;
        }
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

    updateStatus(status) {
        this.elements.surface.setAttribute('status', status);
    }

    /**
     * Update template content
     * @param {String} template     html
     * @param {Array}  replacements An array of key/value replacement objects
     */
    replaceTemplate(template, replacements) {
        if ((typeof replacements !== 'undefined') && (replacements.length > 0)) {
            for (var i = 0; i < replacements.length; i++) {
                const replacement = replacements[i];

                for (const find in replacement) {
                    const replace = replacement[find];
                    template = template.split(find).join(replace);
                }
            }
        }

        this.elements.content.innerHTML = template;
    }

    /**
     * Update Form Template Variables
     */
    updateFormTemplate() {
        let label = `Add your files`;
        let remaining = `Add up to ${this.bytesToHuman(this.settings.maximumBytes)}`;

        if (typeof this.files !== 'undefined' && this.files.length > 0) {

            label = `Add more files`;

            // Calculate Used Bytes
            let totalUsedBytes = 0;
            for (var i = 0; i < this.files.length; i++) {
                totalUsedBytes += this.files[i].size;
            }

            let remainingBytes = (this.settings.maximumBytes - totalUsedBytes);
            remaining = `${this.bytesToHuman(remainingBytes)} remaining`;

            if (remainingBytes <= 0) {

                label = `${this.bytesToHuman(this.settings.maximumBytes)} Limit Exceeded`;
                remaining = `You are ${this.bytesToHuman(totalUsedBytes - this.settings.maximumBytes)} over`;
                this.disableSubmit();
            }
        }

        this.elements.formLabel.innerHTML = label;
        this.elements.formNotice.innerHTML = remaining;
    }

    /**
     * When the user clicks 'Transfer'
     */
    async submit() {
        try {
            await this.disableSubmit();
            await this.prepareUploadedBytesCounters();
            await this.updateStatus('transfering');
            await this.replaceTemplate(TransferringTemplate);
            await this.registerProgressBar();
            await this.triggerTransferProgressEvent(0);
            await this.updateProgressBar(0);
            await this.getToken();
            await this.getTransferObject();
            await this.mergeFilesIntoTransfer();
            await this.uploadFiles();
            await this.finalizeTransfer();
            await this.finish();
        } catch(error) {
            console.error(error);
            this.updateStatus('error');
            await this.replaceTemplate(FailedTemplate);
        }
    }

    /**
     * Prepare counters to track uploaded bytes
     */
    prepareUploadedBytesCounters() {
        // Prepare total uploadable bytes counter
        this.totalUploadableBytes = 0;

        for (var i = 0; i < this.files.length; i++) {
            const file = this.files[i];

            this.totalUploadableBytes += file.size;
        }

        // Prepare total uploadable bytes counter
        this.totalBytesRemaining = this.totalUploadableBytes;
    }

    /**
     * Get WeTransfer Auth Token
     */
    async getToken() {
        const ajaxSettings = {
            url: owpwt.ajaxUrl,
            method: 'post',
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'},
            data: qs.stringify({
                action: 'owpwt--auth'
            })
        };

        // Prepare Request
        return await axios.request(ajaxSettings)
            .then((response) => {
                this.error.checkAuth(response.data);

                return this.token = response.data.token;
            })
            .catch((error) => {
                throw new Error(error.message);
            });
    }

    /**
     * Get WeTransfer Transfer Object
     *
     * We provide WeTransfer with a list of files we want to upload.
     * In return we get a prepared transfer object.
     */
    async getTransferObject() {
        const files = [];

        for (var i = 0; i < this.files.length; i++) {
            const file = this.files[i];

            files.push({
                name: file.name,
                size: file.size
            });
        }

        const ajaxSettings = {
            url: owpwt.ajaxUrl,
            method: 'post',
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'},
            data: qs.stringify({
                action: 'owpwt--transfer',
                token: this.token,
                files: files
            })
        };

        // Prepare Request
        return await axios.request(ajaxSettings)
            .then((response) => {
                this.error.checkTransfer(response.data);

                return this.transfer = response.data;
            })
            .catch((error) => {
                throw new Error(error.message);
            });
    }

    /**
     * Merge array of File Instances Into Transfer Object
     *
     * We needed a way to corrolate the correct file from the users system with the prepared WeTransfer Transfer object.
     * Merging seemed the easiest to manage.
     */
    mergeFilesIntoTransfer() {
        for (var i = 0; i < this.transfer.files.length; i++) {
            const transfer_file = this.transfer.files[i];

            const file = this.files.find((file) => {
                return (file.name === transfer_file.name && file.size === transfer_file.size);
            });
            transfer_file.file = file;
        }
    }

    /**
     * Upload files
     */
    async uploadFiles() {
        for (const transfer_file of this.transfer.files) {

            // Prepare uploaded parts counter
            let uploaded_parts = 0;

            for (let part_number = 1; part_number <= transfer_file.multipart.part_numbers; part_number++) {
                // Get chunk
                const chunk = await this.getFilePartChunk(transfer_file, part_number);

                // Get upload data
                const upload_url = await this.getFilePartUploadUrl(transfer_file, part_number);

                // Upload chunk
                await this.uploadFilePartChunk(chunk, upload_url);

                // Update uploaded parts counter
                await (uploaded_parts++);
            }

            // Mark upload as complete
            await this.completeFileUpload(transfer_file, uploaded_parts);
        }
    }

    /**
     * Split File instance into required chunk size
     * @param  {Object}  transfer_file A complete transfer file object
     * @param  {Integer} part_number   The required part of the file
     * @return {File}                  A chunk of the required file instance
     */
    getFilePartChunk(transfer_file, part_number) {
        // Get chunk
        return transfer_file.file.slice(
            (part_number - 1) * transfer_file.multipart.chunk_size,
            part_number * transfer_file.multipart.chunk_size
        );
    }

    /**
     * Get upload url for file part
     * @param  {Object}  transfer_file A complete transfer file object
     * @param  {Integer} part_number   The required part of the file
     * @return {String}                A valid part upload url
     */
    async getFilePartUploadUrl(transfer_file, part_number) {
        this.error.checkToken(this.token);
        this.error.checkTransferId(this.transfer.id);
        this.error.checkTransferFileObject(transfer_file);
        this.error.checkPartNumber(part_number);

        const ajaxSettings = {
            url: owpwt.ajaxUrl,
            method: 'post',
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'},
            data: qs.stringify({
                action: 'owpwt--url',
                token: this.token,
                transfer_id: this.transfer.id,
                part_number: part_number,
                file_id: transfer_file.id
            })
        };

        // Prepare Request
        return await axios.request(ajaxSettings)
            .then((response) => {
                this.error.checkPartUpload(response.data);

                return response.data.url;
            })
            .catch((error) => {
                throw new Error(error.message);
            });
    }

    /**
     * Calculate and update upload progress percentage
     * @param  {Integer} fileLoadedBytes [description]
     * @param  {Integer} fileTotalBytes  [description]
     */
    updateUploadProgress(fileLoadedBytes, fileTotalBytes) {
        let x = this.totalBytesRemaining - fileLoadedBytes;
        if (fileLoadedBytes === fileTotalBytes) {
            x = this.totalBytesRemaining -= fileTotalBytes;
        }

        let progress = 100 - ((x / this.totalUploadableBytes) * 100);
        document.querySelector('.ozpital-wpwetransfer-transfering__percentage').setAttribute('data-amount', Math.round(progress));

        this.updateProgressBar(progress);
        this.triggerTransferProgressEvent(progress);
    }

    /**
     * Upload file part chunk to url
     * @param  {File}    chunk      A valid chunk of file
     * @param  {String}  upload_url A valid url expecting the chunk
     * @return {Object}             A confirmation response
     */
    async uploadFilePartChunk(chunk, upload_url) {
        this.error.checkChunk(chunk);
        this.error.checkUploadUrl(upload_url);

        const ajaxSettings = {
            url: upload_url,
            method: 'put',
            data: chunk,
            onUploadProgress: (progressEvent) => {
                this.updateUploadProgress(progressEvent.loaded, progressEvent.total);
            }
        };

        // Prepare Request
        return await axios.request(ajaxSettings)
            .then((response) => {
                this.error.checkUploadResponse(response);

                return response;
            })
            .catch((error) => {
                throw new Error(error.message);
            });
    }

    /**
     * Set file upload as complete
     * @param  {Object}  transfer_file  A valid WeTransfer file transfer object
     * @param  {Integer} uploaded_parts A counter of uploaded parts
     * @return {Boolean}
     */
    async completeFileUpload(transfer_file, uploaded_parts) {
        const ajaxSettings = {
            url: owpwt.ajaxUrl,
            method: 'post',
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'},
            data: qs.stringify({
                action: 'owpwt--complete-file-upload',
                token: this.token,
                transfer_id: this.transfer.id,
                file_id: transfer_file.id,
                uploaded_parts: uploaded_parts
            })
        };

        // Prepare Request
        return await axios.request(ajaxSettings)
            .then((response) => {
                if (!this.validate.transfer(response.data)) {
                    this.replaceTemplate(FailedTemplate);
                    return false;
                }

                return true;
            })
            .catch((error) => {
                throw new Error(error.message);
            });
    }

    /**
     * Finalise entire transfer
     */
    async finalizeTransfer() {
        const ajaxSettings = {
            url: owpwt.ajaxUrl,
            method: 'post',
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'},
            data: qs.stringify({
                action: 'owpwt--finalize-transfer',
                token: this.token,
                transfer_id: this.transfer.id
            })
        };

        // Prepare Request
        return await axios.request(ajaxSettings)
            .then((response) => {
                this.transfer = response.data;
            })
            .catch((error) => {
                throw new Error(error.message);
            });
    }

    /**
     * Do the final tasks
     */
    finish() {
        this.updateStatus('success');
        this.replaceTemplate(SuccessTemplate, [
            {'${message}': owpwt.transferCompleteMessage},
            {'${showurl}': owpwt.transferCompleteShowUrl},
            {'${url}': this.transfer.url}
        ]);

        this.triggerSuccessEvent();
    }
}
