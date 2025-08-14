// ----------------------------------------------------------------------------
// markItUp! Universal MarkUp Engine, Vanilla JS with REDAXO compatibility
// v 2.0.0 - Modernized vanilla JavaScript core with jQuery compatibility layer
// Dual licensed under the MIT and GPL licenses.
// ----------------------------------------------------------------------------
// Copyright (C) 2007-2012 Jay Salvat
// Modernized 2024 by Friends Of REDAXO
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
// 
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
// 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.
// ----------------------------------------------------------------------------

(function(global) {
	'use strict';

	// Main MarkItUp! class - vanilla JavaScript implementation
	class MarkItUp {
		constructor(element, settings = {}, extraSettings = {}) {
			this.element = element;
			this.textarea = element;
			this.ctrlKey = false;
			this.shiftKey = false;
			this.altKey = false;
			this.levels = [];
			this.scrollPosition = 0;
			this.caretPosition = 0;
			this.caretOffset = -1;
			this.clicked = null;
			this.hash = null;
			this.header = null;
			this.footer = null;
			this.previewWindow = null;
			this.template = null;
			this.iFrame = null;
			this.abort = false;
			
			// Default options
			this.options = {
				id: '',
				nameSpace: '',
				root: '',
				previewHandler: false,
				previewInWindow: '',
				previewInElement: '',
				previewAutoRefresh: true,
				previewPosition: 'after',
				previewTemplatePath: '~/templates/preview.html',
				previewParser: false,
				previewParserPath: '',
				previewParserVar: 'data',
				resizeHandle: true,
				beforeInsert: '',
				afterInsert: '',
				onEnter: {},
				onShiftEnter: {},
				onCtrlEnter: {},
				onTab: {},
				markupSet: [{ /* set */ }]
			};
			
			// Merge settings
			this.options = Object.assign({}, this.options, settings, extraSettings);
			
			// Initialize
			this.init();
		}
		
		// Static utility methods for DOM manipulation
		static createElement(tag, attributes = {}, content = '') {
			const element = document.createElement(tag);
			Object.keys(attributes).forEach(key => {
				if (key === 'className') {
					element.className = attributes[key];
				} else {
					element.setAttribute(key, attributes[key]);
				}
			});
			if (content) element.innerHTML = content;
			return element;
		}
		
		static wrap(element, wrapperHtml) {
			const wrapper = document.createElement('div');
			wrapper.innerHTML = wrapperHtml;
			const wrapperElement = wrapper.firstChild;
			element.parentNode.insertBefore(wrapperElement, element);
			wrapperElement.appendChild(element);
			return wrapperElement;
		}
		
		static addClass(element, className) {
			if (element.classList) {
				element.classList.add(className);
			} else {
				element.className += ' ' + className;
			}
		}
		
		static on(element, event, handler) {
			const eventType = event.split('.')[0];
			element.addEventListener(eventType, handler, false);
			// Store handler for potential cleanup
			element._markItUpHandlers = element._markItUpHandlers || {};
			element._markItUpHandlers[event] = handler;
		}
		
		static off(element, event) {
			const eventType = event.split('.')[0];
			if (element._markItUpHandlers && element._markItUpHandlers[event]) {
				element.removeEventListener(eventType, element._markItUpHandlers[event], false);
				delete element._markItUpHandlers[event];
			}
		}
		
		// Compute markItUp! path
		computeRoot() {
			if (!this.options.root) {
				const scripts = document.getElementsByTagName('script');
				for (let script of scripts) {
					const match = script.src.match(/(.*)markitup(\.pack)?\.js$/);
					if (match !== null) {
						this.options.root = match[1];
						break;
					}
				}
			}
		}
		
		// Localize paths starting with ~/
		localize(data, inText = false) {
			if (inText) {
				return data.replace(/("|')~\//g, "$1" + this.options.root);
			}
			return data.replace(/^~\//, this.options.root);
		}
		
		// Initialize and build editor
		init() {
			this.computeRoot();
			this.options.previewParserPath = this.localize(this.options.previewParserPath);
			this.options.previewTemplatePath = this.localize(this.options.previewTemplatePath);
			
			let id = '';
			let nameSpace = '';
			
			if (this.options.id) {
				id = 'id="' + this.options.id + '"';
			} else if (this.element.id) {
				id = 'id="markItUp' + this.element.id.charAt(0).toUpperCase() + this.element.id.slice(1) + '"';
			}
			
			if (this.options.nameSpace) {
				nameSpace = 'class="' + this.options.nameSpace + '"';
			}
			
			// Modern DOM wrapping
			MarkItUp.wrap(this.element, '<div ' + nameSpace + '></div>');
			MarkItUp.wrap(this.element, '<div ' + id + ' class="markItUp"></div>');
			MarkItUp.wrap(this.element, '<div class="markItUpContainer"></div>');
			MarkItUp.addClass(this.element, 'markItUpEditor');
			
			// Add header before textarea
			this.header = MarkItUp.createElement('div', { className: 'markItUpHeader' });
			this.element.parentNode.insertBefore(this.header, this.element);
			this.header.appendChild(this.dropMenus(this.options.markupSet));
			
			// Add footer after textarea
			this.footer = MarkItUp.createElement('div', { className: 'markItUpFooter' });
			this.element.parentNode.insertBefore(this.footer, this.element.nextSibling);
			
			// Add resize handle
			if (this.options.resizeHandle === true) {
				const resizeHandle = MarkItUp.createElement('div', { className: 'markItUpResizeHandle' });
				this.element.parentNode.insertBefore(resizeHandle, this.footer);
				this.setupResizeHandle(resizeHandle);
			}
			
			// Setup event handlers
			this.setupEventHandlers();
		}
		
		// Setup resize handle functionality
		setupResizeHandle(resizeHandle) {
			MarkItUp.on(resizeHandle, 'mousedown', (e) => {
				const h = parseInt(getComputedStyle(this.element).height);
				const y = e.clientY;
				
				const mouseMove = (e) => {
					this.element.style.height = Math.max(20, e.clientY + h - y) + 'px';
					return false;
				};
				
				const mouseUp = () => {
					document.removeEventListener('mousemove', mouseMove);
					document.removeEventListener('mouseup', mouseUp);
					return false;
				};
				
				document.addEventListener('mousemove', mouseMove);
				document.addEventListener('mouseup', mouseUp);
				return false;
			});
		}
		
		// Setup event handlers for textarea
		setupEventHandlers() {
			MarkItUp.on(this.element, 'keydown', this.keydown.bind(this));
			MarkItUp.on(this.element, 'keyup', this.keyup.bind(this));
			MarkItUp.on(this.element, 'insertion', this.insertion.bind(this));
			MarkItUp.on(this.element, 'focus', () => {
				this.element.focus();
			});
		}
		
		// Create drop-down menus for button sets
		dropMenus(markupSet) {
			const ul = MarkItUp.createElement('ul');
			
			markupSet.forEach((button, index) => {
				if (this.hideButton(button, index)) return;
				
				const li = MarkItUp.createElement('li');
				const a = MarkItUp.createElement('a', {
					href: 'javascript:void(0)',
					className: button.className || '',
					title: button.name || ''
				});
				
				if (button.key) {
					a.setAttribute('accesskey', button.key);
				}
				
				// Button icon/text
				if (button.name) {
					a.innerHTML = button.name;
				}
				
				// Setup button click handler
				this.setupButtonHandler(a, button);
				
				li.appendChild(a);
				
				// Handle dropdown menus
				if (button.dropMenu) {
					this.levels.push(index);
					li.className = 'markItUpDropMenu';
					li.appendChild(this.dropMenus(button.dropMenu));
				}
				
				ul.appendChild(li);
			});
			
			this.levels.pop();
			return ul;
		}
		
		// Setup individual button event handler
		setupButtonHandler(element, button) {
			// Prevent context menu
			MarkItUp.on(element, 'contextmenu', () => false);
			
			// Prevent default click
			MarkItUp.on(element, 'click', (e) => e.preventDefault());
			
			// Focus handler
			MarkItUp.on(element, 'focusin', () => {
				this.element.focus();
			});
			
			// Main click handler
			MarkItUp.on(element, 'mouseup', () => {
				if (button.call) {
					try {
						(new Function(button.call))();
					} catch(e) {
						console.warn('MarkItUp: Error executing button callback:', e);
					}
				}
				setTimeout(() => this.markup(button), 1);
				return false;
			});
			
			// Dropdown menu handlers
			if (button.dropMenu) {
				MarkItUp.on(element, 'mouseenter', () => {
					const ul = element.parentNode.querySelector('ul');
					if (ul) ul.style.display = 'block';
					
					const closeDropdown = () => {
						document.querySelectorAll('.markItUpHeader ul ul').forEach(menu => {
							menu.style.display = 'none';
						});
					};
					
					document.addEventListener('click', closeDropdown, { once: true });
				});
				
				MarkItUp.on(element, 'mouseleave', () => {
					const ul = element.parentNode.querySelector('ul');
					if (ul) ul.style.display = 'none';
				});
			}
		}
		
		// Check if button should be hidden
		hideButton(button, index) {
			if (button.separator) {
				if (index === 0 || index === this.options.markupSet.length - 1) {
					return true;
				}
				// Check for consecutive separators
				let prevButton = this.options.markupSet[index - 1];
				if (prevButton && prevButton.separator) {
					return true;
				}
			}
			return false;
		}
		
		// Get current selection and caret position
		get() {
			this.scrollPosition = this.element.scrollTop;
			if (this.element.setSelectionRange) {
				this.caretPosition = this.element.selectionStart;
				this.selection = this.element.value.substring(this.element.selectionStart, this.element.selectionEnd);
			} else {
				this.caretPosition = 0;
				this.selection = '';
			}
			return this.selection;
		}
		
		// Set selection and caret position
		set(start, len) {
			if (this.element.setSelectionRange) {
				this.element.focus();
				this.element.setSelectionRange(start, start + len);
			}
			this.element.scrollTop = this.scrollPosition;
		}
		
		// Insert text at current position
		insert(text) {
			if (this.element.setRangeText) {
				// Modern approach
				this.element.setRangeText(text, this.caretPosition, this.caretPosition + this.selection.length, 'end');
			} else {
				// Fallback
				const value = this.element.value;
				this.element.value = value.slice(0, this.caretPosition) + text + value.slice(this.caretPosition + this.selection.length);
			}
		}
		
		// Process magic markups like (!(text)!) and [[![prompt]!]]
		magicMarkups(string) {
			if (!string) return '';
			
			string = string.toString();
			
			// Handle (!(text|alttext)!) patterns
			string = string.replace(/\(\!\(([\s\S]*?)\)\!\)/g, (match, content) => {
				const parts = content.split('|!|');
				if (this.altKey === true) {
					return (parts[1] !== undefined) ? parts[1] : parts[0];
				} else {
					return (parts[1] === undefined) ? "" : parts[0];
				}
			});
			
			// Handle [[![prompt]!]] and [[![prompt:!:value]!]] patterns
			string = string.replace(/\[\!\[([\s\S]*?)\]\!\]/g, (match, content) => {
				const parts = content.split(':!:');
				if (this.abort === true) {
					return false;
				}
				const value = prompt(parts[0], parts[1] || '');
				if (value === null) {
					this.abort = true;
				}
				return value || '';
			});
			
			return string;
		}
		
		// Prepare action (handle functions and magic markups)
		prepare(action) {
			if (typeof action === 'function') {
				action = action(this.hash);
			}
			return this.magicMarkups(action);
		}
		
		// Build markup block
		build(string) {
			const openWith = this.prepare(this.clicked.openWith || '');
			const placeHolder = this.prepare(this.clicked.placeHolder || '');
			const replaceWith = this.prepare(this.clicked.replaceWith || '');
			const closeWith = this.prepare(this.clicked.closeWith || '');
			const openBlockWith = this.prepare(this.clicked.openBlockWith || '');
			const closeBlockWith = this.prepare(this.clicked.closeBlockWith || '');
			const multiline = this.clicked.multiline;
			
			let block;
			
			if (replaceWith !== '') {
				block = openWith + replaceWith + closeWith;
			} else if (this.selection === '' && placeHolder !== '') {
				block = openWith + placeHolder + closeWith;
			} else {
				string = string || this.selection;
				let lines = [string];
				let blocks = [];
				
				if (multiline === true) {
					lines = string.split(/\r?\n/);
				}
				
				lines.forEach(line => {
					const trailingSpaces = line.match(/ *$/);
					if (trailingSpaces) {
						blocks.push(openWith + line.replace(/ *$/g, '') + closeWith + trailingSpaces[0]);
					} else {
						blocks.push(openWith + line + closeWith);
					}
				});
				
				block = blocks.join('\n');
			}
			
			block = openBlockWith + block + closeBlockWith;
			
			return {
				block: block,
				openBlockWith: openBlockWith,
				openWith: openWith,
				replaceWith: replaceWith,
				placeHolder: placeHolder,
				closeWith: closeWith,
				closeBlockWith: closeBlockWith
			};
		}
		
		// Main markup insertion function
		markup(button) {
			this.hash = this.clicked = button;
			this.get();
			
			// Extend hash with current context
			Object.assign(this.hash, {
				line: '',
				root: this.options.root,
				textarea: this.textarea,
				selection: this.selection || '',
				caretPosition: this.caretPosition,
				ctrlKey: this.ctrlKey,
				shiftKey: this.shiftKey,
				altKey: this.altKey
			});
			
			// Callbacks before insertion
			this.prepare(this.options.beforeInsert);
			this.prepare(this.clicked.beforeInsert || '');
			
			if ((this.ctrlKey === true && this.shiftKey === true) || button.multiline === true) {
				this.prepare(this.clicked.beforeMultiInsert || '');
			}
			
			Object.assign(this.hash, { line: 1 });
			
			let string, start, len;
			
			if (this.ctrlKey === true && this.shiftKey === true) {
				// Multi-line processing
				const lines = this.selection.split(/\r?\n/);
				let lineNumber = 0;
				
				const processedLines = lines.map(line => {
					if (line.trim() !== '') {
						Object.assign(this.hash, { line: ++lineNumber, selection: line });
						return this.build(line).block;
					}
					return '';
				});
				
				string = { block: processedLines.join('\n') };
				start = this.caretPosition;
				len = string.block.length;
			} else if (this.ctrlKey === true) {
				string = this.build(this.selection);
				start = this.caretPosition + string.openWith.length;
				len = string.block.length - string.openWith.length - string.closeWith.length;
				len = len - (string.block.match(/ $/) ? 1 : 0);
			} else if (this.shiftKey === true) {
				string = this.build(this.selection);
				start = this.caretPosition;
				len = string.block.length;
			} else {
				string = this.build(this.selection);
				start = this.caretPosition + string.block.length;
				len = 0;
			}
			
			if (this.selection === '' && string.replaceWith === '') {
				start = this.caretPosition + string.openBlockWith.length + string.openWith.length;
				len = string.block.length - string.openBlockWith.length - string.openWith.length - string.closeWith.length - string.closeBlockWith.length;
				
				this.caretOffset = this.element.value.substring(this.caretPosition, this.element.value.length).length;
			}
			
			Object.assign(this.hash, { caretPosition: this.caretPosition, scrollPosition: this.scrollPosition });
			
			if (string.block !== this.selection && this.abort === false) {
				this.insert(string.block);
				this.set(start, len);
			} else {
				this.caretOffset = -1;
			}
			
			this.get();
			
			Object.assign(this.hash, { line: '', selection: this.selection });
			
			// Callbacks after insertion
			if ((this.ctrlKey === true && this.shiftKey === true) || button.multiline === true) {
				this.prepare(this.clicked.afterMultiInsert || '');
			}
			this.prepare(this.clicked.afterInsert || '');
			this.prepare(this.options.afterInsert);
		}
		
		// Keyboard event handlers
		keydown(e) {
			this.shiftKey = e.shiftKey;
			this.ctrlKey = e.ctrlKey || e.metaKey;
			this.altKey = e.altKey;
		}
		
		keyup(e) {
			const keyCode = e.keyCode;
			
			if (keyCode >= 37 && keyCode <= 40) {
				// Arrow keys - handle special key combinations
				if (this.ctrlKey === true || this.shiftKey === true) {
					const button = this.getKeyHandler(keyCode);
					if (button) {
						this.ctrlKey = false;
						setTimeout(() => button.click(), 1);
						return false;
					}
				}
			}
			
			if (keyCode === 13 || keyCode === 10) { // Enter key
				if (this.ctrlKey === true) {
					this.ctrlKey = false;
					this.markup(this.options.onCtrlEnter);
					return this.options.onCtrlEnter.keepDefault;
				} else if (this.shiftKey === true) {
					this.shiftKey = false;
					this.markup(this.options.onShiftEnter);
					return this.options.onShiftEnter.keepDefault;
				} else {
					this.markup(this.options.onEnter);
					return this.options.onEnter.keepDefault;
				}
			}
			
			if (keyCode === 9) { // Tab key
				if (this.shiftKey || this.ctrlKey || this.altKey) {
					return false;
				}
				if (this.caretOffset !== -1) {
					this.get();
					this.caretOffset = this.element.value.length - this.caretOffset;
					this.set(this.caretOffset, 0);
					this.caretOffset = -1;
					return false;
				} else {
					this.markup(this.options.onTab);
					return this.options.onTab.keepDefault;
				}
			}
		}
		
		// Handle insertion event
		insertion(e) {
			this.get();
			const data = e.detail || e.data;
			if (data && data.replaceWith) {
				this.markup(data);
			}
		}
		
		// Get key handler for special key combinations
		getKeyHandler(keyCode) {
			// This would map key codes to specific buttons
			// Implementation depends on specific requirements
			return null;
		}
		
		// Remove markItUp from element
		remove() {
			// Clean up event handlers
			MarkItUp.off(this.element, 'keydown');
			MarkItUp.off(this.element, 'keyup');
			MarkItUp.off(this.element, 'insertion');
			
			// Remove CSS classes
			this.element.classList.remove('markItUpEditor');
			
			// Unwrap the element
			const container = this.element.closest('.markItUpContainer');
			if (container) {
				container.parentNode.replaceChild(this.element, container);
			}
			
			// Clear stored data
			delete this.element._markItUp;
		}
		
		// Static method to call markup on target element
		static callMarkup(settings) {
			const options = Object.assign({ target: false }, settings);
			if (options.target) {
				const targetElement = typeof options.target === 'string' 
					? document.querySelector(options.target) 
					: options.target;
				if (targetElement && targetElement._markItUp) {
					targetElement.focus();
					targetElement.dispatchEvent(new CustomEvent('insertion', { detail: options }));
				}
			} else {
				document.querySelectorAll('textarea').forEach(textarea => {
					if (textarea._markItUp) {
						textarea.dispatchEvent(new CustomEvent('insertion', { detail: options }));
					}
				});
			}
		}
	}
	
	// Global markItUp function
	function markItUp(selector, settings, extraSettings) {
		const elements = typeof selector === 'string' 
			? document.querySelectorAll(selector)
			: [selector];
		
		const instances = [];
		elements.forEach(element => {
			if (element && element.tagName === 'TEXTAREA') {
				// Handle method calls
				if (typeof settings === 'string') {
					if (element._markItUp) {
						switch(settings) {
							case 'remove':
								element._markItUp.remove();
								delete element._markItUp;
								break;
							case 'insert':
								element._markItUp.markup(extraSettings);
								break;
							default:
								console.error('Method ' + settings + ' does not exist on markItUp');
						}
					}
					return;
				}
				
				// Create new instance
				const instance = new MarkItUp(element, settings, extraSettings);
				element._markItUp = instance;
				instances.push(instance);
			}
		});
		
		return instances.length === 1 ? instances[0] : instances;
	}
	
	// Expose to global scope
	global.markItUp = markItUp;
	global.MarkItUp = MarkItUp;
	
	// jQuery compatibility layer
	if (typeof jQuery !== 'undefined') {
		(function($) {
			$.fn.markItUp = function(settings, extraSettings) {
				return this.each(function() {
					markItUp(this, settings, extraSettings);
				});
			};
			
			$.fn.markItUpRemove = function() {
				return this.each(function() {
					markItUp(this, 'remove');
				});
			};
			
			$.markItUp = function(settings) {
				MarkItUp.callMarkup(settings);
			};
		})(jQuery);
	}
	
	// REDAXO compatibility - initialize on rex:ready
	document.addEventListener('DOMContentLoaded', function() {
		// Auto-initialize elements with markitupEditor classes
		const initializeMarkItUp = () => {
			document.querySelectorAll('textarea[class*="markitupEditor-"]').forEach(textarea => {
				if (!textarea._markItUp) {
					// Extract profile from class name
					const classMatch = textarea.className.match(/markitupEditor-(\w+)/);
					if (classMatch) {
						const profile = classMatch[1];
						// Initialize with default settings - profile-specific settings would come from backend
						markItUp(textarea, {});
					}
				}
			});
		};
		
		// Initialize immediately
		initializeMarkItUp();
		
		// Re-initialize when REDAXO triggers rex:ready
		if (typeof jQuery !== 'undefined') {
			$(document).on('rex:ready', initializeMarkItUp);
		}
		
		// Also handle dynamic content
		if (window.MutationObserver) {
			const observer = new MutationObserver(function(mutations) {
				mutations.forEach(function(mutation) {
					if (mutation.type === 'childList') {
						mutation.addedNodes.forEach(function(node) {
							if (node.nodeType === 1) { // Element node
								const textareas = node.querySelectorAll ? 
									node.querySelectorAll('textarea[class*="markitupEditor-"]') : [];
								textareas.forEach(textarea => {
									if (!textarea._markItUp) {
										const classMatch = textarea.className.match(/markitupEditor-(\w+)/);
										if (classMatch) {
											markItUp(textarea, {});
										}
									}
								});
							}
						});
					}
				});
			});
			
			observer.observe(document.body, {
				childList: true,
				subtree: true
			});
		}
	});

})(typeof window !== 'undefined' ? window : this);