/**
 * Loading
 */
function JsLoading(force) {
    let ID = 'js-loading';
    let doc = window.self.document;

    (doc.getElementById(ID) || doc.body.appendChild(JsElement('div', { id: ID, className: ID })))
        .style.display = force ? '' : 'none';
};
