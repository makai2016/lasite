<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 17-12-15
 * Time: 上午5:55
 */

namespace AppBundle\Services\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class AuthenticationHandler implements AuthenticationFailureHandlerInterface
{
    private $_router;
    private $_session;
    private $_managerUri;

    public function __construct(RouterInterface $router, Session $session,$manageUri)
    {
        $this->_router  = $router;
        $this->_session = $session;
        $this->_managerUri = $manageUri;
    }

    /**
     * 登录失败事件
     *
     * @param Request $request
     * @param AuthenticationException $exception
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {

        if ($request->isXmlHttpRequest()) {
            $array = array('success' => false, 'message' => $exception->getMessage());
            $response = new Response(json_encode($array));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        return new RedirectResponse( $this->_router->generate('login') );
    }
}