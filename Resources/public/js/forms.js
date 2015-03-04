(function () {
    //if no validation or classlist feature we don't enhance
    if (!('noValidate' in document.createElement('form')) || !document.createElement('a').classList) {
        return;
    }

    var elemProto = Element.prototype;
    if (!elemProto.matches) {
        elemProto.matches = elemProto.matchesSelector || elemProto.mozMatchesSelector || elemProto.webkitMatchesSelector || elemProto.msMatchesSelector;
    }

    Array.prototype.forEach.call(document.querySelectorAll('.js-form'), function (form) {
        form.noValidate = true;

        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                form.querySelector('input:invalid, select:invalid, textarea:invalid').focus();
            }
        });

        form.addEventListener('blur', function (e) {
            if (e.target.matches(':invalid')) {
                setInvalid(e.target);
            } else {
                removeInvalid(e.target);
            }
        }, true);

        form.addEventListener('invalid', function (e) {
            setInvalid(e.target);
        }, true);
    });


    function setInvalid(element) {
        var message;
        var parent = element.parentNode;

        if (!parent.classList.contains('form__element--has-error')) {
            message = document.createElement('div');
            message.className = 'form__element__help';
            message.innerHTML = element.validationMessage;
            parent.classList.add('form__element--has-error');
            parent.appendChild(message);
        } else {
            parent.querySelector('.form__element__help').innerHTML = element.validationMessage;
        }
    }

    function removeInvalid(element) {
        var parent = element.parentNode;
        if (parent.classList.contains('form__element--has-error')) {
            parent.classList.remove('form__element--has-error');
            parent.removeChild(parent.querySelector('.form__element__help'));
        }
    }

})();