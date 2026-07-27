define([
    'jquery'
], function ($) {

    'use strict';

    return function () {

        const banner = $('#bb-cookie-banner');

        if (!banner.length) {
            return;
        }

        if (localStorage.getItem('bb-cookie-choice')) {

            banner.hide();

            return;

        }

        $('#bb-cookie-accept').on('click', function () {

            localStorage.setItem('bb-cookie-choice','accepted');

            banner.find('.bb-cookie-card')
                  .addClass('bb-cookie-hide');

            setTimeout(function(){

                banner.remove();

            },350);

        });

        $('#bb-cookie-essential').on('click', function () {

            localStorage.setItem('bb-cookie-choice','essential');

            banner.find('.bb-cookie-card')
                  .addClass('bb-cookie-hide');

            setTimeout(function(){

                banner.remove();

            },350);

        });

        $('#bb-cookie-settings').on('click', function(){

            alert('Cookie settings modal coming next.');

        });

    };

});