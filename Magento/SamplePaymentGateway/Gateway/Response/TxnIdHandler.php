<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SamplePaymentGateway\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Vault\Api\Data\PaymentTokenFactoryInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Payment\Model\InfoInterface;

class TxnIdHandler implements HandlerInterface
{
    const transactionid = 'transactionid';

    private $paymentTokenFactory;
    /**
     * Handles transaction id
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function __construct(
        PaymentTokenFactoryInterface $paymentTokenFactory
    ) {
        $this->paymentTokenFactory = $paymentTokenFactory;
    }
    public function handle(array $handlingSubject, array $response)
    {
        if (!isset($handlingSubject['payment'])
            || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $handlingSubject['payment'];

        $payment = $paymentDO->getPayment();
        \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Psr\Log\LoggerInterface')
            ->debug(var_export($response, true));
        $keyvalueresponse = explode("&", $response[0]);
        $trueresponse = [];
        foreach ($keyvalueresponse as $resp) {
            $ele = explode("=", $resp);
            $trueresponse[$ele[0]] = $ele[1];
        }
        
        /** @var $payment \Magento\Sales\Model\Order\Payment */
        $payment->setTransactionId($trueresponse[self::transactionid]);
        $payment->setIsTransactionClosed(false);
        $paymentToken = $this->getVaultPaymentToken($trueresponse);
        if(null !== $paymentToken) {
            $extensionAttributes = $this->getExtensionAttributes($payment);
            $extensionAttributes->setVaultPaymentToken($paymentToken);
        }
    }

    protected function getVaultPaymentToken($transaction)
    {
        // Check token existing in gateway response
        $token = $transaction["customer_vault_id"];
        if (empty($token)) {
            return null;
        }

        /** @var PaymentTokenInterface $paymentToken */
        $paymentToken = $this->paymentTokenFactory->create(PaymentTokenFactoryInterface::TOKEN_TYPE_CREDIT_CARD);
        $paymentToken->setGatewayToken($token);
        $paymentToken->setTokenDetails($this->convertDetailsToJSON([
            'maskedCC' => $transaction["cc_number"],
        ]));

        return $paymentToken;
    }

    private function convertDetailsToJSON($details)
    {
        $json = \Zend_Json::encode($details);
        return $json ? $json : '{}';
    }

    private function getExtensionAttributes(InfoInterface $payment)
    {
        $extensionAttributes = $payment->getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->paymentExtensionFactory->create();
            $payment->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }
}
