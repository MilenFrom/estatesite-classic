# Houzez Enhanced Progress Bar System

## Overview

The enhanced progress bar system provides a reusable, customizable progress bar component with multiple color variants and optional animations. All styles are consolidated in `admin.css`.

## Basic Usage

### HTML Structure

```html
<div class="houzez-progress-bar">
    <div class="houzez-progress-fill default" style="width: 50%"></div>
</div>
<div class="progress-text">
    <span class="progress-percentage">50%</span>
    <span>Processing...</span>
</div>
```

## Color Variants

### Available Colors

-   `default` - Blue gradient (#2271b1 to #72aee6)
-   `success` - Green gradient (#00a32a to #46b450)
-   `warning` - Yellow gradient (#ffb900 to #ffc107)
-   `danger` - Red gradient (#dc3232 to #e65054)
-   `info` - Blue gradient (#0088cc to #72aee6)

### Usage Examples

```html
<!-- Success Progress Bar -->
<div class="houzez-progress-bar">
    <div class="houzez-progress-fill success" style="width: 75%"></div>
</div>

<!-- Warning Progress Bar -->
<div class="houzez-progress-bar">
    <div class="houzez-progress-fill warning" style="width: 30%"></div>
</div>

<!-- Danger Progress Bar -->
<div class="houzez-progress-bar">
    <div class="houzez-progress-fill danger" style="width: 90%"></div>
</div>
```

## Height Variants

### Available Heights

-   **Default** - 24px height (no class needed)
-   `small` - 8px height
-   `medium` - 16px height
-   `large` - 32px height

### Height Usage Examples

```html
<!-- Small Progress Bar -->
<div class="houzez-progress-bar small">
    <div class="houzez-progress-fill success" style="width: 60%"></div>
</div>

<!-- Medium Progress Bar -->
<div class="houzez-progress-bar medium">
    <div class="houzez-progress-fill warning" style="width: 45%"></div>
</div>

<!-- Large Progress Bar -->
<div class="houzez-progress-bar large">
    <div class="houzez-progress-fill danger" style="width: 80%"></div>
</div>

<!-- Default Height (24px) -->
<div class="houzez-progress-bar">
    <div class="houzez-progress-fill default" style="width: 50%"></div>
</div>
```

## Combining Height and Color

You can combine height variants with color variants and animations:

```html
<!-- Small animated success bar -->
<div class="houzez-progress-bar small">
    <div class="houzez-progress-fill success animated" style="width: 75%"></div>
</div>

<!-- Large warning bar with animation -->
<div class="houzez-progress-bar large">
    <div class="houzez-progress-fill warning animated" style="width: 40%"></div>
</div>

<!-- Medium info bar without animation -->
<div class="houzez-progress-bar medium">
    <div class="houzez-progress-fill info" style="width: 65%"></div>
</div>
```

## Animation

### Adding Animation

Add the `animated` class to enable the striped animation effect:

```html
<div class="houzez-progress-bar">
    <div class="houzez-progress-fill default animated" style="width: 60%"></div>
</div>
```

### Animation Control

-   **Without animation**: Just use the color class
-   **With animation**: Add `animated` class for moving stripes

## JavaScript Integration

### Updating Progress

```javascript
function updateProgress(
    percentage,
    status,
    color = 'default',
    animated = false,
    height = ''
) {
    var $progressBar = $('.houzez-progress-bar');
    var $progressFill = $('#progress-fill');
    var $progressText = $('#progress-percentage');
    var $statusText = $('#progress-status');

    // Update width
    $progressFill.css('width', percentage + '%');

    // Update text
    $progressText.text(percentage + '%');
    $statusText.text(status);

    // Update color
    $progressFill
        .removeClass('default success warning danger info')
        .addClass(color);

    // Update height
    $progressBar.removeClass('small medium large');
    if (height) {
        $progressBar.addClass(height);
    }

    // Update animation
    if (animated) {
        $progressFill.addClass('animated');
    } else {
        $progressFill.removeClass('animated');
    }
}

// Usage examples
updateProgress(25, 'Starting...', 'info', true, 'small');
updateProgress(50, 'Processing...', 'default', true, 'medium');
updateProgress(75, 'Almost done...', 'warning', false, 'large');
updateProgress(100, 'Complete!', 'success', false);
```

### Complete Example

```javascript
function updateProgress(
    percentage,
    status,
    color = 'default',
    animated = false,
    height = ''
) {
    var $progressBar = $('.houzez-progress-bar');
    var $progressFill = $('#progress-fill');
    var $progressText = $('#progress-percentage');
    var $statusText = $('#progress-status');

    // Update width
    $progressFill.css('width', percentage + '%');

    // Update text
    $progressText.text(percentage + '%');
    $statusText.text(status);

    // Update color
    $progressFill
        .removeClass('default success warning danger info')
        .addClass(color);

    // Update height
    $progressBar.removeClass('small medium large');
    if (height) {
        $progressBar.addClass(height);
    }

    // Update animation
    if (animated) {
        $progressFill.addClass('animated');
    } else {
        $progressFill.removeClass('animated');
    }
}

// Usage examples
updateProgress(25, 'Starting...', 'info', true, 'small');
updateProgress(50, 'Processing...', 'default', true, 'medium');
updateProgress(75, 'Almost done...', 'warning', false, 'large');
updateProgress(100, 'Complete!', 'success', false);
```

### Height-Specific Examples

```javascript
// Create a small progress bar
function createSmallProgress(containerId, percentage, color = 'default') {
    var html = `
        <div class="houzez-progress-bar small">
            <div class="houzez-progress-fill ${color}" style="width: ${percentage}%"></div>
        </div>
        <div class="progress-text">
            <span class="progress-percentage">${percentage}%</span>
        </div>
    `;
    $('#' + containerId).html(html);
}

// Create a large animated progress bar
function createLargeProgress(
    containerId,
    percentage,
    color = 'default',
    animated = true
) {
    var animatedClass = animated ? 'animated' : '';
    var html = `
        <div class="houzez-progress-bar large">
            <div class="houzez-progress-fill ${color} ${animatedClass}" style="width: ${percentage}%"></div>
        </div>
        <div class="progress-text">
            <span class="progress-percentage">${percentage}%</span>
        </div>
    `;
    $('#' + containerId).html(html);
}

// Usage
createSmallProgress('small-container', 60, 'success');
createLargeProgress('large-container', 80, 'warning', true);
```

## Import Locations Implementation

The import locations feature uses this system with:

-   **Container**: `.houzez-import-progress`
-   **Progress Bar**: `.houzez-progress-bar`
-   **Progress Fill**: `.houzez-progress-fill default animated`
-   **Progress Text**: `.progress-text`

### Spinning Icon

For spinning icons (like loading indicators), add the `spinning` class:

```html
<h3><i class="dashicons dashicons-update spinning"></i> Import in Progress</h3>
```

## CSS Classes Reference

### Container Classes

-   `.houzez-progress-bar` - Main progress bar container

### Height Classes

-   **Default** - 24px height (no additional class needed)
-   `.small` - 8px height
-   `.medium` - 16px height
-   `.large` - 32px height

### Fill Classes

-   `.houzez-progress-fill` - Progress fill element
-   `.default` - Default blue color
-   `.success` - Green color
-   `.warning` - Yellow color
-   `.danger` - Red color
-   `.info` - Blue color
-   `.animated` - Adds moving stripe animation

### Text Classes

-   `.houzez-progress-text` - Progress text container
-   `.houzez-progress-percentage` - Percentage text styling

### Animation Classes

-   `.spinning` - Spinning animation for icons
-   `.animated` - Stripe animation for progress bars

### Complete Class Combinations

```html
<!-- All possible combinations -->
<div class="houzez-progress-bar small">
    <div class="houzez-progress-fill success animated"></div>
</div>

<div class="houzez-progress-bar medium">
    <div class="houzez-progress-fill warning"></div>
</div>

<div class="houzez-progress-bar large">
    <div class="houzez-progress-fill danger animated"></div>
</div>

<div class="houzez-progress-bar">
    <div class="houzez-progress-fill info animated"></div>
</div>
```
