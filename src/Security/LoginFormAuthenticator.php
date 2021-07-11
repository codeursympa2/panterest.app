<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;
    private $session;
    private $container;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder,SessionInterface $session,ContainerInterface $container)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->session=$session;
        $this->container=$container;
    }
    /**
     * elle est appéllé dans chaque page puis aprés il verifie si le nom de la route est app_login
     * et la methode est POST il fait l'authentification 
     */
    public function supports(Request $request):bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * est appellé lorsque supports est egal à true
     */
    public function getCredentials(Request $request):array
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        /**
         * sauvegarde du dernier identifiant(email)
         */
        $request->getSession()->set(Security::LAST_USERNAME,$credentials['email']);

        return $credentials;
    }
/**
 * il reçoit les credentials(informations du formulaire)
 */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new CustomUserMessageAuthenticationException("Token invalide.");
        } 

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Informations invalides.');
        }

        return $user; 
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        /**
         
        *if(!$this->passwordEncoder->isPasswordValid($user, $credentials['password'])){
           * throw new CustomUserMessageAuthenticationException('Mot de passe incorrecte');
        *}
        */
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        //$request->$this->session->getFlashBag()->add('success', 'Connexion reussi avec succès');
        $this->container->get('session')->getFlashBag()->add('success', 'Bienvenue '.$token->getUser()->getFullName().' !');
        //si l'on est connecté on sera redirigé vers la ou etait apres connexion 
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }
        //sinon il s'agit d'une authentification normale donc redirection à la page d'accueil
        return new RedirectResponse($this->urlGenerator->generate('home'));
        //throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl()
    {
        //urlGenerator nous permet de generer une route avec un url donné
        return $this->urlGenerator->generate('app_login');
    }

    /**
     * Redefinition depuis AbstractLoginFormAuthenticator
     * On controle si l'utilisateur n'est pas connecté et qu'il veut acceder a une page securisée on le redirige vers la page de connection.
     *
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
       $request->getSession()->getFlashBag()->add('error','Veillez vous connecter.');
        //recupération de l'url
        $url = $this->getLoginUrl();
        //redirection
        return new RedirectResponse($url);
    }
}
