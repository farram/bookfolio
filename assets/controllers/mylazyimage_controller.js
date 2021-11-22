import { Controller } from 'stimulus';

export default class extends Controller {
    connect() {
        this.element.addEventListener('lazy-image:connect', this._onConnect);
        this.element.addEventListener('lazy-image:ready', this._onReady);
    }

    disconnect() {
        // You should always remove listeners when the controller is disconnected to avoid side-effects
        this.element.removeEventListener('lazy-image:connect', this._onConnect);
        this.element.removeEventListener('lazy-image:ready', this._onReady);
    }

    _onConnect(event) {
        // The lazy-image behavior just started
    }

    _onReady(event) {
        // The HD version has just been loaded
    }
}