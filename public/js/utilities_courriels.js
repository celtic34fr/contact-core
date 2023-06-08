/** @format */

function redirect(url) {
    window.location.href = url;
}

function printEmail(url) {
    const wprint = window.open(url);
}

function submit_form(nav) {
    document.querySelector("#nav").value = nav;
    document.querySelector("#select_mail").submit();
}
