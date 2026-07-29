define([
    'jquery'
], function ($) {
    'use strict';

    $(function () {
        var desktopBreakpoint = window.matchMedia('(min-width: 768px)');
        var titleSelector = '.block.filter #narrow-by-list > .filter-options-title';

        console.log('Desktop filter script loaded');

        function closeAllFilters() {
            $(titleSelector)
                .removeClass('active')
                .attr('aria-expanded', 'false')
                .next('.filter-options-content')
                .removeClass('active')
                .hide();
        }

        $(document)
            .off('click.desktopFilter', titleSelector)
            .on('click.desktopFilter', titleSelector, function (event) {
                if (!desktopBreakpoint.matches) {
                    return;
                }

                event.preventDefault();
                event.stopPropagation();

                var $title = $(this);
                var $content = $title.next('.filter-options-content');
                var isOpen = $title.hasClass('active');

                closeAllFilters();

                if (!isOpen) {
                    $title
                        .addClass('active')
                        .attr('aria-expanded', 'true');

                    $content
                        .addClass('active')
                        .show();
                }
            });

        $(document)
            .off('click.desktopFilterOutside')
            .on('click.desktopFilterOutside', function (event) {
                if (
                    desktopBreakpoint.matches &&
                    !$(event.target).closest('.block.filter').length
                ) {
                    closeAllFilters();
                }
            });

        $(document)
            .off('keydown.desktopFilter')
            .on('keydown.desktopFilter', function (event) {
                if (event.key === 'Escape') {
                    closeAllFilters();
                }
            });

        desktopBreakpoint.addEventListener('change', function () {
            if (!desktopBreakpoint.matches) {
                $(titleSelector)
                    .removeClass('active')
                    .removeAttr('aria-expanded')
                    .next('.filter-options-content')
                    .removeClass('active')
                    .removeAttr('style');
            }
        });
    });
});