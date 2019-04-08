<?php
namespace CleverAge\Bundle\ConnectAsBundle\Security;

use CleverAge\Bundle\ConnectAsBundle\Entity\ConnectAsToken;
use Doctrine\ORM\ORMException;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\SecurityBundle\Authentication\Token\UsernamePasswordOrganizationTokenFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Class ConnectAsAuthenticator
 * @package CleverAge\Bundle\ConnectAsBundle\Security
 */
class ConnectAsAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var TokenStorageInterface
     */
    protected $securityContext;

    /**
    * @var AuthenticationManagerInterface
    */
    protected $authenticationManager;

    /**
     * @var UsernamePasswordOrganizationTokenFactoryInterface
     */
    protected $usernamePasswordOrganizationTokenFactory;

    /**
     * @var DoctrineHelper
     */
    protected $doctrineHelper;

    /**
     * @var ConnectAsToken|null
     */
    protected $connectAsToken;

    /**
     * ConnectAsAuthenticator constructor.
     * @param RouterInterface $router
     * @param TokenStorageInterface $tokenStorage
     * @param UsernamePasswordOrganizationTokenFactoryInterface $usernamePasswordOrganizationTokenFactory
     * @param DoctrineHelper $doctrineHelper
     */
    public function __construct(
        RouterInterface $router,
        TokenStorageInterface $tokenStorage,
        UsernamePasswordOrganizationTokenFactoryInterface $usernamePasswordOrganizationTokenFactory,
        DoctrineHelper $doctrineHelper
    ) {
        $this->router = $router;
        $this->securityContext = $tokenStorage;
        $this->usernamePasswordOrganizationTokenFactory = $usernamePasswordOrganizationTokenFactory;
        $this->doctrineHelper = $doctrineHelper;
    }

    /**
     * @inheritdoc
     */
    public function supports(Request $request)
    {
        if (!$request->query->has('_token')) {
            return false;
        }
        /** @noinspection PhpUndefinedMethodInspection */
        $this->connectAsToken = $this->doctrineHelper
            ->getEntityRepository(ConnectAsToken::class)
            ->findOneByToken($request->query->get('_token'));

        if (!$this->connectAsToken instanceof ConnectAsToken) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($this->connectAsToken->getCustomerUser()->getUsername());
    }

    /**
     * @inheritdoc
     */
    public function getCredentials(Request $request)
    {
        return ['_token' => $request->query->get('_token')];
    }

    /**
     * @inheritdoc
     */
    public function supportsRememberMe()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $response = new RedirectResponse($request->getSchemeAndHttpHost());

        $authenticatedToken = $this->usernamePasswordOrganizationTokenFactory->create(
            $token->getUser(),
            $token->getCredentials(),
            'frontend',
            $token->getUser()->getOrganization(),
            $token->getRoles()
        );

        $this->securityContext->setToken($authenticatedToken);
        $em = $this->doctrineHelper->getEntityManager(ConnectAsToken::class);
        try {
            $em->remove($this->connectAsToken);
            $em->flush();
        } catch (ORMException $e) {
        }

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(null, Response::HTTP_FORBIDDEN);
    }

    /**
     * @inheritdoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @inheritdoc
     */
    public function createAuthenticatedToken(UserInterface $user, $providerKey)
    {
        return parent::createAuthenticatedToken($user, 'frontend');
    }
}
