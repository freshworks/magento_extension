Freshdesk = Class.create();
Freshdesk.prototype = {
	portalLinkSearchTemplate: 'freshdesk/portal',

	initialize: function () {
	},

	processLinks: function () {
		$$('a[href*="' + this.portalLinkSearchTemplate + '"]').each(function (el) {
			el.target = '_blank';
		});
	}
}

Freshdesk.Fields = {};
Freshdesk.Fields.Nested = Class.create();
Freshdesk.Fields.Nested.prototype = {
	id: '',
	mainSelect: null,
	level1Select: null,
	level2Select: null,
	level1block: null,
	level2block: null,
	options: [],

	initialize: function (id, options) {
		this.id = id;
		this.mainSelect = $(this.id);

		this.level1block = $(this.id + '_level1_block');
		this.level1Select = $(this.id + '_level1_block').select('select').first();

		this.level2block = $(this.id + '_level2_block');
		if (this.hasLevel2()) {
			this.level2Select = $(this.id + '_level2_block').select('select').first();
		}

		this.options = options;
	},

	optionChanged: function (el) {
		if (Object.isUndefined(el)) {
			return;
		}

		var mainValue = this.mainSelect.options[this.mainSelect.selectedIndex].value;

		if (this.isLevel2(el)) {
			return;
		} else if (this.isLevel1(el)) {
			if (!this.hasLevel2()) {
				return;
			}

			this.hideLevel2();

			var level1Value = this.level1Select.options[this.level1Select.selectedIndex].value;
			if (level1Value == '') {
				return;
			}

			var level2Options = [];
			for (var i = 0; i < this.options.length; i++) {
				if (this.options[i].value == mainValue) {
					var level1Options = this.options[i].children;
					for (var j = 0; j < level1Options.length; j++) {
						if (level1Options[j].value == level1Value) {
							level2Options = level1Options[j].children;
							break;
						}
					}
					break;
				}
			}

			if (level2Options.length > 0) {
				this.showLevel2(level2Options);
			}


		} else {
			this.hideLevel1();
			this.hideLevel2();

			if (mainValue == '') {
				return;
			}

			var level1Options = [];
			for (var i = 0; i < this.options.length; i++) {
				if (this.options[i].value == mainValue) {
					level1Options = this.options[i].children;
					break;
				}
			}

			if (level1Options.length > 0) {
				this.showLevel1(level1Options);
			}
		}
	},

	isMain: function (el) {
		return el.id == this.id;
	},

	isLevel1: function (el) {
		return el.id == this.level1Select.id;
	},

	isLevel2: function (el) {
		return this.hasLevel2() && el.id == this.level2Select.id;
	},

	hasLevel2: function () {
		return Object.isElement(this.level2block);
	},

	showLevel1: function (level1options) {
		for (var i = 0; i < level1options.length; i++) {
			this.level1Select.insert('<option value="' + level1options[i].value + '">' + level1options[i].label + '</option>');
		}

		this.level1Select.selectedIndex = 0;
		this.level1Select.className = this.mainSelect.className;

		this.level1block.show();
	},

	hideLevel1: function () {
		this.level1block.hide();

		this.level1Select.select('option').each(Element.remove);
		this.level1Select.className = '';
	},

	showLevel2: function (level2options) {
		if (!this.hasLevel2()) {
			return;
		}

		for (var i = 0; i < level2options.length; i++) {
			this.level2Select.insert('<option value="' + level2options[i].value + '">' + level2options[i].label + '</option>');
		}

		this.level2Select.selectedIndex = 0;
		this.level2Select.className = this.mainSelect.className;

		this.level2block.show();
	},

	hideLevel2: function () {
		if (!this.hasLevel2()) {
			return;
		}

		this.level2block.hide();

		this.level2Select.select('option').each(Element.remove);
		this.level2Select.className = '';
	}
};

