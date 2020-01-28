<?php 
require_once '../include/init.php'; 
require_once C_INC.'paypal/PaypalIPN.php';
require_once C_INC.'structure_class.php'; 
require_once C_INC.'structure_fonc.php'; 
require_once C_INC.'paypal_ipn_fonc.php'; 

use PaypalIPN;

$ipn = new PaypalIPN();

// Use the sandbox endpoint during testing.
// $ipn->useSandbox();

try
{
    $verified = $ipn->verifyIPN();
}
catch(Exception $e)
{
    //file_put_contents('error.txt', $e->getMessage(), FILE_APPEND ); 
}

if ($verified) 
{
    /*
     * Process IPN
     * A list of variables is available here:
     * https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNandPDTVariables/
     */

	//file_put_contents('ipn-infos.txt', date('d/m/Y H:i:s')."\n\n".var_dump_str($_POST), FILE_APPEND); 

	$do = []; 
	$do['ps'] = $_POST['payment_status']; // Vérifier que c'est terminé : Completed 
	$do['f'] = $_POST['item_number'];  // Numéro de la facture
	$do['somme'] = $_POST['mc_gross']; // Somme encaissé
	$do['txn_id'] = $_POST['txn_id']; // Vérifier les doublons
	$do['id_paypal'] = $_POST['custom']; // Retrouver la str

	structure_payment($do); 
}

// Reply with an empty 200 response to indicate to paypal the IPN was received correctly.
header("HTTP/1.1 200 OK");
