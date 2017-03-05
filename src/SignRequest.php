<?php
/**
 * Sign request to payment
 * @author Ivo Toman
 */

namespace TwentyFourPay;


class SignRequest extends Sign
{

	// Transaction data
	private $amount;
	private $currencyAplhaCode;
	private $msTxnId;
	private $firstName;
	private $familyName;
	private $timestamp;


	public function __construct(ShopSettings $shop, $amount, $currency, $mstxnid, $firstname, $familyname, $time)
	{
		parent::__construct($shop);

		$this->amount = $amount;
		$this->currencyAplhaCode = $currency;
		$this->msTxnId = $mstxnid;
		$this->firstName = $firstname;
		$this->familyName = $familyname;
		$this->timestamp = $time;
	}


	public function getStringForSign()
	{
		return $this->mid . $this->amount . $this->currencyAplhaCode . $this->msTxnId . $this->firstName . $this->familyName . $this->timestamp;
	}
}