/* --------------------------------------------------------------
 namespace.js 2015-10-13 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

(function () {

	'use strict';

	/**
	 * Class Namespace
	 *
	 * @param {string} name The namespace name must be unique within the app.
	 * @param {string} source Complete URL to the namespace modules directory (without trailing slash).
	 * @param {array} collections Contains collection instances to be included in the namespace.
	 *
	 * @constructor JSE/Core/Namespace
	 */
	function Namespace(name, source, collections) {
		this.name = name;
		this.source = source;
		this.collections = collections; // contains the default instances
	}

	/**
	 * Initialize the namespace collections.
	 *
	 * This method will create new collection instances based in the original ones.
	 */
	Namespace.prototype.init = function () {
		var deferredCollection = [];

		for (var index in this.collections) {
			var collection = this.collections[index],
			    deferred = $.Deferred();

			deferredCollection.push(deferred);
			
			this[collection.name] = new jse.constructors.Collection(collection.name, collection.attribute, this);
			this[collection.name].init(null, deferred);
		}

		if (deferredCollection.length === 0) {
			return $.Deferred().resolve();
		}

		return $.when.apply(undefined, deferredCollection).promise();

	};

	jse.constructors.Namespace = Namespace;
})();
