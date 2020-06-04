<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SamplePaymentGateway\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;

class AuthorizationRequest implements BuilderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(
        ConfigInterface $config
    ) {
        $this->config = $config;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $payment */
        $paymentDO = $buildSubject['payment'];
        $order = $paymentDO->getOrder();
        $address = $order->getShippingAddress();
        $payment = $paymentDO->getPayment();
        
        return [
            'type' => 'sale',
            'orderid' => $order->getOrderIncrementId(),
            'amount' => $order->getGrandTotalAmount(),
            'email' => $address->getEmail(),
            'security_key' => $this->config->getValue(
                'merchant_gateway_key',
                $order->getStoreId()
            ),
            'ccnumber' => $payment->getAdditionalInformation('ccnumber'),
            'ccexp' => $payment->getAdditionalInformation('ccexp'),
            'cvv' => $payment->getAdditionalInformation('cvv'),
            'customer_vault'=>'add_customer'
        ];
    }
}
