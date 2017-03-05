<?php
/**
 * Author: Ivo Toman
 */

namespace TwentyFourPay\Transaction;


interface IXmlProcess
{
	function getAmount();
	function getCurrency();
	function getPspTxnId();
	function getMsTxnId();
	function getTimestamp();
	function getResult();
	function getSign();
}