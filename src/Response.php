<?php
/**
 * Author: Ivo Toman
 */

namespace TwentyFourPay;


use TwentyFourPay\Transaction\IXmlProcess;

class Response
{

	/**
	 * @var ShopSettings
	 */
	private $shop;

	/**
	 * @var IXmlProcess
	 */
	private $transaction;

	/**
	 * @var SignResponse
	 */
	private $signResponse;

	/**
	 * Request constructor.
	 * @param ShopSettings $shop
	 * @param IXmlProcess $transaction
	 */
	public function __construct(ShopSettings $shop, IXmlProcess $transaction)
	{
		$this->shop = $shop;
		$this->transaction = $transaction;

		$this->signResponse = new SignResponse(
			$shop,
			$transaction->getAmount(),
			$transaction->getCurrency(),
			$transaction->getPspTxnId(),
			$transaction->getMsTxnId(),
			$transaction->getTimestamp(),
			$transaction->getResult()
		);
	}

	/**
	 * @return bool
	 */
	public function isValid()
	{
		// sign from pay24 transaction is same like computed sign in signResponse
		if($this->transaction->getSign() === $this->signResponse->sign())
			return true;
		else
			return false;
	}



}