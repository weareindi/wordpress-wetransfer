import ValidationService from '../../Services/ValidationService/ValidationService';

export default class ErrorService {
    constructor() {
        this.validate = new ValidationService();
    }

    variablesAvailable() {
        if (typeof wordpresswetransfer === 'undefined') {
            throw new Error(`Required variable ${'wordpresswetransfer'} is not available`);
        }

        if (!wordpresswetransfer.pluginDir) {
            throw new Error(`Required variable ${'wordpresswetransfer.pluginDir'} is not available`);
        }
    }

    featuresAvailable() {
        if (!window.FileList) {
            throw new Error('The FileList API is not supported in this browser');
        }

        if (!window.fetch) {
            throw new Error('The Fetch API is not supported in this browser');
        }
    }

    files(filesArray) {
        if (filesArray.length === 0) {
            throw new Error('No input files');
        }

        Array.forEach(filesArray, (file) => {
            if (!(file instanceof File)) {
                throw new Error('Input files is not a File instance');
            }
        });

        // Does total combined file bytes exceeds max allowed?
        if (!this.validate.uploadLimit(filesArray)) {
            throw new Error('Your files exceed the maximum allowed of 2GB');
        }
    }

    response(response) {
        if (response.status !== 200) {
            throw new Error(response);
        }
    }

    success(response) {
        if (!response.success) {
            throw new Error(response);
        }
    }

    transfer(transfer) {
        if (!transfer.id) {
            throw new Error('transfer is not defined');
        }

        if (!transfer.id) {
            throw new Error('transfer id is not defined');
        }
    }

    transferItems(transferItems) {
        if (!transferItems) {
            throw new Error('transferItems is not defined');
        }
    }

    uploadData(uploadData) {
        if (!uploadData) {
            throw new Error('uploadData not defined');
        }

        if (!uploadData.upload_url) {
            throw new Error('No upload url');
        }
    }

    fileinfo(fileinfo) {
        if (!fileinfo) {
            throw new Error('No fileinfo defined');
        }

        if (!(fileinfo instanceof File)) {
            throw new Error('fileinfo is not an instance of File');
        }
    }

    totalParts(totalParts) {
        if (!totalParts) {
            throw new Error('totalParts not defined');
        }
    }

    partNumber(partNumber) {
        if (!partNumber) {
            throw new Error('partNumber not defined');
        }
    }

    token(token) {
        if (!token) {
            throw new Error('token not defined');
        }
    }

    multipartUploadId(multipartUploadId) {
        if (!multipartUploadId) {
            throw new Error('multipartUploadId not defined');
        }
    }

    items(items) {
        if (!items) {
            throw new Error('items not defined');
        }
    }

    chunk(chunk) {
        if (!chunk) {
            throw new Error('chunk is not defined');
        }

        if (!(chunk instanceof Blob)) {
            throw new Error('chunk is not a Blob');
        }
    }
}
