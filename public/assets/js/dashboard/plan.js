var createCheckoutSession = function (priceId, planId) {
    return fetch("/dashboard/create-checkout-session", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            priceId: priceId,
            planId: planId
        })
    }).then(function (result) {
        return result.json();
    });
};

var stripe = Stripe(stripePublicKey);

document
    .getElementById("checkout")
    .addEventListener("click", function (evt) {

        createCheckoutSession(PriceId, PlanId).then(function (data) {
            // Call Stripe.js method to redirect to the new Checkout page
            stripe
                .redirectToCheckout({
                    sessionId: data.sessionId
                })
                .then(handleResult);
        });
    });