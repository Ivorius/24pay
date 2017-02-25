<?php
/**
 * Author: Ivo Toman
 */

namespace TwentyFourPay;


class ShopSettings
{
	private $mid;
	private $key;
	private $eshopId;

	public function __construct($mid, $eshopId, $key)
	{
		$this->setMid($mid);
		$this->setEshopId($eshopId);
		$this->setKey($key);

	}

	public function setMid($mid)
	{
		if (mb_strlen($mid) != 8)
			throw new \InvalidArgumentException("Mid must be 8 characters length");

		$this->mid = $mid;
	}

	public function setEshopId($eshopId)
	{
		if (!is_numeric($eshopId) || $eshopId == 0)
			throw new \InvalidArgumentException("EshopId must be numeric");
		if (strlen($eshopId) > 10)
			throw new \InvalidArgumentException("EshopId must be max 10 characters length");

		$this->eshopId = $eshopId;
	}

	public function setKey($key)
	{
		if (mb_strlen($key) != 64)
			throw new \InvalidArgumentException("Key must be 64 characters length");

		$this->key = $key;
	}

	public function getMid()
	{
		return $this->mid;
	}

	public function getEshopId()
	{
		return $this->eshopId;
	}

	public function getKey()
	{
		return $this->key;
	}


}