<?php
namespace CleverAge\Bundle\ConnectAsBundle\Controller;

use CleverAge\Bundle\ConnectAsBundle\Factory\TokenFactory;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Exception as ExceptionAlias;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\CustomerBundle\Entity\CustomerUser;
use Oro\Bundle\CustomerBundle\Entity\CustomerUserManager;
use Oro\Bundle\ScopeBundle\Manager\ScopeManager;
use Oro\Bundle\WebsiteBundle\DependencyInjection\Configuration;
use Oro\Bundle\WebsiteBundle\DependencyInjection\OroWebsiteExtension;
use Oro\Bundle\WebsiteBundle\Provider\ScopeCriteriaProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ConnectAsController
 * @package CleverAge\Bundle\ConnectAsBundle\Controller
 */
class ConnectAsController extends Controller
{
    /**
     * @var TokenFactory
     */
    private $tokenFactory;

    /**
     * @var CustomerUserManager
     */
    private $customerUserManager;

    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @var ConfigManager
     */
    private $configManager;

    /**
     * @var ScopeManager
     */
    private $scopeManager;

    /**
     * ConnectAsController constructor.
     * @param TokenFactory $factory
     * @param CustomerUserManager $customerUserManager
     * @param Registry $doctrine
     * @param ConfigManager $configManager
     * @param ScopeManager $scopeManager
     */
    public function __construct(
        TokenFactory $factory,
        CustomerUserManager $customerUserManager,
        Registry $doctrine,
        ConfigManager $configManager,
        ScopeManager $scopeManager
    ) {
        $this->tokenFactory = $factory;
        $this->customerUserManager = $customerUserManager;
        $this->doctrine = $doctrine;
        $this->configManager = $configManager;
        $this->scopeManager = $scopeManager;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws ExceptionAlias
     *
     * @Route(path="/connect-as", name="cleverage_connect_as")
     */
    public function connectAsAction(Request $request)
    {
        $em = $this->doctrine->getManagerForClass(CustomerUser::class);
        $customerUser = $em->getRepository(CustomerUser::class)->find($request->get('id'));

        if (!$customerUser instanceof CustomerUser) {
            throw new NotFoundHttpException('Customer user not found.');
        }
        $token = $this->tokenFactory->create($customerUser);
        $em->persist($token);
        $em->flush();

        $websiteUrl = $this->configManager->get(
            OroWebsiteExtension::ALIAS . '.' . Configuration::SECURE_URL,
            false,
            false,
            $this->scopeManager->find(
                ScopeManager::BASE_SCOPE,
                [ScopeCriteriaProvider::WEBSITE => $customerUser->getWebsite()]
            )->getId()
        );

        return new RedirectResponse($websiteUrl . '?_token='. $token->getToken());
    }
}
