
    var IdealIssuersElement = document.querySelectorAll('#payment_method_ideal_options')[0];
    var IdealWarningElement = document.querySelectorAll('#payment_method_ideal_warning')[0];

    function onPaymentMethodChanged(el)
    {
        if (el.value == 'ideal') {
            IdealIssuersElement.style.display = '';
        } else {
            IdealIssuersElement.style.display = 'none';
        }
    }

    function onIdealIssuerChanged(el)
    {
        if (el.options && el.selectedIndex) {

            showWarning = false;
            selOption = el.options[el.selectedIndex];
            if (selOption.hasAttribute('data-show-warning')) {
                showWarning = (selOption.getAttribute('data-show-warning') == '1');
            }

            console.log(IdealWarningElement);
            IdealWarningElement.style.display = (showWarning ? '' : 'none');
        }
    }
