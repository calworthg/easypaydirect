<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SamplePaymentGateway\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\SamplePaymentGateway\Gateway\Http\Client\ClientMock;

class ResponseCodeValidator extends AbstractValidator
{
    const RESULT_CODE = '3';

    /**
     * Performs validation of result code
     *
     * @param array $validationSubject
     * @return ResultInterface
     */
    public function validate(array $validationSubject)
    {
        if (!isset($validationSubject['response']) || !is_array($validationSubject['response'])) {
            throw new \InvalidArgumentException('Response does not exist');
        }
        
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(var_export($validationSubject['response'], true));
        $keyvalueresponse = explode("&", $validationSubject['response'][0]);
        $response = [];
        foreach($keyvalueresponse as $resp){
            $ele = explode("=", $resp);
            $response[$ele[0]]=$ele[1];
        }
        
        if ($this->isSuccessfulTransaction($response)) {
            return $this->createResult(
                true,
                []
            );
        } else {
            return $this->createResult(
                false,
                [__($this->getMessage($response))]
            );
        }
    }

    /**
     * @param array $response
     * @return bool
     */
    private function isSuccessfulTransaction(array $response)
    {
        if(isset($response["response_code"]) && $response["response_code"]=="300"){
            return false;
        } elseif(isset($response["response_code"]) && $response["response_code"] == "200") {
            return false;
        } else {
            return true;
        }
    }
    private function getMessage(array $response)
    {
        if (isset($response["responsetext"])) {
            return $response["responsetext"];
        } else {
            return "";
        }
    }
}
