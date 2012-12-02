<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$shiftId = $_POST['id'];
$groupcode = $_POST['grpcode'];

			$shiftId = "5049860f69e95eb75b8c65b3";

			// remove the shift from user preference documents
			$arg = array('col' => "testy", 'type' => 'user');
			$results = $db->find($arg);
			foreach ($results as $result) {
				$shiftPrefs = $result['preferences']['shifts'];
				foreach ($shiftPrefs as $key => $shift) {
					if ($shift == $shiftId) {
						unset($result['preferences']['shifts'][$key]);
						$tmpShiftOrder = $result['preferences']['shifts'];
						unset($result['preferences']['shifts']);
						$i=0;
						foreach ($tmpShiftOrder as $key => $shift2) {
							$result['preferences']['shifts'][$i] = $shift2;
							$i++;
						}
						
						echo $result['user_name'] . " - Matched shift: " . $shift . "\n";
					}
					
					echo $result['user_name'] . " - Unmatched shift: " . $shift . "\n";
				}
				
				if ($result['preferences']['shifts'] == "") {
					$result['preferences']['shifts'] = array();	
				}
				
				// $result is ready to be upserted
				// $data = $db->upsert(array('id' => $result['_id'], 'col' => $groupcode, 'type' => "user", 'obj' => $result ));
			}

?>