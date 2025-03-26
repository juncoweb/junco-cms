<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

class Color extends FilterAbstract
{
    // vars
    protected array $types = ['hex', 'hex+', 'named', 'named+'];
    protected array $named = [
        'black',
        'silver',
        'gray',
        'white',
        'maroon',
        'red',
        'purple',
        'fuchsia',
        'green',
        'lime',
        'olive',
        'yellow',
        'navy',
        'blue',
        'teal',
        'aqua',
        'orange',
        //
        'aliceblue',
        'antiquewhite',
        'aquamarine',
        'azure',
        'beige',
        'bisque',
        'blanchedalmond',
        'blueviolet',
        'brown',
        'burlywood',
        'cadetblue',
        'chartreuse',
        'chocolate',
        'coral',
        'cornflowerblue',
        'cornsilk',
        'crimson',
        'darkblue',
        'darkcyan',
        'darkgoldenrod',
        'darkgray',
        'darkgreen',
        'darkgrey',
        'darkkhaki',
        'darkmagenta',
        'darkolivegreen',
        'darkorange',
        'darkorchid',
        'darkred',
        'darksalmon',
        'darkseagreen',
        'darkslateblue',
        'darkslategray',
        'darkslategrey',
        'darkturquoise',
        'darkviolet',
        'deeppink',
        'deepskyblue',
        'dimgray',
        'dimgrey',
        'dodgerblue',
        'firebrick',
        'floralwhite',
        'forestgreen',
        'gainsboro',
        'ghostwhite',
        'gold',
        'goldenrod',
        'greenyellow',
        'grey',
        'honeydew',
        'hotpink',
        'indianred',
        'indigo',
        'ivory',
        'khaki',
        'lavender',
        'lavenderblush',
        'lawngreen',
        'lemonchiffon',
        'lightblue',
        'lightcoral',
        'lightcyan',
        'lightgoldenrodyellow',
        'lightgray',
        'lightgreen',
        'lightgrey',
        'lightpink',
        'lightsalmon',
        'lightseagreen',
        'lightskyblue',
        'lightslategray',
        'lightslategrey',
        'lightsteelblue',
        'lightyellow',
        'limegreen',
        'linen',
        'mediumaquamarine',
        'mediumblue',
        'mediumorchid',
        'mediumpurple',
        'mediumseagreen',
        'mediumslateblue',
        'mediumspringgreen',
        'mediumturquoise',
        'mediumvioletred',
        'midnightblue',
        'mintcream',
        'mistyrose',
        'moccasin',
        'navajowhite',
        'oldlace',
        'olivedrab',
        'orangered',
        'orchid',
        'palegoldenrod',
        'palegreen',
        'paleturquoise',
        'palevioletred',
        'papayawhip',
        'peachpuff',
        'peru',
        'pink',
        'plum',
        'powderblue',
        'rosybrown',
        'royalblue',
        'saddlebrown',
        'salmon',
        'sandybrown',
        'seagreen',
        'seashell',
        'sienna',
        'skyblue',
        'slateblue',
        'slategray',
        'slategrey',
        'snow',
        'springgreen',
        'steelblue',
        'tan',
        'thistle',
        'tomato',
        'turquoise',
        'violet',
        'wheat',
        'whitesmoke',
        'yellowgreen',
        //
        'rebeccapurple',
    ];
    protected array $named_plus = [
        'currentcolor',
        'transparent',
    ];

    // const
    const HEX_PATTERN = '/^#[0-9a-f]{6}$/i';
    const HEXPLUS_PATTERN = '/^#(?:[0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})$/i';

    /**
     * Constructor
     * 
     * @param string|array|null $filter_value
     */
    public function __construct(string|array|null $filter_value = null)
    {
        $this->type = 'string';
        $this->default  = '';
        $this->argument = [
            'filter' => FILTER_CALLBACK,
            'options' => [$this, 'validate']
        ];

        // I validate schemes
        if ($filter_value) {
            if (is_string($filter_value)) {
                $filter_value = $this->strToArr($filter_value);
            }
            if (array_diff($filter_value, $this->types)) {
                throw new \Exception('There is an error in the color filter values.');
            }

            $this->types = $filter_value;
        }
    }

    /**
     * Validate
     */
    public function validate(?string $value = null)
    {
        if ($value === null) {
            return null;
        }

        $value = trim(strtolower($value));

        foreach ($this->types as $type) {
            switch ($type) {
                case 'hex':
                    if (preg_match(self::HEX_PATTERN, $value)) {
                        return $value;
                    }
                    break;

                case 'hex+':
                    if (preg_match(self::HEXPLUS_PATTERN, $value)) {
                        return $value;
                    }
                    break;

                case 'named':
                    if (in_array($value, $this->named)) {
                        return $value;
                    }
                    break;

                case 'named+':
                    if (in_array($value, array_merge($this->named, $this->named_plus))) {
                        return $value;
                    }
                    break;
            }
        }

        return false;
    }
}
