(function() {
    if (!("noValidate" in document.createElement("form")) || !document.createElement("a").classList) {
        return;
    }
    var a = Element.prototype;
    if (!a.matches) {
        a.matches = a.matchesSelector || a.mozMatchesSelector || a.webkitMatchesSelector || a.msMatchesSelector;
    }
    Array.prototype.forEach.call(document.querySelectorAll(".js-form"), function(a) {
        a.noValidate = true;
        a.addEventListener("submit", function(b) {
            if (!a.checkValidity()) {
                b.preventDefault();
                a.querySelector("input:invalid, select:invalid, textarea:invalid").focus();
            }
        });
        a.addEventListener("blur", function(a) {
            if (a.target.matches(":invalid")) {
                b(a.target);
            } else {
                c(a.target);
            }
        }, true);
        a.addEventListener("invalid", function(a) {
            b(a.target);
        }, true);
    });
    function b(a) {
        var b;
        var c = a.parentNode;
        if (!c.classList.contains("form__element--has-error")) {
            b = document.createElement("div");
            b.className = "form__element__help";
            b.innerHTML = a.validationMessage;
            c.classList.add("form__element--has-error");
            c.appendChild(b);
        } else {
            c.querySelector(".form__element__help").innerHTML = a.validationMessage;
        }
    }
    function c(a) {
        var b = a.parentNode;
        if (b.classList.contains("form__element--has-error")) {
            b.classList.remove("form__element--has-error");
            b.removeChild(b.querySelector(".form__element__help"));
        }
    }
})();