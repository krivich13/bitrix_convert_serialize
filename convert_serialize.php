<?
// Выполнять нужно из PHP консоли в админке битрикс
global $DB;
$q1 = "SHOW TABLES";
$res1 = $DB->Query($q1);
$arrTables = array();
while ($row = $res1->Fetch()):
	$arrTables[] = $row['Tables_in_sitemanager'];
endwhile;
foreach ($arrTables as $table):
	$q2 = "SELECT * FROM `$table`";
	$res2 = $DB->Query($q2);
	$arrRows = array();
	while ($row = $res2->Fetch()):
		foreach($row as $field => $valSourc):
			if (preg_match('/a:[0-9]+:\{/', $valSourc) > 0) {
				$valDest = $APPLICATION->ConvertCharset($valSourc, 'utf-8', 'windows-1251');

				/* Конвертируем обратно */
				$valDest = @unserialize($valDest);
		
				if ($valDest === false) continue;

				/* Преобразуем массив в кодироку UTF-8 */
				$valDest = $APPLICATION->ConvertCharsetArray($valDest, 'windows-1251', 'utf-8');

				/* По новой сериализуем */
				$valDest = serialize($valDest);
				
				$valDest = $DB->ForSql($valDest);
				$valSourc = $DB->ForSql($valSourc);
				$q3 = "UPDATE `$table` SET `$field` = '$valDest' WHERE `$field` = '".$valSourc."'";
				$DB->Query($q3);
			}
		endforeach;
	endwhile;
endforeach;
?>