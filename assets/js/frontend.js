(function () {
	'use strict';

	var settings = window.BMUXSettings || null;

	function onReady(callback) {
		if ('loading' === document.readyState) {
			document.addEventListener('DOMContentLoaded', callback);
		} else {
			callback();
		}
	}

	function makeElement(tagName, className, text) {
		var element = document.createElement(tagName);

		if (className) {
			element.className = className;
		}

		if ('undefined' !== typeof text && null !== text) {
			element.textContent = text;
		}

		return element;
	}

	function getAccountArea(nav) {
		return nav.closest('.elementor-widget-theme-post-content') ||
			nav.closest('.entry-content') ||
			nav.closest('.post-content') ||
			nav.closest('main') ||
			nav.parentElement;
	}

	function iconMarkup(kind) {
		var paths = {
			dashboard: '<rect x="3" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="3" width="7" height="7" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect>',
			profile: '<circle cx="12" cy="8" r="4"></circle><path d="M4 21a8 8 0 0 1 16 0"></path>',
			subscriptions: '<path d="M4 5h16v14H4z"></path><path d="M8 9h8M8 13h8M8 17h5"></path>',
			payments: '<rect x="2" y="5" width="20" height="14" rx="2"></rect><path d="M2 10h20M6 15h4"></path>',
			logout: '<path d="M10 17l5-5-5-5M15 12H3"></path><path d="M14 4h5a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-5"></path>'
		};
		var span = makeElement('span', 'bmux-nav-icon');

		span.setAttribute('aria-hidden', 'true');
		span.innerHTML = '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" focusable="false">' + (paths[kind] || paths.profile) + '</svg>';

		return span;
	}

	function navKind(link) {
		var value = ((link.id || '') + ' ' + (link.parentElement.className || '') + ' ' + (link.getAttribute('href') || '')).toLowerCase();

		if (-1 !== value.indexOf('dashboard')) {
			return 'dashboard';
		}
		if (-1 !== value.indexOf('subscription')) {
			return 'subscriptions';
		}
		if (-1 !== value.indexOf('payment')) {
			return 'payments';
		}
		if (-1 !== value.indexOf('logout')) {
			return 'logout';
		}

		return 'profile';
	}

	function addNavIcons(nav) {
		Array.prototype.forEach.call(nav.querySelectorAll('a'), function (link) {
			if (!link.querySelector('.bmux-nav-icon')) {
				link.insertBefore(iconMarkup(navKind(link)), link.firstChild);
			}
		});
	}

	function addDashboardLink(nav) {
		var list;
		var item;
		var link;
		var firstProfileItem;

		if (!settings.addDashboard || !settings.dashboardUrl || nav.querySelector('.bmux-dashboard-item')) {
			return;
		}

		list = nav.querySelector('ul');
		if (!list) {
			return;
		}

		item = makeElement('li', 'mepr-nav-item bmux-dashboard-item');
		link = makeElement('a', '', settings.dashboardLabel || '');
		link.href = settings.dashboardUrl;
		link.insertBefore(iconMarkup('dashboard'), link.firstChild);
		item.appendChild(link);

		firstProfileItem = list.querySelector('.mepr-home') || list.firstElementChild;
		list.insertBefore(item, firstProfileItem);
	}

	function renameHomeLink(nav) {
		var link;

		if (!settings.renameHome || !settings.homeLabel) {
			return;
		}

		link = nav.querySelector('#mepr-account-home') || nav.querySelector('.mepr-home a');
		if (!link) {
			return;
		}

		link.textContent = settings.homeLabel;
	}

	function currentAccountView(nav) {
		var active = nav.querySelector('.mepr-active-nav-tab a, .active a, [aria-current="page"]');
		var value = active ? ((active.id || '') + ' ' + (active.getAttribute('href') || '')) : window.location.search;

		value = value.toLowerCase();
		if (-1 !== value.indexOf('subscription')) {
			return 'subscriptions';
		}
		if (-1 !== value.indexOf('payment')) {
			return 'payments';
		}
		if (-1 !== value.indexOf('password')) {
			return 'password';
		}

		return 'home';
	}

	function createAccountCard(title) {
		var card = makeElement('section', 'bmux-account-card');
		var heading = makeElement('h2', 'bmux-account-card-title', title);
		var body = makeElement('div', 'bmux-account-card-body');

		card.appendChild(heading);
		card.appendChild(body);

		return {
			card: card,
			body: body
		};
	}

	function enhanceProfileForm(main) {
		var form = main.querySelector('#mepr_account_form, form.mepr-account-form, form[name="mepr_account_form"]');
		var rows;
		var profileCard;
		var addressCard;
		var actionRow;
		var submit;
		var passwordLink;
		var addressSelector = '.mepr_mepr-address-one, .mepr_mepr-address-two, .mepr_mepr-address-city, .mepr_mepr-address-country, .mepr_mepr-address-state, .mepr_mepr-address-zip';

		if (!form || form.classList.contains('bmux-profile-enhanced')) {
			return;
		}

		form.classList.add('bmux-profile-form', 'bmux-profile-enhanced');
		rows = Array.prototype.filter.call(form.querySelectorAll('.mp-form-row'), function (row) {
			return !row.parentElement.closest('.mp-form-row');
		});

		if (rows.length) {
			profileCard = createAccountCard(settings.strings.profileInformation);
			addressCard = createAccountCard(settings.strings.addressDetails);

			rows.forEach(function (row) {
				if (row.matches(addressSelector)) {
					addressCard.body.appendChild(row);
				} else {
					profileCard.body.appendChild(row);
				}
			});

			if (profileCard.body.children.length) {
				form.insertBefore(profileCard.card, form.firstChild);
			}
			if (addressCard.body.children.length) {
				form.insertBefore(addressCard.card, profileCard.card.nextSibling);
			}
		}

		submit = form.querySelector('.submit');
		passwordLink = main.querySelector('.mepr-account-change-password, a[href*="action=password"], a[href*="action=change_password"]');
		if (submit || passwordLink) {
			actionRow = makeElement('div', 'bmux-form-actions');
			if (submit) {
				actionRow.appendChild(submit);
			}
			if (passwordLink && !actionRow.contains(passwordLink)) {
				passwordLink.classList.add('bmux-change-password-link');
				actionRow.appendChild(passwordLink);
			}
			form.appendChild(actionRow);
		}
	}

	function enhanceTables(main) {
		var selectors = '#mepr-account-subscriptions-table, #mepr-account-payments-table';

		Array.prototype.forEach.call(main.querySelectorAll(selectors), function (table) {
			var headers;
			var wrapper;

			if (table.closest('.bmux-table-card')) {
				return;
			}

			headers = Array.prototype.map.call(table.querySelectorAll('thead th'), function (header) {
				return header.textContent.trim();
			});

			Array.prototype.forEach.call(table.querySelectorAll('tbody tr'), function (row) {
				Array.prototype.forEach.call(row.children, function (cell, index) {
					if (!cell.getAttribute('data-label') && headers[index]) {
						cell.setAttribute('data-label', headers[index]);
					}
				});
			});

			wrapper = makeElement('div', 'bmux-account-card bmux-table-card');
			table.parentNode.insertBefore(wrapper, table);
			wrapper.appendChild(table);
		});
	}

	function enhanceEmptyStates(main) {
		var selector = '.mepr-no-active-subscriptions, .mp-no-subs, .mepr-no-payments, .mp-no-payments';

		Array.prototype.forEach.call(main.querySelectorAll(selector), function (emptyState) {
			emptyState.classList.add('bmux-empty-state');
		});
	}

	function enhanceLooseForms(main, view) {
		if ('password' !== view) {
			return;
		}

		Array.prototype.forEach.call(main.querySelectorAll('form'), function (form) {
			if (!form.classList.contains('bmux-profile-form')) {
				form.classList.add('bmux-account-card', 'bmux-password-form');
			}
		});
	}

	function buildAccountLayout(nav, area) {
		var parent = nav.parentNode;
		var layout = makeElement('div', 'bmux-account-layout');
		var sidebar = makeElement('aside', 'bmux-account-sidebar');
		var userBlock = makeElement('div', 'bmux-account-user');
		var main = makeElement('div', 'bmux-account-main');
		var accountContent;
		var looseNodes = [];
		var node;

		sidebar.setAttribute('aria-label', settings.strings.memberAccount);
		userBlock.appendChild(makeElement('h2', '', settings.memberName || settings.strings.memberAccount));
		userBlock.appendChild(makeElement('p', '', settings.strings.manageMembership));
		sidebar.appendChild(userBlock);

		accountContent = area.querySelector('#mepr-account-content, .mepr-account-content');
		if (!accountContent || accountContent.contains(nav)) {
			accountContent = null;
		}

		if (!accountContent) {
			node = nav.nextSibling;
			while (node) {
				looseNodes.push(node);
				node = node.nextSibling;
			}
		}

		parent.insertBefore(layout, nav);
		layout.appendChild(sidebar);
		layout.appendChild(main);
		sidebar.appendChild(nav);

		if (accountContent) {
			main.appendChild(accountContent);
		} else {
			looseNodes.forEach(function (looseNode) {
				main.appendChild(looseNode);
			});
		}

		return main;
	}

	function enhanceAccountPage() {
		var nav = document.querySelector('#mepr-account-nav');
		var area;
		var main;
		var view;

		if (!nav || !settings.enableAccount || 'true' === nav.getAttribute('data-bmux-enhanced')) {
			return;
		}

		area = getAccountArea(nav);
		if (!area) {
			return;
		}

		nav.setAttribute('data-bmux-enhanced', 'true');
		area.classList.add('bmux-account-area', 'bmux-account-enhanced');
		renameHomeLink(nav);
		addNavIcons(nav);
		addDashboardLink(nav);
		main = buildAccountLayout(nav, area);
		view = currentAccountView(nav);
		main.setAttribute('data-bmux-view', view);

		if ('home' === view) {
			enhanceProfileForm(main);
		}
		enhanceTables(main);
		enhanceEmptyStates(main);
		enhanceLooseForms(main, view);
	}

	function authArea(wrapper) {
		var area = wrapper.closest('.elementor-widget-theme-post-content, .entry-content, .post-content, main') || wrapper.parentElement;

		if (area) {
			area.classList.add('bmux-auth-area');
		}
	}

	function enhanceLoginPage() {
		var form = document.querySelector('#mepr_loginform');
		var wrapper;
		var intro;
		var remember;

		if (!form || !settings.enableLogin || 'true' === form.getAttribute('data-bmux-enhanced')) {
			return;
		}

		wrapper = form.closest('.mp_wrapper') || form.parentElement;
		if (!wrapper) {
			return;
		}

		form.setAttribute('data-bmux-enhanced', 'true');
		wrapper.classList.add('bmux-auth-card', 'bmux-login-card');
		authArea(wrapper);

		if (!wrapper.querySelector('.bmux-auth-intro')) {
			intro = makeElement('div', 'bmux-auth-intro');
			intro.appendChild(makeElement('h2', '', settings.loginHeading));
			intro.appendChild(makeElement('p', '', settings.loginHelper));
			wrapper.insertBefore(intro, form);
		}

		remember = form.querySelector('#rememberme');
		if (remember && remember.parentElement) {
			remember.parentElement.classList.add('bmux-remember-row');
		}

		Array.prototype.forEach.call(wrapper.querySelectorAll('.mepr-login-actions'), function (actions) {
			actions.classList.add('bmux-auth-actions');
		});
	}

	function enhancePasswordResetPage() {
		var form = document.querySelector('#mepr_forgot_password_form');
		var wrapper;
		var heading;
		var helper;
		var back;
		var link;

		if (!form || !settings.enablePasswordReset || 'true' === form.getAttribute('data-bmux-enhanced')) {
			return;
		}

		wrapper = form.closest('.mp_wrapper') || form.parentElement;
		if (!wrapper) {
			return;
		}

		form.setAttribute('data-bmux-enhanced', 'true');
		wrapper.classList.add('bmux-auth-card', 'bmux-reset-card');
		authArea(wrapper);
		heading = wrapper.querySelector('h3');
		if (heading) {
			heading.classList.add('bmux-reset-heading');
		}

		if (!wrapper.querySelector('.bmux-reset-helper')) {
			helper = makeElement('p', 'bmux-reset-helper', settings.passwordResetHelper);
			if (heading) {
				heading.parentNode.insertBefore(helper, heading.nextSibling);
			} else {
				wrapper.insertBefore(helper, form);
			}
		}

		if (!wrapper.querySelector('.bmux-back-to-login')) {
			back = makeElement('p', 'bmux-back-to-login');
			back.appendChild(document.createTextNode(settings.strings.rememberPassword + ' '));
			link = makeElement('a', '', settings.strings.returnToLogin);
			link.href = settings.loginUrl;
			back.appendChild(link);
			form.parentNode.insertBefore(back, form.nextSibling);
		}
	}

	onReady(function () {
		if (!settings || !settings.enabled) {
			return;
		}

		if (!document.querySelector('#mepr-account-nav, #mepr_loginform, #mepr_forgot_password_form')) {
			return;
		}

		enhanceAccountPage();
		enhanceLoginPage();
		enhancePasswordResetPage();
	});
}());
