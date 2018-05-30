export default class ValidationService {
    constructor() {}

    uploadLimit(filesArray) {
        // Max allowed bytes per transfer
        const maxTotalBytes = 2147483648;

        // Combine total bytes in all files
        let totalFileBytes = 0;
        Array.forEach(filesArray, (fileinfo) => {
            totalFileBytes += fileinfo.size;
        });

        // Does total combined file bytes exceeds max allowed?
        if (totalFileBytes >= maxTotalBytes) {
            return false;
        }

        return true;
    }

    transfer(completeResponse) {
        if (!completeResponse) {
            return false;
        }

        if (!completeResponse.ok) {
            return false;
        }

        return true;
    }
}
