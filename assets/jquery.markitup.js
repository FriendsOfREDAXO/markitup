// ----------------------------------------------------------------------------
// markItUp! Universal MarkUp Engine, Backward Compatibility Layer
// v 2.0.0 - jQuery compatibility layer for vanilla JS core
// Dual licensed under the MIT and GPL licenses.
// ----------------------------------------------------------------------------
// Copyright (C) 2007-2012 Jay Salvat
// Modernized 2024 by Friends Of REDAXO
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------

// jQuery compatibility layer - assumes markitup.js is already loaded
(function($) {
	'use strict';
	
	if (typeof $ === 'undefined') {
		console.warn('MarkItUp jQuery compatibility layer: jQuery not found');
		return;
	}
	
	// Wait for vanilla markItUp to be available
	function waitForMarkItUp(callback) {
		if (typeof markItUp !== 'undefined') {
			callback();
		} else {
			setTimeout(() => waitForMarkItUp(callback), 10);
		}
	}
	
	waitForMarkItUp(function() {
		// jQuery plugin implementation
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
			if (typeof MarkItUp !== 'undefined' && MarkItUp.callMarkup) {
				MarkItUp.callMarkup(settings);
			}
		};
		
		// REDAXO compatibility - auto-initialize on rex:ready
		$(document).on('rex:ready', function() {
			// Auto-initialize elements with markitupEditor classes
			$('textarea[class*="markitupEditor-"]').each(function() {
				if (!this._markItUp) {
					// Extract profile from class name
					const classMatch = this.className.match(/markitupEditor-(\w+)/);
					if (classMatch) {
						const profile = classMatch[1];
						// Initialize with default settings - profile-specific settings come from backend
						$(this).markItUp({});
					}
				}
			});
		});
	});
})(jQuery);