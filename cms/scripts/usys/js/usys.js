
// --- usys ------------------------------ */
function UsysLogout() {
    JsDropdown.hide();
    JsRequest.modal({
        url: JsUrl('/usys/logout'),
        modalOptions: {
            onLoad: function () {
                JsForm('logout', { btn: this }).request(JsUrl('/usys/take_logout'), function () {
                    window.location.reload();
                });
            }
        }
    });
}
