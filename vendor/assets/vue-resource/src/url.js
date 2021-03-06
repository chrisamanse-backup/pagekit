var _ = require('./util');

/**
 * Url provides URL templating.
 *
 * @param {String} url
 * @param {Object} params
 */

function Url (url, params) {

    var urlParams = {}, queryParams = {}, options = url, query;

    if (!_.isPlainObject(options)) {
        options = {url: url, params: params};
    }

    options = _.extend({}, Url.options, _.options('url', this, options));

    url = options.url.replace(/:([a-z]\w*)/gi, function (match, name) {

        if (options.params[name]) {
            urlParams[name] = true;
            return encodeUriSegment(options.params[name]);
        }

        return '';
    });

    if (!url.match(/^(https?:)?\//) && options.root) {
        url = options.root + '/' + url;
    }

    url = url.replace(/(^|[^:])[\/]{2,}/g, '$1/');
    url = url.replace(/(\w+)\/+$/, '$1');

    _.each(options.params, function (value, key) {
        if (!urlParams[key]) {
            queryParams[key] = value;
        }
    });

    query = Url.params(queryParams);

    if (query) {
        url += (url.indexOf('?') == -1 ? '?' : '&') + query;
    }

    return url;
}

/**
 * Url options.
 */

Url.options = {
    root: '',
    params: {}
};

/**
 * Encodes a Url parameter string.
 *
 * @param {Object} obj
 */

Url.params = function (obj) {

    var params = [];

    params.add = function (key, value) {

        if (_.isFunction (value)) {
            value = value();
        }

        if (value === null) {
            value = '';
        }

        this.push(encodeUriSegment(key) + '=' + encodeUriSegment(value));
    };

    serialize(params, obj);

    return params.join('&');
};

/**
 * Parse a URL and return its components.
 *
 * @param {String} url
 */

Url.parse = function (url) {

    var pattern = RegExp("^(?:([^:/?#]+):)?(?://([^/?#]*))?([^?#]*)(?:\\?([^#]*))?(?:#(.*))?"),
        matches = url.match(pattern);

    return {
        url: url,
        scheme: matches[1] || '',
        host: matches[2] || '',
        path: matches[3] || '',
        query: matches[4] || '',
        fragment: matches[5] || ''
    };
};

function serialize (params, obj, scope) {

    var array = _.isArray(obj), plain = _.isPlainObject(obj), hash;

    _.each(obj, function (value, key) {

        hash = _.isObject(value) || _.isArray(value);

        if (scope) {
            key = scope + '[' + (plain || hash ? key : '') + ']';
        }

        if (!scope && array) {
            params.add(value.name, value.value);
        } else if (hash) {
            serialize(params, value, key);
        } else {
            params.add(key, value);
        }
    });
}

function encodeUriSegment (value) {

    return encodeUriQuery(value, true).
        replace(/%26/gi, '&').
        replace(/%3D/gi, '=').
        replace(/%2B/gi, '+');
}

function encodeUriQuery (value, spaces) {

    return encodeURIComponent(value).
        replace(/%40/gi, '@').
        replace(/%3A/gi, ':').
        replace(/%24/g, '$').
        replace(/%2C/gi, ',').
        replace(/%20/g, (spaces ? '%20' : '+'));
}

module.exports = Url;
