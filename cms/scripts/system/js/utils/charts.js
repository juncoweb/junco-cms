/**
 * Charts
 * @see https://developers.google.com/chart
 */

function GoogleCharts(elements, options) {
    options = Object.assign({
        height: 140,
        backgroundColor: 'transparent',
        chartArea: {
            backgroundColor: 'transparent',
        },
        legend: {
            position: 'top',
            maxLines: 3
        },
        bar: {
            groupWidth: '60%'
        },
        annotations: {
            alwaysOutside: true
        },
        isStacked: true,
        //colors: ['#e0440e', '#e6693e'],
    }, options);

    function getType(el) {
        const type = el.dataset.chart;
        if (type == 'line') {
            return 'Line';
        }

        return 'Bar';
    }

    elements.forEach(function (el) {
        if (el.style.display !== 'none') {
            return; // is initialized
        }
        let array = JSON.parse(el.innerHTML);
        let data = new google.visualization.arrayToDataTable(array);
        let Type = getType(el);

        el.innerHTML = '';
        el.style.display = '';
        new google.charts[Type](el).draw(data, google.charts[Type].convertOptions(options));

    });
}

window.addEventListener('DOMContentLoaded', function () {
    let elements = document.querySelectorAll('[data-chart]');
    if (elements.length) {
        JsImport('https://www.gstatic.com/charts/loader.js')
            .then(function () {
                google.charts.load('current', {
                    packages: ['corechart', 'bar', 'line']
                });
                google.charts.setOnLoadCallback(function () {
                    GoogleCharts(elements);
                });
            });
    }
});
