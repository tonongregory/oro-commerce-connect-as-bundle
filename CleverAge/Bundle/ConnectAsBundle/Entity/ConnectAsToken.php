<?php
namespace CleverAge\Bundle\ConnectAsBundle\Entity;

use Oro\Bundle\CustomerBundle\Entity\CustomerUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ConnectAsToken
 *
 * @ORM\Entity()
 * @ORM\Table(name="cleverage_connect_as_token")
 */
class ConnectAsToken
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $token;

    /**
     * @var CustomerUser
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\CustomerBundle\Entity\CustomerUser")
     * @ORM\JoinColumn(referencedColumnName="id", name="customer_user_id")
     */
    protected $customerUser;

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }

    /**
     * @return CustomerUser
     */
    public function getCustomerUser(): ?CustomerUser
    {
        return $this->customerUser;
    }

    /**
     * @param CustomerUser $customerUser
     */
    public function setCustomerUser(CustomerUser $customerUser): void
    {
        $this->customerUser = $customerUser;
    }
}
