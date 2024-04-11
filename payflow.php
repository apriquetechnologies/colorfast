<?php
 	require('PayflowClass.php');
	$payflow = new Payflow;
	$payflow->setEnv('sandbox');
	$payflow->setPartner('Partner Name');
	$payflow->setVendor('Merchant Login');
	$payflow->setCurrency('USD');
	$payflow->setUser('User Name');
	$payflow->setPassword('Password');
	$payflow->data['ACCT'] = '4111111111111111';
	$payflow->data['AMT'] = '10';
    	$payflow->data['CVV2'] = '123';
	$payflow->data['EXPDATE'] = '0220';
	$payflow->data['FIRSTNAME'] = 'Yogesh';
	$payflow->data['LASTNAME'] = 'Sanger';
	$payflow->data['STREET'] = 'Street 123';
	$payflow->data['CITY'] = 'Melbourne';
	$payflow->data['STATE'] = 'VIC';
	$payflow->data['ZIP'] = '3000';
	$payflow->data['COUNTRY'] = 'AUS';
	$result = $payflow->pay();
	if ($result['success']) {
		$token = $result['data']['PNREF'];
	}

?>