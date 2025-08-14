// ----------------------------------------------------------------------------
// markItUp! Universal MarkUp Engine, Backward Compatibility Layer
// v 2.0.0 - jQuery compatibility layer for vanilla JS core
// Dual licensed under the MIT and GPL licenses.
// ----------------------------------------------------------------------------
// Copyright (C) 2007-2012 Jay Salvat
// Modernized 2024 by Friends Of REDAXO
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------

// Load the main markItUp! implementation
document.addEventListener('DOMContentLoaded', function() {
	const script = document.createElement('script');
	script.src = (function() {
		const scripts = document.getElementsByTagName('script');
		for (let script of scripts) {
			const match = script.src.match(/(.*)jquery\.markitup(\.pack)?\.js$/);
			if (match !== null) {
				return match[1] + 'markitup.js';
			}
		}
		return 'markitup.js'; // fallback
	})();
	
	script.onload = function() {
		// Ensure jQuery compatibility is available
		if (typeof jQuery !== 'undefined' && typeof markItUp !== 'undefined') {
			console.log('MarkItUp! v2.0.0 loaded with jQuery compatibility');
		}
	};
	
	document.head.appendChild(script);
});

// Immediate jQuery compatibility for cases where it's needed before DOMContentLoaded
if (typeof jQuery !== 'undefined') {
	(function($) {
		// Placeholder functions that will be replaced once the main script loads
		$.fn.markItUp = function(settings, extraSettings) {
			console.warn('MarkItUp! is still loading. Please use $(document).ready() or rex:ready event.');
			return this;
		};
		
		$.fn.markItUpRemove = function() {
			console.warn('MarkItUp! is still loading. Please use $(document).ready() or rex:ready event.');
			return this;
		};
		
		$.markItUp = function(settings) {
			console.warn('MarkItUp! is still loading. Please use $(document).ready() or rex:ready event.');
		};
	})(jQuery);
}