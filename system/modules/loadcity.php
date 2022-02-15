<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
if (!defined('MOZG'))
    die('Hacking attempt!');

NoAjaxQuery();

$country_id = intval($_POST['country']);

echo '<option value="0">- Выбрать -</option>';

if ($country_id) {
    $sql_ = $db->super_query("SELECT id, name FROM `city` WHERE id_country = '{$country_id}' ORDER by `name` ASC", true);
    foreach ($sql_ as $row2)
        echo '<option value="' . $row2['id'] . '">' . stripslashes($row2['name']) . '</option>';
}
?>
    <script type="text/javascript">$('#load_mini').hide();</script>
<?php
die();
?>