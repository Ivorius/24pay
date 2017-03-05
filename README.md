24pay.sk
=============
Propojení s platební bránou 24pay.sk

## Vytvoření požadavku platby

```php

try {
	// nastavení údajů získaných po podpisu smlouvy
	$settings24 = new \TwentyFourPay\ShopSettings("MID","ESHOPID","PASS");
	
	//priprava pro odeslani
	$request = new \TwentyFourPay\Request($settings24);
	$request->setAmount($amount);
	$request->setCurrency("EUR");
	$request->setVariableSymbol($variabilni_symbol);
	$request->setFirstName($firstname);
	$request->setFamilyName($lastname);
	$request->setClientId($clientId);
	$request->setCountry("SVK");
	$request->setEmail($mail);
	
	//zkontroluje zda-li jsou nastavena vsechna potrebna data pro odeslani
	$request->checkData();
	
	// vytvorit formular s odesilacim tlacitkem
	echo $request->getForm('<input type="image" src="/24pay/logo.gif" alt="Platba přes 24 pay" width="90">');
	
	// nebo ziskat data pro odeslani napr. ajaxem
	$string = $request->getFormData("string"); 
	$json = $request->getFormData("json");

} catch (\Exception $e) {
    echo $e->getMessage();
}
```

## Přijetí odpovědi z brány
```php
$data = $_POST["params"];
try {
	$settings24 = new TwentyFourPay\ShopSettings(MID, ESHOPID, PASS);

	$xml = simplexml_load_string($data);
	$transaction = new TwentyFourPay\Transaction\SimpleXmlProcess($xml);

	$response = new TwentyFourPay\Response($settings24, $transaction);
	if ($response->isValid()) {
		//odpoved je validni (spravne podepsana), zjisti vysledek zpracovani
		 $result = $transaction->getResult();
	} else {
		//zaloguj chybu
	}

} catch (\Exception $e) {
	
}

```