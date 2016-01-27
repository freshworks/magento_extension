Freshdesk = {};

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

Freshdesk.Account = Class.create();
Freshdesk.Account.prototype = {
	label: '',
	path: '',
	position: 0,

	initialize: function (label, path, position) {
		this.label = label;
		this.path = path;
		this.position = position;
	},

	process: function () {
		if (!this.label && !this.path) {
			return;
		}

		var removed = false;
		var arrMenuLi = $$('.sidebar .block-account ul li');
		if (Object.isArray(arrMenuLi) && arrMenuLi.length > 0 && this.position <= arrMenuLi.length) {
			var freshdeskLink = $$('.sidebar ul a[href*="' + this.path + '"]').first();
			if (Object.isElement(freshdeskLink)) {
				freshdeskLink.up().removeClassName('last');
				arrMenuLi[this.position].insert({before: freshdeskLink.up()});
				removed = true;
			} else {
				arrMenuLi.each(function (el, index) {
					el.childElements().each(function (chEl, index) {
						if (chEl.innerHTML == this.label) {
							el.removeClassName('last');
							arrMenuLi[this.position].insert({before: el});
							removed = true;
							throw $break;
						}
					}.bind(this));

					if (removed) {
						throw $break;
					}
				}.bind(this));
			}
		}

		if(removed) {
			$$('.sidebar .block-account ul li').last().addClassName('last');
		}
	}
};

Freshdesk.Account.Ticket = {};
Freshdesk.Account.Ticket.Recent = Class.create();
Freshdesk.Account.Ticket.Recent.prototype = {
	pairs: [],

	initialize: function () {
	},

	add: function (parentClass, childClass, altParentClass) {
		if ((!Object.isString(parentClass) && !Object.isString(altParentClass)) || !Object.isString(childClass)) {
			return this;
		}

		this.pairs.push({'parent': parentClass, 'child': childClass, 'altParent': altParentClass, 'skip': false});

		return this;
	},

	process: function () {
		if (this.pairs.length < 1) {
			return;
		}

		var parent, child, altParent;
		for (var i = 0; i < this.pairs.length; i++) {
			if (this.pairs[i].skip) {
				continue;
			}

			parent = $$(this.pairs[i].parent).first();
			child = $$(this.pairs[i].child).first();
			altParent = $$(this.pairs[i].altParent).first();
			if ((Object.isElement(parent) || Object.isElement(altParent)) && Object.isElement(child)) {
				if (Object.isElement(parent)) {
					parent.insert({after: child});
				} else {
					altParent.insert({after: child});
				}

				this.pairs[i].skip = true;
			}
		}
	}
};

Freshdesk.Account.Ticket.View = {};
Freshdesk.Account.Ticket.View.Reply = Class.create();
Freshdesk.Account.Ticket.View.Reply.prototype = {
	id: undefined,
	replyButtonId: undefined,
	sendButtonId: undefined,
	sendButtonDisabled: false,
	text: '',
	className: '',
	sendUrl: undefined,
	tag: undefined,


	initialize: function (id, replyId, sendId, text, className, url, messageTag) {
		this.id = id;
		this.replyButtonId = replyId;
		this.sendButtonId = sendId;
		this.sendUrl = url;
		this.text = text;
		this.className = className;
		this.tag = messageTag;

		$(this.replyButtonId).observe('click', this.reply.bind(this));
		$(this.sendButtonId).observe('click', this.send.bind(this));
		$(this.id).observe('focus', this.focus.bind(this));
		$(this.id).observe('blur', this.blur.bind(this));
	},

	reply: function () {
		$(this.id).scrollTo();
		$(this.id).focus();
	},

	send: function () {
		if (this.sendButtonDisabled) {
			return;
		}

		var message = $(this.id).value;
		if ('' == message || message == this.text) {
			this.reply();
			return;
		}

		this.sendButtonDisabled = true;
		$(this.id).disabled = true;
		$(this.sendButtonId).addClassName('disabled');
		location.href = this.sendUrl.replace(this.tag, message);
	},

	focus: function () {
		if ($(this.id).value == this.text) {
			$(this.id).value = '';
		}

		$(this.id).removeClassName(this.className);
	},

	blur: function () {
		if ($(this.id).value == '') {
			$(this.id).value = this.text;
		}

		$(this.id).addClassName(this.className);
	}
};

var fdAccountTicketRecent = new Freshdesk.Account.Ticket.Recent();
Event.observe(window, 'load', function () {
	fdAccountTicketRecent.process();
});
