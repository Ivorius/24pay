<?php
/**
 * Author: Ivo Toman
 */

namespace TwentyFourPay\Transaction;


class SimpleXmlProcess implements IXmlProcess
{

	private $xml;

	public function __construct(\SimpleXMLElement $xml)
	{
		$this->xml = $xml;
	}

	public function getSign()
	{
		return (string)$this->xml["sign"];
	}

	public function getAmount()
	{
		return (float)$this->xml->Transaction->Presentation->Amount;
	}

	public function getCurrency()
	{
		return (string)$this->xml->Transaction->Presentation->Currency;
	}

	public function getPspTxnId()
	{
		return (string)$this->xml->Transaction->Identification->PspTxnId;
	}

	public function getMsTxnId()
	{
		return (string)$this->xml->Transaction->Identification->MsTxnId;
	}

	public function getTimestamp()
	{
		return (string)$this->xml->Transaction->Processing->Timestamp;
	}

	public function getResult()
	{
		return (string)$this->xml->Transaction->Processing->Result;
	}

}