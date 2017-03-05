<?php

/**
 * Author: Ivo Toman
 */
namespace TwentyFourPay;


class Request
{
	const GATE = "https://admin.24-pay.eu/pay_gate/paygt";

	// Transaction data
	private $amount;
	private $msTxnId;
	private $currencyAplhaCode;
	private $clientId;
	private $firstName;
	private $familyName;
	private $email;
	private $country;

	private $data = [];
	private $required = ["Mid", "EshopId", "MsTxnId", "Amount", "CurrAlphaCode", "ClientId", "FirstName", "FamilyName", "Email", "Country", "Timestamp", "Sign"];
	private $optional = ["LangCode", "RURL", "NURL", "NotifyEmail", "RedirectSign", "PreAuthProvided", "Phone", "Street", "City", "Zip"];

	/**
	 * @var ShopSettings
	 */
	private $shop;


	/**
	 * Request constructor.
	 * @param ShopSettings $shop
	 */
	public function __construct(ShopSettings $shop)
	{
		$this->shop = $shop;
	}


	/**
	 * Sign request
	 */
	private function sign()
	{
		$time = (new \DateTime())->format("Y-m-d H:i:s");

		$this->data = array_merge($this->data, [
			"Mid" => $this->shop->getMid(),
			"EshopId" => $this->shop->getEshopId(),
			"Timestamp" => $time,
			"Sign" => (new SignRequest($this->shop, $this->amount, $this->currencyAplhaCode, $this->msTxnId, $this->firstName, $this->familyName, $time))->sign(),
		]);
	}


	/**
	 * compare required fields and inserted data and return what is missing
	 * @return array
	 */
	private function checkRequired()
	{
		$missing = array_diff_key(array_flip($this->required), $this->data);

		return array_flip($missing);
	}


	/**
	 * @return array
	 */
	final private function getData()
	{
		if(!isset($this->data['Sign']))
			$this->sign();

		return $this->data;
	}


	/**
	 * Check if is set all required data
	 * @throws \InvalidArgumentException
	 */
	public function checkData()
	{
		$this->getData();
		$miss = $this->checkRequired();
		if (count($miss))
			throw new \InvalidArgumentException("Required fields is missing: " . implode(",", $miss));
	}


	/**
	 * @return bool
	 */
	public function isValid()
	{
		try {
			$this->checkData();
		} catch (\Exception $e) {
			return false;
		}

		return true;
	}


	public function setAmount($amount)
	{
		if (!is_numeric($amount))
			throw new \InvalidArgumentException('Amount must be numeric');

		$this->data["Amount"] = $this->amount = number_format($amount, 2, ".", "");
	}

	public function setCurrency($currency)
	{
		if (strlen($currency) != 3)
			throw new \InvalidArgumentException('Currency code must be 3 characters length');

		$this->data["CurrAlphaCode"] = $this->currencyAplhaCode = strtoupper($currency);
	}

	public function setVariableSymbol($symbol)
	{
		$this->setMsTxnId($symbol);
	}

	public function setMsTxnId($mstxnid)
	{
		if (mb_strlen($mstxnid) > 32 || empty($mstxnid))
			throw new \InvalidArgumentException('MsTxnId (variable symbol) must be between 1 - 32 characters length');

		$this->data["MsTxnId"] = $this->msTxnId = $mstxnid;
	}

	public function setClientId($id)
	{
		if (mb_strlen($id) < 3 || mb_strlen($id) > 10)
			throw new \InvalidArgumentException('Client ID must be between 3 - 10 characters length');

		$this->data["ClientId"] = $this->clientId = $id;
	}

	public function setFirstName($name)
	{
		if (mb_strlen($name) < 2 || mb_strlen($name) > 50)
			throw new \InvalidArgumentException('First name must be between 2 - 50 characters length');

		$this->data["FirstName"] = $this->firstName = $name;
	}

	public function setFamilyName($name)
	{
		if (mb_strlen($name) < 2 || mb_strlen($name) > 50)
			throw new \InvalidArgumentException('Family name must be between 2 - 50 characters length');

		$this->data["FamilyName"] = $this->familyName = $name;
	}

	public function setEmail($email)
	{
		if (self::isEmail($email) === false)
			throw new \InvalidArgumentException('Email is not in right format');

		$this->data["Email"] = $this->email = $email;
	}

	public function setCountry($country)
	{
		if (strlen($country) != 3)
			throw new \InvalidArgumentException('Country code must be 3 characters length');

		$this->data["Country"] = $this->country = strtoupper($country);
	}


	/**
	 * Thank you Nette\Utils\Validators
	 * @param $value
	 * @return bool
	 */
	public static function isEmail($value)
	{
		$atom = "[-a-z0-9!#$%&'*+/=?^_`{|}~]"; // RFC 5322 unquoted characters in local-part
		$alpha = "a-z\x80-\xFF"; // superset of IDN
		return (bool)preg_match("(^
			(\"([ !#-[\\]-~]*|\\\\[ -~])+\"|$atom+(\\.$atom+)*)  # quoted or unquoted
			@
			([0-9$alpha]([-0-9$alpha]{0,61}[0-9$alpha])?\\.)+    # domain - RFC 1034
			[$alpha]([-0-9$alpha]{0,17}[$alpha])?                # top domain
		\\z)ix", $value);
	}


	/**
	 * Create form for sending data to pay24 gate
	 * @param null $sendButton
	 * @param bool $hiddenInput
	 * @return string
	 */
	public function getForm($sendButton = null, $hiddenInput = true)
	{
		$form = "<form method='post' action='" . self::GATE . "'>";
		foreach ($this->getData() AS $key => $data) {
			$form .= "<input type='". ($hiddenInput ? "hidden" : "text") . "' name='" . $key . "' value='" . $data . "'>";
		}
		if (is_null($sendButton)) {
			$form .= "<input type='submit'>";
		} else {
			$form .= $sendButton;
		}
		$form .= "</form>";

		return $form;
	}


	/**
	 * @param null|string(string|json) $type
	 * @return mixed
	 */
	public function getFormData($type = null)
	{
		$data = $this->getData();
		if($type == "string")
			return http_build_query($data);
		elseif($type == "json")
			return json_encode($data);
		else
			return $data;
	}
}