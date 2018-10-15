/**
 * ValidationService
 */
export default class ValidationService {
    /**
     * An empty constructor
     */
    constructor() {}

    /**
     * Does the total of all file uploads exceed the maximum threshold?
     * @param  {array} files An array of File objects
     * @return {Boolean}
     */
    uploadLimit(files) {
        // Max allowed bytes per transfer
        const maxTotalBytes = 2147483648;

        // Combine total bytes in all files
        let totalFileBytes = 0;

        for (var i = 0; i < files.length; i++) {
            const file = files[i];

            totalFileBytes += file.size;
        }

        // Does total combined file bytes exceeds max allowed?
        if (totalFileBytes >= maxTotalBytes) {
            return false;
        }

        return true;
    }

    /**
     * Does the transfer response seem ok?
     * @param  {object} response
     * @return {Boolean}
     */
    transfer(response) {
        if (!response) {
            return false;
        }

        if (!response.id) {
            return false;
        }

        if (!response.name) {
            return false;
        }

        if (!response.size) {
            return false;
        }

        if (!response.chunk_size) {
            return false;
        }

        return true;
    }

    /**
     * Does a string parse as JSON?
     * @param  {string} string
     * @return {Boolean}
     */
    isJson(string) {
        string = typeof string !== 'string' ? JSON.stringify(string) : string;

        try {
            string = JSON.parse(string);
        } catch (e) {
            return false;
        }

        if (typeof string === 'object' && string !== null) {
            return true;
        }

        return false;
    }
}
