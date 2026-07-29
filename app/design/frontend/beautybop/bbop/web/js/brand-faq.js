define([], function () {
    'use strict';

    return function () {
        var faqSections = document.querySelectorAll('.brand-faq');

        faqSections.forEach(function (section) {
            var items = section.querySelectorAll('.brand-faq__item');

            items.forEach(function (item) {
                item.addEventListener('toggle', function () {
                    if (!item.open) {
                        return;
                    }

                    items.forEach(function (otherItem) {
                        if (otherItem !== item) {
                            otherItem.removeAttribute('open');
                        }
                    });
                });
            });
        });
    };
});