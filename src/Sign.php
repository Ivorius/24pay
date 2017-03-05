<?php
/**
 * PHP script for use to generating sign for 24-pay payment gate
 *
 * @author 24-pay
 */

namespace TwentyFourPay;


abstract class Sign implements ISign
{
	protected $mid;
	// Merchant secure data - merchant key
	private $key;
	//inverse vector
	private $iv;

	// Sign config
	private $_mode = MCRYPT_MODE_CBC;
	private $_cipher;
	private $_paddingType = 'PKCS7';


	public function __construct(ShopSettings $shop)
	{
		$this->_cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', $this->_mode, '');

		$this->key = $shop->getKey();
		$this->mid = $mid = $shop->getMid();

		$this->iv = $mid . strrev($mid);
	}

	/**
	 * @return string which can be signed
	 */
	abstract public function getStringForSign();


	public function sign()
	{
		return $this->signTransaction();
	}


	public function signTransaction()
	{
		if ($this->_paddingType == 'PKCS7') {
			$data = $this->AddPadding($this->getData());
		}

		mcrypt_generic_init($this->_cipher, $this->getHexKey(), $this->iv);
		$result = mcrypt_generic($this->_cipher, $data);
		mcrypt_generic_deinit($this->_cipher);

		return strtoupper(substr(bin2hex($result), 0, 32));
	}


	public function getHexKey()
	{
		return pack("H*", $this->key);
	}

	private function getData()
	{
		return sha1($this->getStringForSign(), true);
	}


	private function AddPadding($data)
	{
		$block = mcrypt_get_block_size('des', $this->_mode);
		$pad = $block - (strlen($data) % $block);
		$data .= str_repeat(chr($pad), $pad);
		return $data;
	}

}