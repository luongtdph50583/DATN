// Plugins loader - scripts are now loaded via CDN in app.blade.php
// This file is kept for backward compatibility but no longer uses document.write
(function() {
    // Check if required elements exist, but don't load scripts here
    // Scripts are loaded via CDN in the main layout file
    if (document.querySelectorAll("[toast-list]").length > 0 || 
        document.querySelectorAll("[data-choices]").length > 0 || 
        document.querySelectorAll("[data-provider]").length > 0) {
        // Scripts will be available from CDN
        console.log('Plugin dependencies will be loaded from CDN');
    }
})();
