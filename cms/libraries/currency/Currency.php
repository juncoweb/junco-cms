<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class Currency
{
    // vars
    protected int    $decimals;
    protected string $decimal_separator;
    protected string $thousands_separator;

    /**
     * Constructor
     */
    public function __construct()
    {
        if (preg_match('/([^0])([^0])(0+)?$/', _t('$.,00'), $match)) {
            $this->thousands_separator    = $match[1];
            $this->decimal_separator    = $match[2];
            $this->decimals                = isset($match[3]) ? (int)strlen($match[3]) : 2;
        }
    }

    /**
     * Format
     * 
     * @param float $number
     * @param bool  $simplify_integers
     * 
     * @return string
     */
    public function format(float $number, bool $simplify_integers = false): string
    {
        if ($simplify_integers && $number == round($number)) {
            return number_format($number, 0, $this->decimal_separator, $this->thousands_separator);
        }

        return number_format($number, $this->decimals, $this->decimal_separator, $this->thousands_separator);
    }

    /**
     * Remove
     * 
     * @param string $number
     * 
     * @return float
     */
    public function removeFormat(string $number): float
    {
        return (float)str_replace([$this->thousands_separator, $this->decimal_separator], ['', '.'], $number);
    }

    /**
     * Get
     */
    public function getDecimals(): int
    {
        return $this->decimals;
    }

    /**
     * To string
     */
    public function __toString()
    {
        return '$'
            . $this->thousands_separator
            . $this->decimal_separator
            . str_repeat('0', $this->decimals);
    }
}
