
var done = false;
function Zaznacz() {
    var elements = document.forms['formularz'].elements;
    for (var i = 0;i<elements.length ;i++ ) {
        if (elements[i].type === "checkbox" && elements[i].class !== "all") {
            if (!done) {
                elements[i].checked = "true";
            } else {
                elements[i].checked = "";
            }
        }
    }
    if (!done) {
        done = true;
    } else {
        done = false;
    }
}