<?php

$data = [
    [1, 'Россия'],
    [2, 'Украина'],
    [3, 'Казахстан'],
    [4, 'Беларусь'],
    [5, 'Латвия'],
    [6, 'Молдова'],
    [7, 'Эстония'],
    [8, 'Азербайджан'],
    [9, 'Литва'],
    [10, 'США'],
];
$stmt = $db->prepare("INSERT INTO country (id, name) VALUES (?,?)");
try {
    $db->beginTransaction();
    foreach ($data as $row) {
        $stmt->execute($row);
    }
    $db->commit();
} catch (Exception $e) {
    $db->rollback();
    throw $e;
}