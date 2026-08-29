'use strict';

class Autocomplete {

	constructor(options = {}) {
		this.selector = options.selector ?? [
			'.ping-composer-textarea',
			'.ping-editor textarea',
			'.pong-editor textarea'
		].join(',');

		this.textareas = [];
		this.searchTimeout = null;
		this.dropdown = null;

		this.activeTextarea = null;
		this.currentMention = null;
		this.currentUsers = [];
		this.selectedIndex = -1;
	}

	init() {
		this.createDropdown();

		this.textareas = Array.from(
			document.querySelectorAll(this.selector)
		);

		this.textareas.forEach(textarea => {
			this.bindTextarea(textarea);
		});

		document.addEventListener('mousedown', event => {
			this.onDocumentMouseDown(event);
		});

		window.addEventListener('resize', () => {
			this.repositionDropdown();
		});

		window.addEventListener('scroll', () => {
			this.repositionDropdown();
		}, true);
	}

	bindTextarea(textarea) {
		if (textarea.dataset.autocompleteBound === '1') {
			return;
		}

		textarea.dataset.autocompleteBound = '1';

		textarea.addEventListener('input', event => {
			this.onInput(event);
		});

		textarea.addEventListener('keydown', event => {
			this.onKeyDown(event);
		});

		textarea.addEventListener('blur', () => {
			clearTimeout(this.searchTimeout);
		});
	}

	async onInput(event) {
		const textarea = event.target;
		const mention = this.findMention(textarea);

		clearTimeout(this.searchTimeout);

		if (!mention) {
			this.resetState();
			this.hideDropdown();
			return;
		}

		this.activeTextarea = textarea;
		this.currentMention = mention;
		this.selectedIndex = -1;

		this.searchTimeout = setTimeout(async () => {
			const queryAtRequestTime = mention.query;
			const users = await this.searchUsers(queryAtRequestTime);

			if (
				this.activeTextarea !== textarea ||
				!this.currentMention ||
				this.currentMention.query !== queryAtRequestTime
			) {
				return;
			}

			if (users.length === 0) {
				this.currentUsers = [];
				this.selectedIndex = -1;
				this.hideDropdown();
				return;
			}

			this.currentUsers = users;
			this.selectedIndex = 0;

			this.showDropdown(textarea, users);
			this.updateSelectedItem();
		}, 200);
	}

	onKeyDown(event) {
		if (!this.isDropdownVisible()) {
			return;
		}

		switch (event.key) {
			case 'ArrowDown':
				event.preventDefault();
				this.moveSelection(1);
				break;

			case 'ArrowUp':
				event.preventDefault();
				this.moveSelection(-1);
				break;

			case 'Enter':
			case 'Tab':
				if (this.selectedIndex < 0) {
					return;
				}

				event.preventDefault();
				this.selectCurrentUser();
				break;

			case 'Escape':
				event.preventDefault();
				this.resetState();
				this.hideDropdown();
				break;
		}
	}

	onDocumentMouseDown(event) {
		if (!this.isDropdownVisible()) {
			return;
		}

		if (this.dropdown.contains(event.target)) {
			return;
		}

		if (
			this.activeTextarea &&
			this.activeTextarea.contains(event.target)
		) {
			return;
		}

		this.resetState();
		this.hideDropdown();
	}

	findMention(textarea) {
		const caret = textarea.selectionStart;
		const before = textarea.value.substring(0, caret);

		const match = before.match(
			/(^|\s)@([a-zA-Z0-9._-]{2,})$/
		);

		if (!match) {
			return null;
		}

		return {
			query: match[2],
			start: caret - match[2].length - 1,
			end: caret
		};
	}

	async searchUsers(query) {
		try {
			const response = await fetch(
				'/api/mentions?q=' + encodeURIComponent(query),
				{
					headers: {
						'Accept': 'application/json'
					}
				}
			);

			if (!response.ok) {
				return [];
			}

			const data = await response.json();

			if (!data.success) {
				return [];
			}

			return Array.isArray(data.users)
				? data.users
				: [];

		} catch (error) {
			console.error('Autocomplete request failed:', error);
			return [];
		}
	}

	createDropdown() {
		if (this.dropdown) {
			return;
		}

		this.dropdown = document.createElement('div');
		this.dropdown.className = 'autocomplete-dropdown';
		this.dropdown.style.display = 'none';
		this.dropdown.setAttribute('role', 'listbox');

		document.body.appendChild(this.dropdown);
	}

	showDropdown(textarea, users) {
		this.renderDropdown(users);
		this.positionDropdown(textarea);

		this.dropdown.style.display = 'block';
	}

	renderDropdown(users) {
		this.dropdown.innerHTML = '';

		users.forEach((user, index) => {
			const item = document.createElement('button');

			item.type = 'button';
			item.className = 'autocomplete-item';
			item.dataset.index = String(index);
			item.setAttribute('role', 'option');
			item.setAttribute('aria-selected', 'false');

			const avatar = document.createElement('img');
			avatar.className = 'autocomplete-avatar';
			avatar.src = user.avatar_url;
			avatar.alt = '';

			const username = document.createElement('span');
			username.className = 'autocomplete-username';
			username.textContent = '@' + user.username;

			item.appendChild(avatar);
			item.appendChild(username);

			item.addEventListener('mousedown', event => {
				event.preventDefault();
			});

			item.addEventListener('mouseenter', () => {
				this.selectedIndex = index;
				this.updateSelectedItem();
			});

			item.addEventListener('click', () => {
				this.selectedIndex = index;
				this.selectCurrentUser();
			});

			this.dropdown.appendChild(item);
		});
	}

