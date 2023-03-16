
function goPage(url, prefix, noPage) {
    let parameters = prefix + "Page=" + parseInt(noPage);
    location.href = url + "?" + parameters;
}
