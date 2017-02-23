define(function (require) {

	var $ = require('jquery');
	var Ajax = require('elgg/Ajax');

	var popup = {
		/**
		 * Update notifications badge with unread count
		 *
		 * @param {int} unread Unread count
		 * @returns {void}
		 */
		setNewBadge: function (unread) {
			if (unread > 0) {
				$('#notifications-new').text(unread).removeClass('hidden');
			} else {
				$('#notifications-new').text(unread).addClass('hidden');
			}
		},
		ticker: function () {
			var ajax = new Ajax(false);
			ajax.path('notifications/ticker', {
				data: {
					view: 'json'
				}
			}).done(function (output) {
				popup.setNewBadge(output.new);
			});
		}
	};

	$(document).on('open', '.elgg-notifications-popup', function () {
		var $loader = $('<div>').addClass('elgg-ajax-loader');
		$('#notifications-messages').html($loader);

		var ajax = new Ajax(false);
		ajax.path('notifications/view', {
			data: {
				view: 'json'
			}
		}).done(function (output) {
			$('#notifications-messages').html(output.list);
			popup.setNewBadge(output.new);
		});
	});

	$(document).on('click', '.notification-item.notification-new a', function () {
		var ajax = new Ajax(false);
		var $elem = $(this).closest('.notification-item');

		ajax.action('notifications/mark_read', {
			data: $elem.data()
		});
	});

	var ticker = function () {
		popup.ticker();
		timeout();
	};
	var timeout = function () {
		setTimeout(ticker, 60000);
	};
	ticker();

	return popup;
});
