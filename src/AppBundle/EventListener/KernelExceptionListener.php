<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-2-1
 * Time: 上午4:15
 */

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
class KernelExceptionListener
{
    private $storage;

    public function __construct(TokenStorage $storage)
    {
        $this->storage = $storage;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $user = null;

        if ($token = $this->storage->getToken()) {
            $user = $token->getUser();
        }

        $request = $event->getRequest();
        $exception = $event->getException();
        if ($request->isXmlHttpRequest()) {
            if ($exception instanceof AuthenticationException) {
                $event->setResponse(new JsonResponse(['message' => $exception->getMessage()], 401));
                $event->stopPropagation();
            } else if ($exception instanceof AccessDeniedException) {
                if ($user instanceof UserInterface) {
                    $event->setResponse(new JsonResponse(['message' => $exception->getMessage()], 403));
                } else {
                    $event->setResponse(new JsonResponse(['message' => 'Unauthorized'], 401));
                }
                $event->stopPropagation();
            }
        }

    }
}