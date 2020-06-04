<?php
namespace Magento\SamplePaymentGateway\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\SamplePaymentGateway\Observer\DataAssignObserver;
use Magento\Payment\Gateway\Helper\SubjectReader;
class PaymentDataBuilder implements BuilderInterface
{
    const CCNUMBER = "";
    const CVV = "";
    const CCEXP = "";
    const AMOUNT = "";
    const ORDER_ID = "";
    /**
     * @inheritdoc
     */

    public function build(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);

        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();
        
        $result = [
            self::AMOUNT => $this->formatPrice(SubjectReader::readAmount($buildSubject)),
            self::CCNUMBER => $payment->getAdditionalInformation(
                DataAssignObserver::CCNUMBER
            ),
            self::CCEXP => $payment->getAdditionalInformation(
                DataAssignObserver::CCEXP
            ),
            self::CVV => $payment->getAdditionalInformation(
                DataAssignObserver::CVV
            ),
            self::ORDER_ID => $order->getOrderIncrementId(),
        ];

        return $result;
    }
    public function formatPrice($price){
        return number_format($price, 2);
    }
}