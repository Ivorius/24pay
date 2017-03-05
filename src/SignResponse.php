<?php
/**
 * Sign response from payment
 * @author Ivo Toman
 */

namespace TwentyFourPay;


class SignResponse extends Sign
{
	// Transaction data
	private $amount;
	private $currencyAplhaCode;
	private $pspTxnId;
	private $msTxnId;
	private $timestamp;
	private $result;


	public function __construct(ShopSettings $shop, $amount, $currency, $psptxnid, $mstxnid, $timestamp, $result)
	{
		parent::__construct($shop);

		$this->amount = $amount;
		$this->currencyAplhaCode = $currency;
		$this->msTxnId = $mstxnid;
		$this->pspTxnId = $psptxnid;
		$this->timestamp = $timestamp;
		$this->result = $result;
	}


	public function getStringForSign()
	{
		return $this->mid . $this->amount . $this->currencyAplhaCode . $this->pspTxnId . $this->msTxnId . $this->timestamp . $this->result;
	}



}