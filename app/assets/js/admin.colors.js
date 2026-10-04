
/* --- Colors --- */
function getLinearValue(colorValue) {
    const channel = colorValue / 255;
    return channel <= 0.03928
        ? channel / 12.92
        : Math.pow((channel + 0.055) / 1.055, 2.4);
}

function getRelativeLuminance(r, g, b) {
    const rs = getLinearValue(r);
    const gs = getLinearValue(g);
    const bs = getLinearValue(b);
    return 0.2126 * rs + 0.7152 * gs + 0.0722 * bs;
}

function getContrastRatio(rgb1, rgb2) {
    const lum1 = getRelativeLuminance(...rgb1);
    const lum2 = getRelativeLuminance(...rgb2);

    const lighter = Math.max(lum1, lum2);
    const darker = Math.min(lum1, lum2);

    return ((lighter + 0.05) / (darker + 0.05)).toFixed(1);
}

function isHexColor(color) {
    return color.match(/^\#[a-f0-9]{6}$/i);
}

function hex2rgb(color) {
    if (!isHexColor(color)) {
        return;
    }

    return [
        parseInt(color.substring(1, 3), 16),
        parseInt(color.substring(3, 5), 16),
        parseInt(color.substring(5, 7), 16)
    ];
}

function rgb2hex(r, g, b) {
    return "#" + (1 << 24 | r << 16 | g << 8 | b).toString(16).slice(1);
}

function rgbStrToArr(color) {
    return color.replace(/[^\d,]/g, '').split(',');
}

function getBgColor($elem) {
    const color = window.getComputedStyle($elem).backgroundColor;
    return rgbStrToArr(color);
}

function getBorderColor($elem) {
    const color = window.getComputedStyle($elem).borderColor;
    return rgbStrToArr(color);
}

function getFontColor($elem) {
    const color = window.getComputedStyle($elem).color;
    return rgbStrToArr(color);
}

function textColor(color) {
    return getContrastRatio([255, 255, 255], color) > 7
        ? 'white'
        : 'black';
}

function mixColor(color1, color2, weight) {
    const weight2 = 1;
    const div = 1 - (1 - weight) * (1 - weight2);

    return [
        Math.round(color1[0] * weight / div + color2[0] * weight2 * (1 - weight) / div),
        Math.round(color1[1] * weight / div + color2[1] * weight2 * (1 - weight) / div),
        Math.round(color1[2] * weight / div + color2[2] * weight2 * (1 - weight) / div),
    ];
}

function isBright(color) {
    const lightness = (0.2126 * color[0] / 255) + (0.7152 * color[1] / 255) + (0.0722 * color[2] / 255);
    return lightness > 0.5;
}

function scaleColor(color, weight) {
    const white = [255, 255, 255], black = [0, 0, 0];
    if (weight == 50) {
        return mixColor(white, color, 0.9);
    }
    if (weight == 100) {
        return mixColor(white, color, 0.8);
    }
    if (weight == 200) {
        return mixColor(white, color, 0.6);
    }
    if (weight == 300) {
        return mixColor(white, color, 0.4);
    }
    if (weight == 400) {
        return mixColor(white, color, 0.2);
    }
    if (weight == 500) {
        return color;
    }
    if (weight == 600) {
        return mixColor(black, color, 0.2);
    }
    if (weight == 700) {
        return mixColor(black, color, 0.4);
    }
    if (weight == 800) {
        return mixColor(black, color, 0.6);
    }
    if (weight == 900) {
        return mixColor(black, color, 0.8);
    }
    if (weight == 950) {
        return mixColor(black, color, 0.9);
    }

    return "";
}

function getColorData(color, bgw, bw) {
    const bg = scaleColor(color, bgw);

    return {
        font: scaleColor(color, isBright(bg) ? 950 : 50),
        bg: bg,
        border: scaleColor(color, bw)
    };
}

function getBadgeColor(ratio) {
    if (ratio < 3) {
        //return 'badge-danger';
    }
    if (ratio < 4.5) {
        return 'badge-danger';
    }
    if (ratio < 7) {
        return 'badge-warning';
    }
    return 'badge-success';
}

function getBadge(ratio) {
    return '<span class="badge badge-solid ' + getBadgeColor(ratio) + ' rounded-full" >' + ratio + '</span>';
}

function getDataElement(title, color, baseColor = '') {
    const contrast = baseColor
        ? ' ' + getBadge(getContrastRatio(baseColor, color))
        : '';

    return JsElement('div.text-nowrap', { html: '<b>' + title + '</b> ' + rgb2hex(...color) + contrast });
}

function showInfo($elem) {
    const bgColor = getBgColor($elem);
    const borderColor = getBorderColor($elem.firstChild);
    const fontColor = getFontColor($elem);
    const firstChild = $elem.firstChild;

    $elem.insertBefore(getDataElement('bg', bgColor), firstChild);
    $elem.insertBefore(getDataElement('border', borderColor, bgColor), firstChild);
    firstChild.appendChild(getDataElement('font', fontColor, bgColor));
}

window.addEventListener('load', function () {
    // index
    document.querySelectorAll('[data-color]').forEach(($elem) => {
        const color = getBgColor($elem);
        const id = $elem.getAttribute('data-color');

        let html = '<span style="color: ' + textColor(color) + '">' + rgb2hex(...color) + '</span>';

        if (id) {
            const $ref = document.querySelector('#' + id);
            const ratio = getContrastRatio(getBgColor($ref), color);

            html += ' ' + getBadge(ratio);
        }

        $elem.innerHTML = html;
    });

    // contrast
    document.querySelectorAll('[color-contrast]').forEach(($elem) => showInfo($elem));

    // checker
    const $input = document.querySelector('#color');
    if ($input) {
        JsForm();
        const $result = document.querySelector('#color-result');

        $input.addEventListener('change', function () {
            const color = hex2rgb($input.value);

            if (!color) {
                return;
            }
            const $box = $result.insertBefore(JsElement('div.flex'), $result.firstChild);
            const drColor = mixColor([0, 0, 0], color, 0.6);
            const dsColor = mixColor([0, 0, 0], color, 0.4);

            [{ title: 'light / regular', color: getColorData(color, 200, 500) },
            { title: 'solid', color: getColorData(color, 500, 300) },
            { title: 'dark / regular', color: getColorData(drColor, 600, 400) },
            { title: 'solid', color: getColorData(dsColor, 500, 300) },
            ].forEach((row) => {
                $elem = $box.appendChild(
                    JsElement('div', {
                        html: '<h3>' + row.title.toUpperCase() + '</h3>'
                            + '<div style="background-color: ' + rgb2hex(...row.color.bg) + '; color: ' + rgb2hex(...row.color.font) + '" class="p-4">'
                            + '<div style="border: 1px solid ' + rgb2hex(...row.color.border) + ';" class="p-4"></div>'
                            + '</div>'
                    }));

                showInfo($elem.querySelector('div'));
            });
        });

        document.querySelector('#clean')?.addEventListener('click', () => $result.innerHTML = '');
    }
});
