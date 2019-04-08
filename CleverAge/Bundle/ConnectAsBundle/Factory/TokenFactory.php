<?php
namespace CleverAge\Bundle\ConnectAsBundle\Factory;

use CleverAge\Bundle\ConnectAsBundle\Entity\ConnectAsToken;
use Exception;
use Oro\Bundle\CustomerBundle\Entity\CustomerUser;

/**
 * Class TokenFactory
 * @package CleverAge\Bundle\ConnectAsBundle
 */
class TokenFactory
{
    /**
     * @var int
     */
    private $tokenLength;

    /**
     * TokenFactory constructor.
     * @param int $tokenLength
     */
    public function __construct(int $tokenLength)
    {
        $this->tokenLength = $tokenLength;
    }

    /**
     * Create new connectAs token
     * @param CustomerUser $customerUser
     * @return ConnectAsToken
     * @throws Exception
     */
    public function create(CustomerUser $customerUser)
    {
        $token = new ConnectAsToken();
        $token->setCustomerUser($customerUser);
        $token->setToken(bin2hex(random_bytes($this->tokenLength)));

        return $token;
    }
}
