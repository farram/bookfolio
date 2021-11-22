"use strict";

// Class definition
var KTModalUpgradePlan = function () {
    // Private variables
    var modal;
    var planPeriodMonthButton;
    var planPeriodAnnualButton;

    var changePlanPrices = function (type) {
        var items = [].slice.call(modal.querySelectorAll('[data-kt-plan-price-month]'));

        items.map(function (item) {
            var monthPrice = item.getAttribute('data-kt-plan-price-month');
            var annualPrice = item.getAttribute('data-kt-plan-price-annual');

            if (type === 'month') {
                item.innerHTML = monthPrice;
            } else if (type === 'annual') {
                item.innerHTML = annualPrice;
            }
        });

        var itemsPeriod = [].slice.call(modal.querySelectorAll('[data-kt-plan-period-month]'));
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

        var itemsArgument = [].slice.call(modal.querySelectorAll('[data-kt-plan-month-price-argument]'));
        itemsArgument.map(function (itemsArgument) {
            var monthPriceArgument = itemsArgument.getAttribute('data-kt-plan-month-price-argument');
            var yearPriceArgument = itemsArgument.getAttribute('data-kt-plan-year-price-argument');

            if (type === 'month') {
                itemsArgument.innerHTML = monthPriceArgument;
            } else if (type === 'annual') {
                itemsArgument.innerHTML = yearPriceArgument;
            }
        });

        var itemsURLs = [].slice.call(modal.querySelectorAll('[data-kt-plan-link-month]'));
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

    var handlePlanPeriodSelection = function () {
        // Handle period change
        planPeriodMonthButton.addEventListener('click', function (e) {
            changePlanPrices('month');
        });

        planPeriodAnnualButton.addEventListener('click', function (e) {
            changePlanPrices('annual');
        });
    }

    var handleTabs = function () {
        KTUtil.on(modal, '[data-bs-toggle="tab"]', 'click', function (e) {
            this.querySelector('[type="radio"]').checked = true;
        });
    }

    // Public methods
    return {
        init: function () {
            // Elements
            modal = document.querySelector('#kt_modal_upgrade_plan');

            if (!modal) {
                return;
            }

            planPeriodMonthButton = modal.querySelector('[data-kt-plan="month"]');
            planPeriodAnnualButton = modal.querySelector('[data-kt-plan="annual"]');

            // Handlers
            handlePlanPeriodSelection();
            handleTabs();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTModalUpgradePlan.init();
});