	moveSelection(direction) {
		if (this.currentUsers.length === 0) {
			return;
		}

		this.selectedIndex += direction;

		if (this.selectedIndex >= this.currentUsers.length) {
			this.selectedIndex = 0;
		}

		if (this.selectedIndex < 0) {
			this.selectedIndex = this.currentUsers.length - 1;
		}

		this.updateSelectedItem();
	}

	updateSelectedItem() {
		const items = Array.from(
			this.dropdown.querySelectorAll('.autocomplete-item')
		);

		items.forEach((item, index) => {
			const isSelected = index === this.selectedIndex;

			item.classList.toggle(
				'is-selected',
				isSelected
			);

			item.setAttribute(
				'aria-selected',
				isSelected ? 'true' : 'false'
			);
		});

		const selectedItem = items[this.selectedIndex];

		if (selectedItem) {
			selectedItem.scrollIntoView({
				block: 'nearest'
			});
		}
	}

	selectCurrentUser() {
		const user = this.currentUsers[this.selectedIndex];

		if (!user) {
			return;
		}

		this.selectUser(user);
	}

	selectUser(user) {
		const textarea = this.activeTextarea;
		const mention = this.currentMention;

		if (!textarea || !mention) {
			this.hideDropdown();
			return;
		}

		const before = textarea.value.substring(0, mention.start);
		const after = textarea.value.substring(mention.end);
		const replacement = '@' + user.username + ' ';

		textarea.value = before + replacement + after;

		const caretPosition = before.length + replacement.length;

		textarea.focus();
		textarea.setSelectionRange(
			caretPosition,
			caretPosition
		);

		textarea.dispatchEvent(
			new Event('input', {
				bubbles: true
			})
		);

		this.resetState();
		this.hideDropdown();
	}

	positionDropdown(textarea) {
		const caretPosition = this.getCaretPosition(textarea);

		Object.assign(this.dropdown.style, {
			position: 'absolute',
			top: (
				caretPosition.top +
				caretPosition.height +
				6
			) + 'px',
			left: caretPosition.left + 'px',
			color: '#000'
		});
	}

	repositionDropdown() {
		if (
			!this.isDropdownVisible() ||
			!this.activeTextarea
		) {
			return;
		}

		this.positionDropdown(this.activeTextarea);
	}

	getCaretPosition(textarea) {
		const mirror = document.createElement('div');
		const style = window.getComputedStyle(textarea);

		const properties = [
			'boxSizing',
			'width',
			'height',
			'overflowX',
			'overflowY',
			'borderTopWidth',
			'borderRightWidth',
			'borderBottomWidth',
			'borderLeftWidth',
			'paddingTop',
			'paddingRight',
			'paddingBottom',
			'paddingLeft',
			'fontStyle',
			'fontVariant',
			'fontWeight',
			'fontStretch',
			'fontSize',
			'fontFamily',
			'lineHeight',
			'textAlign',
			'textTransform',
			'textIndent',
			'textDecoration',
			'letterSpacing',
			'wordSpacing',
			'tabSize',
			'whiteSpace',
			'wordBreak'
		];

		properties.forEach(property => {
			mirror.style[property] = style[property];
		});

		Object.assign(mirror.style, {
			position: 'absolute',
			visibility: 'hidden',
			whiteSpace: 'pre-wrap',
			overflowWrap: 'break-word',
			top: '0',
			left: '-9999px'
		});

		const before = textarea.value.substring(
			0,
			textarea.selectionStart
		);

		const after = textarea.value.substring(
			textarea.selectionStart
		);

		mirror.textContent = before;

		const marker = document.createElement('span');

		marker.textContent = after.length > 0
			? after[0]
			: '.';

		mirror.appendChild(marker);
		document.body.appendChild(mirror);

		const textareaRect = textarea.getBoundingClientRect();

		const position = {
			top:
				window.scrollY +
				textareaRect.top +
				marker.offsetTop -
				textarea.scrollTop,

			left:
				window.scrollX +
				textareaRect.left +
				marker.offsetLeft -
				textarea.scrollLeft,

			height:
				parseFloat(style.lineHeight) ||
				parseFloat(style.fontSize) * 1.2
		};

		mirror.remove();

		return position;
	}

	isDropdownVisible() {
		return (
			this.dropdown &&
			this.dropdown.style.display !== 'none'
		);
	}

	resetState() {
		this.activeTextarea = null;
		this.currentMention = null;
		this.currentUsers = [];
		this.selectedIndex = -1;
	}

	hideDropdown() {
		if (!this.dropdown) {
			return;
		}

		this.dropdown.style.display = 'none';
		this.dropdown.innerHTML = '';
	}

}

document.addEventListener('DOMContentLoaded', () => {
	new Autocomplete().init();
});
