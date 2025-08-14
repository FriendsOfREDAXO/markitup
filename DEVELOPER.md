# MarkItUp! v2.0 Developer Guide

This comprehensive guide explains how to extend MarkItUp! v2.0, create custom plugins, and integrate with the new vanilla JavaScript API.

## Table of Contents

- [Architecture Overview](#architecture-overview)
- [JavaScript API](#javascript-api)
  - [Vanilla JavaScript API](#vanilla-javascript-api)
  - [jQuery Compatibility Layer](#jquery-compatibility-layer)
  - [REDAXO Integration](#redaxo-integration)
- [Creating Custom Plugins](#creating-custom-plugins)
- [Extending the System](#extending-the-system)
- [Migration Guide](#migration-guide)
- [Examples](#examples)

## Architecture Overview

MarkItUp! v2.0 features a completely rewritten core architecture:

```
┌─────────────────────────────────────┐
│           REDAXO Backend            │
│    (rex:ready, jQuery events)      │
└─────────────────┬───────────────────┘
                  │
┌─────────────────▼───────────────────┐
│     jQuery Compatibility Layer     │
│       (jquery.markitup.js)         │
└─────────────────┬───────────────────┘
                  │
┌─────────────────▼───────────────────┐
│      Vanilla JS Core Engine        │
│         (markitup.js)               │
└─────────────────────────────────────┘
```

### Key Changes in v2.0

1. **No jQuery Dependency**: Core is pure vanilla JavaScript
2. **Modern Browser APIs**: Uses `setRangeText`, `addEventListener`, etc.
3. **REDAXO Compatibility**: Maintains integration with REDAXO's event system
4. **Modular Design**: Easier to extend and customize

## JavaScript API

### Vanilla JavaScript API

The new vanilla JavaScript API provides modern, dependency-free functionality:

#### Basic Usage

```javascript
// Initialize a single textarea
const editor = markItUp(document.getElementById('myTextarea'), {
    markupSet: [
        { name: 'Bold', openWith: '**', closeWith: '**', key: 'B' },
        { name: 'Italic', openWith: '*', closeWith: '*', key: 'I' }
    ]
});

// Initialize multiple textareas
const editors = markItUp('.markitupEditor', options);

// Method calls
markItUp('#myTextarea', 'insert', { replaceWith: 'Hello World' });
markItUp('#myTextarea', 'remove');
```

#### Configuration Options

```javascript
const options = {
    id: 'unique-editor-id',
    nameSpace: 'custom-namespace',
    root: '/path/to/markitup/',
    resizeHandle: true,
    markupSet: [
        {
            name: 'Heading 1',
            key: '1',
            openWith: '# ',
            closeWith: '',
            placeHolder: 'Your title here...'
        },
        {
            name: 'Link',
            key: 'L',
            openWith: '[',
            closeWith: ']([![Url:!:http://]!])',
            placeHolder: 'Your text to link here...'
        }
    ],
    onEnter: { keepDefault: true },
    onShiftEnter: { keepDefault: true },
    onCtrlEnter: { keepDefault: true },
    onTab: { keepDefault: true },
    beforeInsert: function(hash) {
        // Custom logic before insertion
    },
    afterInsert: function(hash) {
        // Custom logic after insertion
    }
};
```

#### Button Definition

```javascript
const button = {
    name: 'Button Name',           // Display name
    key: 'B',                      // Access key (Alt+B)
    className: 'custom-button',    // CSS class
    openWith: '**',                // Text before selection
    closeWith: '**',               // Text after selection
    placeHolder: 'Bold text',      // Placeholder when no selection
    replaceWith: 'Fixed text',     // Replace selection entirely
    multiline: false,              // Apply to each line separately
    beforeInsert: function(hash) { /* custom logic */ },
    afterInsert: function(hash) { /* custom logic */ },
    call: 'customFunction()',      // JavaScript to execute on click
    dropMenu: [                    // Dropdown submenu
        { name: 'Option 1', openWith: '## ' },
        { name: 'Option 2', openWith: '### ' }
    ]
};
```

### jQuery Compatibility Layer

For backward compatibility with existing REDAXO integrations:

```javascript
// Traditional jQuery usage (still works)
$('#myTextarea').markItUp(settings);
$('#myTextarea').markItUpRemove();
$.markItUp({ target: '#myTextarea', replaceWith: 'Text' });

// These calls are automatically converted to vanilla JS equivalents
```

### REDAXO Integration

#### Auto-Initialization

Elements with `markitupEditor-{profile}` classes are automatically initialized:

```html
<textarea class="markitupEditor-markdown_full" name="content"></textarea>
```

#### REDAXO Events

The system listens for REDAXO-specific events:

```javascript
// Triggered on REDAXO's rex:ready event
$(document).on('rex:ready', function() {
    // Auto-initialize new textareas
});

// Manual trigger for dynamic content
$(document).trigger('rex:ready');
```

## Creating Custom Plugins

### Plugin Structure

Create a plugin file following this pattern:

```javascript
// File: assets/plugins/my-custom-plugin.js
(function(global) {
    'use strict';
    
    // Extend MarkItUp class with custom functionality
    if (typeof global.MarkItUp !== 'undefined') {
        global.MarkItUp.prototype.myCustomMethod = function() {
            // Custom implementation
            return this; // Enable method chaining
        };
        
        // Add custom button sets
        global.MarkItUp.ButtonSets = global.MarkItUp.ButtonSets || {};
        global.MarkItUp.ButtonSets.MyCustomSet = [
            {
                name: 'Custom Button',
                className: 'custom-btn',
                key: 'C',
                openWith: '[custom]',
                closeWith: '[/custom]',
                call: 'myCustomFunction'
            }
        ];
    }
    
    // Global helper functions
    global.myCustomFunction = function() {
        console.log('Custom button clicked!');
    };
    
})(typeof window !== 'undefined' ? window : this);
```

### Loading Plugins

#### Method 1: Include in Template

```php
<?php
// In your REDAXO template or module
rex_view::addJsFile(rex_url::addonAssets('markitup', 'plugins/my-custom-plugin.js'));
?>
```

#### Method 2: Dynamic Loading

```javascript
// Load plugin dynamically
const script = document.createElement('script');
script.src = '/assets/addons/markitup/plugins/my-custom-plugin.js';
script.onload = function() {
    console.log('Plugin loaded');
};
document.head.appendChild(script);
```

### Advanced Plugin Example

```javascript
// File: assets/plugins/enhanced-markdown.js
(function(global) {
    'use strict';
    
    const EnhancedMarkdown = {
        // Custom button set for enhanced Markdown
        buttons: [
            {
                name: 'Task List',
                className: 'task-list-btn',
                key: 'T',
                multiline: true,
                openWith: '- [ ] ',
                closeWith: '',
                placeHolder: 'Task item'
            },
            {
                name: 'Code Block',
                className: 'code-block-btn',
                key: 'K',
                openWith: '```[![Language:!:javascript]!]\n',
                closeWith: '\n```',
                placeHolder: 'Your code here...'
            },
            {
                name: 'Table',
                className: 'table-btn',
                call: 'EnhancedMarkdown.insertTable'
            }
        ],
        
        // Custom function to insert a table
        insertTable: function() {
            const rows = prompt('Number of rows:', '3');
            const cols = prompt('Number of columns:', '3');
            
            if (rows && cols) {
                let table = '';
                
                // Header row
                for (let i = 0; i < cols; i++) {
                    table += '| Header ' + (i + 1) + ' ';
                }
                table += '|\n';
                
                // Separator row
                for (let i = 0; i < cols; i++) {
                    table += '| --- ';
                }
                table += '|\n';
                
                // Data rows
                for (let r = 0; r < rows - 1; r++) {
                    for (let c = 0; c < cols; c++) {
                        table += '| Cell ' + (r + 1) + ',' + (c + 1) + ' ';
                    }
                    table += '|\n';
                }
                
                // Insert the table
                global.markItUp.callMarkup({
                    replaceWith: table
                });
            }
        }
    };
    
    // Register with global scope
    global.EnhancedMarkdown = EnhancedMarkdown;
    
    // Add to MarkItUp button sets
    if (global.MarkItUp) {
        global.MarkItUp.ButtonSets = global.MarkItUp.ButtonSets || {};
        global.MarkItUp.ButtonSets.EnhancedMarkdown = EnhancedMarkdown.buttons;
    }
    
})(typeof window !== 'undefined' ? window : this);
```

## Extending the System

### Custom Button Types

Create reusable button types:

```javascript
// Custom button factory
const ButtonFactory = {
    createHeadingButton: function(level, key) {
        return {
            name: 'Heading ' + level,
            key: key,
            openWith: '#'.repeat(level) + ' ',
            closeWith: '',
            className: 'heading-' + level + '-btn'
        };
    },
    
    createLinkButton: function(type, urlPrefix) {
        return {
            name: type + ' Link',
            openWith: '[',
            closeWith: '](' + urlPrefix + '[![Url:!:]!])',
            placeHolder: 'Link text'
        };
    }
};

// Use the factory
const customMarkupSet = [
    ButtonFactory.createHeadingButton(1, '1'),
    ButtonFactory.createHeadingButton(2, '2'),
    ButtonFactory.createLinkButton('Internal', '/'),
    ButtonFactory.createLinkButton('External', 'https://')
];
```

### Custom Event Handlers

Add custom behavior to button interactions:

```javascript
const customOptions = {
    beforeInsert: function(hash) {
        // Log all insertions
        console.log('Inserting:', hash);
        
        // Modify based on current selection
        if (hash.selection.length > 100) {
            confirm('Large selection detected. Continue?');
        }
    },
    
    afterInsert: function(hash) {
        // Auto-save after insertions
        setTimeout(() => {
            document.getElementById('save-btn')?.click();
        }, 1000);
    },
    
    onEnter: {
        keepDefault: false,
        call: 'customEnterHandler'
    }
};

function customEnterHandler() {
    // Custom enter key behavior
    const editor = this;
    const currentLine = editor.getCurrentLine();
    
    if (currentLine.match(/^\* /)) {
        // Continue list
        editor.insertAfterCursor('\n* ');
        return false;
    }
    
    return true; // Use default behavior
}
```

### Integration with REDAXO Modules

Create reusable module components:

```php
<?php
// File: fragments/markitup-editor.php
$profile = $this->getVar('profile', 'markdown_full');
$name = $this->getVar('name', 'REX_INPUT_VALUE[1]');
$value = $this->getVar('value', 'REX_VALUE[1]');
$height = $this->getVar('height', 300);
?>

<div class="form-group">
    <label class="col-sm-2 control-label"><?= $this->getVar('label', 'Content') ?></label>
    <div class="col-sm-10">
        <textarea 
            class="form-control markitupEditor-<?= $profile ?>" 
            name="<?= $name ?>" 
            style="height: <?= $height ?>px"
            data-markitup-config='<?= json_encode($this->getVar('config', [])) ?>'
        ><?= htmlspecialchars($value) ?></textarea>
    </div>
</div>

<script>
// Custom configuration based on data attribute
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.querySelector('[data-markitup-config]');
    if (textarea) {
        const config = JSON.parse(textarea.dataset.markitupConfig);
        markItUp(textarea, config);
    }
});
</script>
```

Usage in module:

```php
<?php
$fragment = new rex_fragment();
$fragment->setVar('profile', 'markdown_full');
$fragment->setVar('name', 'REX_INPUT_VALUE[1]');
$fragment->setVar('value', 'REX_VALUE[1]');
$fragment->setVar('label', 'Article Content');
$fragment->setVar('height', 400);
$fragment->setVar('config', [
    'beforeInsert' => 'function(hash) { console.log("Custom config"); }'
]);
echo $fragment->parse('markitup-editor.php');
?>
```

## Migration Guide

### From jQuery v1.x to v2.0

#### Basic Usage (No Changes Required)

```javascript
// This still works unchanged
$('#textarea').markItUp(settings);
$('#textarea').markItUpRemove();
$.markItUp(settings);
```

#### New Vanilla JS API Usage

```javascript
// Old jQuery way
$('#textarea').markItUp(settings);

// New vanilla JS way
markItUp('#textarea', settings);
// or
markItUp(document.getElementById('textarea'), settings);
```

#### Configuration Migration

```javascript
// Old configuration
var oldConfig = {
    nameSpace: 'custom',
    settings: settings,
    // ... other options
};

// New configuration (same options work)
const newConfig = {
    nameSpace: 'custom',
    markupSet: settings, // settings renamed to markupSet
    // ... other options (mostly unchanged)
};
```

#### Event Handler Migration

```javascript
// Old jQuery event handling
$('#textarea').on('markitup.beforeInsert', function(e, hash) {
    // handle event
});

// New native event handling
const textarea = document.getElementById('textarea');
textarea.addEventListener('markitup:beforeInsert', function(e) {
    const hash = e.detail;
    // handle event
});
```

### Breaking Changes

1. **Browser Support**: IE support removed, modern browsers only
2. **jQuery Dependency**: Core no longer depends on jQuery (compatibility layer available)
3. **Event Names**: Custom events use native CustomEvent with `markitup:` prefix
4. **Some Internal Methods**: Internal implementation details changed (public API remains compatible)

## Examples

### Complete Integration Example

```php
<?php
// In your REDAXO module input
if (!rex_addon::get('markitup')->isAvailable()) {
    echo rex_view::error('MarkItUp addon required');
    return;
}
?>

<script>
// Define custom profile
document.addEventListener('DOMContentLoaded', function() {
    // Custom button set for this specific use case
    const customButtons = [
        {
            name: 'Bold',
            key: 'B',
            openWith: '**',
            closeWith: '**',
            className: 'bold-btn'
        },
        {
            name: 'Insert Date',
            call: 'insertCurrentDate',
            className: 'date-btn'
        },
        {
            name: 'REDAXO Variables',
            dropMenu: [
                {
                    name: 'Article ID',
                    replaceWith: 'REX_ARTICLE_ID'
                },
                {
                    name: 'Current Date',
                    replaceWith: 'REX_DATE[format=Y-m-d]'
                }
            ]
        }
    ];
    
    // Initialize with custom configuration
    markItUp('#content-textarea', {
        markupSet: customButtons,
        beforeInsert: function(hash) {
            // Custom validation
            if (hash.selection && hash.selection.length > 1000) {
                return confirm('Large text selection. Continue?');
            }
        }
    });
});

// Custom function
function insertCurrentDate() {
    const date = new Date().toISOString().split('T')[0];
    markItUp.callMarkup({
        replaceWith: date
    });
}
</script>

<div class="form-group">
    <label class="col-sm-2 control-label">Content</label>
    <div class="col-sm-10">
        <textarea 
            id="content-textarea"
            class="form-control" 
            name="REX_INPUT_VALUE[1]" 
            rows="20"
        >REX_VALUE[1]</textarea>
    </div>
</div>
```

### Dynamic Profile Loading

```javascript
// Load different profiles based on content type
document.addEventListener('DOMContentLoaded', function() {
    const contentType = document.getElementById('content-type').value;
    let profile;
    
    switch(contentType) {
        case 'markdown':
            profile = {
                markupSet: [
                    { name: 'Bold', openWith: '**', closeWith: '**' },
                    { name: 'Italic', openWith: '*', closeWith: '*' },
                    { name: 'Code', openWith: '`', closeWith: '`' }
                ]
            };
            break;
        case 'html':
            profile = {
                markupSet: [
                    { name: 'Bold', openWith: '<strong>', closeWith: '</strong>' },
                    { name: 'Italic', openWith: '<em>', closeWith: '</em>' },
                    { name: 'Paragraph', openWith: '<p>', closeWith: '</p>' }
                ]
            };
            break;
        default:
            profile = { markupSet: [] };
    }
    
    markItUp('#dynamic-textarea', profile);
});
```

This developer guide provides comprehensive information for extending and customizing MarkItUp! v2.0. The new architecture makes it easier to create powerful, maintainable solutions while preserving compatibility with existing REDAXO integrations.