<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Support\Registry;

NoAjaxQuery();

$country_id = intFilter('country');

echo '<option value="0">- Выбрать -</option>';

if ($country_id) {
    $db = Registry::get('db');
    $sql_ = $db->super_query("SELECT id, name FROM `city` WHERE id_country = '{$country_id}' ORDER by `name` ASC", true);
    foreach ($sql_ as $row2) {
        echo '<option value="' . $row2['id'] . '">' . stripslashes($row2['name']) . '</option>';
    }
}

echo <<<HTML
<script type="text/javascript">$('#load_mini').hide();</script>
HTML;