Freshdesk.Customer = {};
Freshdesk.Customer.View = {};
Freshdesk.Customer.View.Tickets = Class.create();
Freshdesk.Customer.View.Tickets.prototype = {
	elLastOrdersId: 'dd-lastOrders',
	elTicketsDDId: 'dd-freshdesk_tickets',
	elTicketsDTId: 'dt-freshdesk_tickets',

	initialize: function () {
	},

	process: function () {
		if (!Object.isElement($(this.elLastOrdersId)) || !Object.isElement($(this.elTicketsDDId)) || !Object.isElement($(this.elTicketsDTId))) {
			return;
		}

		$(this.elLastOrdersId).insert({after: $(this.elTicketsDDId)}).insert({after: $(this.elTicketsDTId)});
	}
};

Freshdesk.System = {};
Freshdesk.System.Config = Class.create();
Freshdesk.System.Config.prototype = {
	elDomainId: 'freshdesk_account_domain',

	elTicketFieldLinkId: 'fd_order_id_ticket_fields_link',
	linkTicketField: 'ticket_fields',

	elFeedbackWidgetLinkId: 'fd_channels_feedback_widget_link',
	linkFeedbackWidget: 'admin/widget_config',

	initialize: function () {
	},

	processLinks: function () {
		if (!Object.isElement($(this.elDomainId))) {
			return;
		}

		$(this.elDomainId).observe('change', this.changeLinks.bind(this));
		$(this.elDomainId).observe('keyup', this.changeLinks.bind(this));
		$(this.elDomainId).observe('mouseup', this.changeLinks.bind(this));

		this.changeLinks();
	},

	changeLinks: function () {
		if (!Object.isElement($(this.elDomainId)) || !Object.isElement($(this.elTicketFieldLinkId))) {
			return;
		}

		var fdUrl = ($(this.elDomainId).value.indexOf("://") > 0 ? '' : 'http://') + $(this.elDomainId).value + '/';

		$(this.elTicketFieldLinkId).href = fdUrl + this.linkTicketField;
		$(this.elFeedbackWidgetLinkId).href = fdUrl + this.linkFeedbackWidget;
	}
}

Freshdesk.System.Config.Widget = Class.create();
Freshdesk.System.Config.Widget.prototype = {
	elEnableId: 'freshdesk_channels_enable_feedback_widget',
	elCodeId: 'freshdesk_channels_feedback_widget',

	initialize: function () {
	},

	process: function () {
		if (!Object.isElement($(this.elEnableId))) {
			return;
		}

		$(this.elEnableId).observe('change', this.toggle.bind(this));

		this.toggle();
	},

	toggle: function () {
		if (this.isEnable()) {
			$(this.elCodeId).up().up().show();
		} else {
			$(this.elCodeId).up().up().hide();
		}
	},

	isEnable: function () {
		return $(this.elEnableId).value == 1;
	}
}

Freshdesk.System.Config.CustomerView = Class.create();
Freshdesk.System.Config.CustomerView.prototype = {
	elEnableId: 'freshdesk_customer_view_enable_customer_view',
	elChild1Id: 'freshdesk_customer_view_enable_ticket_tab',
	elChild2Id: 'freshdesk_customer_view_enable_recent_ticket',

	initialize: function () {
	},

	process: function () {
		if (!Object.isElement($(this.elEnableId))) {
			return;
		}

		$(this.elEnableId).observe('change', this.toggle.bind(this));

		this.toggle();
	},

	toggle: function () {
		if (this.isEnable()) {
			/*$(this.elChild1Id).up().up().show();*/
			$(this.elChild2Id).up().up().show();
		} else {
			/*$(this.elChild1Id).up().up().hide();*/
			$(this.elChild2Id).up().up().hide();
		}
	},

	isEnable: function () {
		return $(this.elEnableId).value == 1;
	}
}

Event.observe(window, 'load', function () {
	new Freshdesk().processLinks();
	new Freshdesk.Customer.View.Tickets().process();
	new Freshdesk.System.Config().processLinks();
	new Freshdesk.System.Config.Widget().process();
	new Freshdesk.System.Config.CustomerView().process();
});