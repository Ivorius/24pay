<?php
/**
 * PHP script for use to generating sign for 24-pay payment gate
 *
 * @author 24-pay
 */

namespace TwentyFourPay;


class SignRequest implements ISign
{
	private $mid;
	// Merchant secure data - merchant key
	private $key;
	//inverse vector
	private $iv;

	// Transaction data
	private $amount;
	private $currencyAplhaCode;
	private $msTxnId;
	private $firstName;
	private $familyName;
	private $timestamp;

	// Sign config
	private $_mode = MCRYPT_MODE_CBC;
	private $_cipher;
	private $_paddingType;


	public function __construct(ShopSettings $shop, $amount, $currency, $mstxnid, $firstname, $familyname, $time)
	{
		$this->_cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', $this->_mode, '');
		$this->_paddingType = 'PKCS7';


		$this->key = $shop->getKey();
		$this->mid = $mid = $shop->getMid();
		$this->amount = $amount;
		$this->currencyAplhaCode = $currency;
		$this->msTxnId = $mstxnid;
		$this->firstName = $firstname;
		$this->familyName = $familyname;

		$this->timestamp = $time;
		$this->iv = $mid . strrev($mid);
	}


	public function sign()
	{
		return $this->signTransaction();
	}

	public function getHexKey()
	{
		return pack("H*", $this->key);
	}


	public function getPlainText()
	{
		return $this->mid . $this->amount . $this->currencyAplhaCode . $this->msTxnId . $this->firstName . $this->familyName . $this->timestamp;
	}

	private function getData()
	{
		return sha1($this->getPlainText(), true);
	}

	public function signTransaction()
	{
		if ($this->_paddingType == 'PKCS7') {
			$data = $this->addPadding($this->getData());
		}

		mcrypt_generic_init($this->_cipher, $this->getHexKey(), $this->iv);
		$result = mcrypt_generic($this->_cipher, $data);
		mcrypt_generic_deinit($this->_cipher);

		return strtoupper(substr(bin2hex($result), 0, 32));
	}

	private function addPadding($data)
	{
		$block = mcrypt_get_block_size('des', $this->_mode);
		$pad = $block - (strlen($data) % $block);
		$data .= str_repeat(chr($pad), $pad);
		return $data;
	}





}