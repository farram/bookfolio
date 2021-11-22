"use strict";

// Class definition
var KTGeneralPricing = function () {
    // Private variables
    var element;
    var planPeriodMonthButton;
    var planPeriodAnnualButton;


    var changePlanPrices = function (type) {
        var items = [].slice.call(element.querySelectorAll('[data-kt-plan-price-month]'));

        items.map(function (item) {
            var monthPrice = item.getAttribute('data-kt-plan-price-month');
            var annualPrice = item.getAttribute('data-kt-plan-price-annual');

            if (type === 'month') {
                item.innerHTML = monthPrice;
            } else if (type === 'annual') {
                item.innerHTML = annualPrice;
            }
        });

        var itemsPeriod = [].slice.call(element.querySelectorAll('[data-kt-plan-period-month]'));
        itemsPeriod.map(function (itemsPeriod) {
            var monthPeriod = itemsPeriod.getAttribute('data-kt-plan-period-month');
            var annualPeriod = itemsPeriod.getAttribute('data-kt-plan-period-annual');

            if (type === 'month') {
                itemsPeriod.innerHTML = monthPeriod;
                $(".comparison.month").removeClass("d-none").addClass("d-block");
                $(".comparison.annual").removeClass("d-block").addClass("d-none");

            } else if (type === 'annual') {
                itemsPeriod.innerHTML = annualPeriod;
                $(".comparison.month").removeClass("d-block").addClass("d-none");
                $(".comparison.annual").removeClass("d-none").addClass("d-block");
            }
        });

        var itemsArgument = [].slice.call(element.querySelectorAll('[data-kt-plan-month-price-argument]'));
        itemsArgument.map(function (itemsArgument) {
            var monthPriceArgument = itemsArgument.getAttribute('data-kt-plan-month-price-argument');
            var yearPriceArgument = itemsArgument.getAttribute('data-kt-plan-year-price-argument');

            if (type === 'month') {
                itemsArgument.innerHTML = monthPriceArgument;
            } else if (type === 'annual') {
                itemsArgument.innerHTML = yearPriceArgument;
            }
        });

        var itemsURLs = [].slice.call(element.querySelectorAll('[data-kt-plan-link-month]'));
        itemsURLs.map(function (itemsURLs) {
            var monthPeriod = itemsURLs.getAttribute('data-kt-plan-link-month');
            var annualPeriod = itemsURLs.getAttribute('data-kt-plan-link-annual');

            if (type === 'month') {
                itemsURLs.setAttribute("href", monthPeriod);
            } else if (type === 'annual') {
                itemsURLs.setAttribute("href", annualPeriod);
            }
        });
    }


    var handlePlanPeriodSelection = function (e) {

        // Handle period change
        planPeriodMonthButton.addEventListener('click', function (e) {
            e.preventDefault();

            changePlanPrices('month');
        });

        planPeriodAnnualButton.addEventListener('click', function (e) {
            e.preventDefault();

            changePlanPrices('annual');
        });
    }

    // Public methods
    return {
        init: function () {
            element = document.querySelector('#kt_pricing');
            planPeriodMonthButton = element.querySelector('[data-kt-plan="month"]');
            planPeriodAnnualButton = element.querySelector('[data-kt-plan="annual"]');

            // Handlers
            handlePlanPeriodSelection();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTGeneralPricing.init();
});
