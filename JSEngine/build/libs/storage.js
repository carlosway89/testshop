/* --------------------------------------------------------------
 storage.js 2015-10-13 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.storage = jse.libs.storage || {};

/**
 * Storage API Library
 *
 * This library handles the HTML storage functionality.
 *
 * @namespace JSE/Libs/storage
 */
(function (exports) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
	// ------------------------------------------------------------------------

	/**
	 * JavaScript Storage Object
	 *
	 * @name Core/StorageAP.storage
	 * @public
	 * @type {boolean}
	 */
	var webstorage = (typeof Storage !== 'undefined') ? true : false;

	/**
	 * Store value to the browser.
	 *
	 * @name Core/StorageAP.store
	 * @private
	 * @method
	 *
	 * @param {object} store Storage handler object.
	 * @param {boolean} overwrite Whether to overwrite an existing storage value.
	 * @param {string} value String defining the value key name to be stored.
	 * @param {object} dataset Contains the information to be stored.
	 * @param {number} userId User id will be used to identify stored information of a specific user.
	 *
	 * @return {boolean} Returns the operation result.
	 */
	var _store = function (store, overwrite, value, dataset, userId) {

		var dataCache = null,
			result = null;

		if (webstorage) {
			dataCache = store.getItem('user_' + userId);
			dataCache = dataCache || '{}';
			dataCache = $.parseJSON(dataCache);

			if (overwrite || dataCache[value] === undefined) {
				dataCache[value] = dataset;
			} else {
				dataCache[value] = $.extend({}, dataCache[value], dataset);
			}

			result = JSON.stringify(dataCache);
			store.setItem('user_' + userId, result);
			return true;
		}
		return false;
	};

	/**
	 * Restore data from storage.
	 *
	 * @name Core/StorageAP._restore
	 * @private
	 * @method
	 *
	 * @param {object} store Storage handler object.
	 * @param {string} value Value key name to be retrieved.
	 * @param {number} userId User id that owns the value.
	 *
	 * @return {object} Returns the value if exists or an empty object if not.
	 */
	var _restore = function (store, value, userId) {

		var dataCache = null;

		if (webstorage) {
			dataCache = store.getItem('user_' + userId);
			dataCache = dataCache || '{}';
			dataCache = $.parseJSON(dataCache);
			return dataCache[value] || {};
		}
		return {};
	};

	// ------------------------------------------------------------------------
	// NAMESPACE FUNCTIONALITY
	// ------------------------------------------------------------------------

	/**
	 * Store Data to Browser Storage
	 *
	 * @name Core/StorageAP.storeData
	 * @public
	 * @method
	 *
	 * @param {array} destinations Array containing where to store the data (session, local, server).
	 * @param {object} dataset Data to be stored.
	 * @param {boolean} overwrite Whether to overwrite existing values.
	 *
	 * @return {object} Returns a promise object.
	 */
	var _storeData = function (destinations, dataset, overwrite) {

		var userID = $('body').data().userId,
			resultObject = {},
			promises = [];

		$.each(destinations, function (dest, value) {
			var localDeferred = $.Deferred();
			promises.push(localDeferred);

			switch (dest) {
				case 'session':
					resultObject.session = _store(sessionStorage, overwrite, value, dataset, userID);
					localDeferred.resolve(resultObject);
					break;
				case 'local':
					resultObject.local = _store(localStorage, overwrite, value, dataset, userID);
					localDeferred.resolve(resultObject);
					break;
				case 'server':
					localDeferred.resolve(resultObject);
					break;
				default:
					break;
			}
		});

		return $.when.apply(undefined, promises).promise();

	};

	/**
	 * Restore Data from Storage
	 *
	 * @name Core/StorageAP.restoreData
	 * @public
	 * @method
	 *
	 * @param {array} sources Defines the source of the data to be retrieved (session, local, server).
	 *
	 * @return {object} Returns the promise object.
	 */
	var _restoreData = function (sources) {
		var userID = $('body').data().userId,
			resultObject = {},
			promises = [];

		$.each(sources, function (src, value) {
			var localDeferred = $.Deferred();
			promises.push(localDeferred);

			switch (src) {
				case 'session':
					resultObject.session = _restore(sessionStorage, value, userID);
					localDeferred.resolve(resultObject);
					break;
				case 'local':
					resultObject.local = _restore(localStorage, value, userID);
					localDeferred.resolve(resultObject);
					break;
				case 'server':
					localDeferred.resolve(resultObject);
					break;
				default:
					break;
			}
		});

		return $.when
			.apply(undefined, promises)
			.then(function (result) {
				      return $.extend(true, {}, result.local || {}, result.session || {}, result.server || {});
			      })
			.promise();
	};

	// ------------------------------------------------------------------------
	// VARIABLE EXPORT
	// ------------------------------------------------------------------------

	exports.store = _storeData;
	exports.restore = _restoreData;

}(jse.libs.storage));
