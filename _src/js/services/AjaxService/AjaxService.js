import ErrorService from '../../Services/ErrorService/ErrorService';
import ValidationService from '../../Services/ValidationService/ValidationService';
import qs from 'qs';

/**
 * AjaxService
 */
export default class AjaxService {
    /**
     * Register and prepare settings for the fetch request
     * @param {object} settings Unique settings for the fetch attempt
     */
    constructor(settings) {
        // Default Settings
        this.defaultSettings = {
            url: owpwt.ajaxUrl,
            data: {},
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
            }
        };

        // Merge Settings
        this.settings = Object.assign({}, this.defaultSettings, settings);
    }

    /**
     * Fetch using the 'POST' method
     * @return {String} Response as JSON or string
     */
    async post() {
        // Prepare Options
        const options = {
            method: 'POST',
            headers: new Headers(this.settings.headers),
            body: qs.stringify(this.settings.data)
        };

        // Prepare Request
        const request = new Request(this.settings.url, options);

        // Fetch
        return await fetch(request)
            .then((response) => {
                // Error check
                const errorService = new ErrorService();
                errorService.response(response);

                return response.text();
            })
            .then((responseString) => {
                const validationService = new ValidationService();

                if (!validationService.isJson(responseString)) {
                    return responseString;
                }

                return JSON.parse(responseString);
            })
            .catch((error) => {
                throw new Error(error.message);
            });
    }

    /**
     * Fetch using the 'PUT' method
     * @return {String} Response as string
     */
    async put() {
        // Prepare Options
        const options = {
            method: 'PUT',
            body: this.settings.data
        };

        // Prepare Request
        const request = new Request(this.settings.url, options);

        // Fetch
        return await fetch(request)
            .then((response) => {
                // Error check
                const errorService = new ErrorService();
                errorService.response(response);

                return response;
            })
            .catch((error) => {
                throw new Error(error.message);
            });
    }
}
