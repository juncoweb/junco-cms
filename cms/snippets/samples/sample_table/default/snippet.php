<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class sample_table_samples_default_snippet
{
    // vars
    protected string $html = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        $assets = app('assets');
        $assets->css('cms/scripts/samples/css/table-example.css');
        $assets->js('cms/scripts/samples/js/table-example.js');
        $assets->domready('JsFelem.load("#sample-table");');
    }

    /**
     * Row
     */
    public function row($i, $code, $details)
    {
        $this->html .= '
		<!-- Example ' . $i . ' -->
		<table class="table-example">
			<tr>
				<td>
					<h2>Example ' . $i . '</h2>
					<form>
						<input type="submit" class="btn">
						<input type="reset" class="btn">
						<textarea name="code" class="input-field" control-felem="auto-grow">' . $code . '</textarea>
					</form>
				</td>
				<td>' . $details . '</td>
			</tr>
		</table>';
    }

    /**
     * Render
     */
    public function render()
    {
        return '<div id="sample-table" class="panel">' . $this->html . '</div>';
    }
}
