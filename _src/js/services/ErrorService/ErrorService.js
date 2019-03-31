import ValidationService from '../../Services/ValidationService/ValidationService';

export default class ErrorService {
    constructor() {
        this.validate = new ValidationService();
    }

    checkPluginVariables() {
        if (typeof indiwt === 'undefined') {
            throw new Error(`Required variable ${'indiwt'} is not available`);
        }

        if (!indiwt.pluginDir) {
            throw new Error(`Required variable ${'indiwt.pluginDir'} is not available`);
        }
    }

    checkPluginFeaturesAvailable() {
        if (!window.FileList) {
            throw new Error('The FileList API is not supported in this browser');
        }
    }

    checkUploadResponse(response) {
        if (response.status !== 200) {
            throw new Error(response);
        }
    }

    checkSuccess(response) {
        if (!response.success) {
            throw new Error(response);
        }
    }

    checkTransfer(transfer) {
        if (!transfer) {
            throw new Error('transfer is not defined');
        }
    }

    checkTransferId(transfer_id) {
        if (!transfer_id) {
            throw new Error('transfer id is not defined');
        }
    }

    checkTransferFileObject(transfer_file) {
        if (!transfer_file) {
            throw new Error('transfer file is not defined');
        }

        if (!transfer_file.id) {
            throw new Error('transfer file has no id defined');
        }

        if (!transfer_file.name) {
            throw new Error('transfer file has no name defined');
        }

        if (!transfer_file.size) {
            throw new Error('transfer file has no size defined');
        }
    }

    checkPartNumber(part_number) {
        if (!part_number) {
            throw new Error('part number not defined');
        }
    }

    checkPartUpload(response) {
        if (!response) {
            throw new Error('part upload not defined');
        }

        if (!response.url) {
            throw new Error('part upload did not return the required url');
        }
    }

    checkToken(token) {
        if (!token) {
            throw new Error('token not defined');
        }
    }

    checkChunk(chunk) {
        if (!chunk) {
            throw new Error('chunk is not defined');
        }

        if (!(chunk instanceof Blob)) {
            throw new Error('chunk is not a Blob');
        }
    }

    checkUploadUrl(url) {
        if (!url) {
            throw new Error('url is not defined');
        }
    }

    checkAuth(response) {
        if (response.message) {
            throw new Error(response.message);
        }

        if (!response.token) {
            throw new Error('no token returned');
        }
    }
}
