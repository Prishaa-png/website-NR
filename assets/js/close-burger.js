$(document).ready(function() {
    // Listen for clicks on any standard link inside the mobile menu
    $('.mobile_menu').on('click', '.slicknav_nav a:not(.slicknav_item)', function() {
        // Find the hamburger toggle button
        var $menuButton = $('.slicknav_btn');
        
        // If the menu is currently open, trigger a click to close it
        if ($menuButton.hasClass('slicknav_open')) {
            $menuButton.click();
        }
    });
});