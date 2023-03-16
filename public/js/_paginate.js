
function paginate(url, prefix, direct) {
    let parameters = "";
    let page = null;
    let prefixes = document.querySelector(".paginate.page");
    prefixes.forEach(prefixe => {
        if (prefix !== prefixe.dataset('ref')) {
            parameters = parameters + prefix + "Page=" + prefixe.value + "&";
        } else {
            parameters = parameters + prefix + "Page=" + (parseInt(prefixe.value) + parseInt(direct)) + "&";
        }
    });
    parameters = parameters.substr(0, parameters.length - 1);
    location.href = url + "?" + parameters;
}

function paginateForm(prefix, direct) {
    const page = document.querySelector("#"+prefix+"Page");
    const form = document.querySelector("form");
    const maxPage = parseInt(document.querySelector("#"+prefix+"MaxPage").value);

    if (parseInt(page.value) > 1 && direct === -1) {
        page.value = parseInt(page.value) - 1;
    }
    if (parseInt(page.value) < maxPage && direct === 1) {
        page.value = parseInt(page.value) + 1;
    }
    form.submit();
}
