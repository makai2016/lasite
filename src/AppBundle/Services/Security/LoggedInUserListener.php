<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-2-1
 * Time: 上午4:10
 */

namespace AppBundle\Services\Security;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * 已登录的用户禁止访问登录页面、注册页面
 *
 * @author kkma
 */
class LoggedInUserListener
{
    public function __construct(Router $router, ContainerInterface $container)
    {
        $this->_router = $router;
        $this->_container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $denyRouteNameList = ['security.login'];

        if ($event->isMasterRequest()) {
            $routeName = $event->getRequest()->get('_route');
            if (in_array($routeName, $denyRouteNameList)) {
                $security = $this->_container->get('security.authorization_checker');
                if ($security->isGranted('IS_AUTHENTICATED_FULLY')) {
                    $url = $this->_router->generate('home');
                    $event->setResponse(new RedirectResponse($url));
                }
            }
        }
    }
}