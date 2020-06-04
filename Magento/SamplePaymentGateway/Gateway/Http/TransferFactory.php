<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SamplePaymentGateway\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\SamplePaymentGateway\Gateway\Request\MockDataRequest;

class TransferFactory implements TransferFactoryInterface
{
    /**
     * @var TransferBuilder
     */
    private $transferBuilder;

    /**
     * @param TransferBuilder $transferBuilder
     */
    public function __construct(
        TransferBuilder $transferBuilder
    ) {
        $this->transferBuilder = $transferBuilder;
    }

    /**
     * Builds gateway transfer object
     *
     * @param array $request
     * @return TransferInterface
     */
    public function create(array $request)
    {
        
        $query = "";
        foreach($request as $key=>$value){
            if($key != ""){
                $query .= $key."=".urlencode($value)."&";
            }
        }
        $fullquery = substr($query, 0, -1);
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug("******POSTING***********");
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(var_export($fullquery, true));
        return $this->transferBuilder
            ->setBody(json_encode($request, JSON_UNESCAPED_SLASHES))
            ->setMethod('POST')
            ->setHeaders(
                ["content-type"=>'application/json']
            )
            ->setUri("https://secure.easypaydirectgateway.com/api/transact.php?".$fullquery)
            ->build();
    }
}
