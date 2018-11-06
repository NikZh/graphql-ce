<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProductCompareGraphQl\Model;

use Magento\Framework\Session\SessionManagerInterface;

class VisitorDataProvider
{
    const VISITOR_ID_KEY = 'visitor_id';
    const CUSTOMER_ID_KEY = 'customer_id';

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @param SessionManagerInterface $sessionManager
     */
    public function __construct(SessionManagerInterface $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    /**
     * @return string|null
     */
    public function getVisitorId()
    {
        return $this->getVisitorData(self::VISITOR_ID_KEY);
    }

    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->getVisitorData(self::CUSTOMER_ID_KEY);
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    private function getVisitorData(string $key)
    {
        $data = $this->sessionManager->getVisitorData();
        return $data[$key] ?? null;
    }
}
