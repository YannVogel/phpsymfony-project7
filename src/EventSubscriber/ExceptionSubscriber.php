<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $data = [];

        if ($exception instanceof NotFoundHttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => 'Resource not found.'
            ];
        } else if ($exception instanceof BadRequestHttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => 'Bad request. There are errors on given fields.'
            ];
        }else if ($exception instanceof MethodNotAllowedHttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => 'Method not allowed.'
            ];
        } else if($exception instanceof HttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => 'Full authentication is required to access this resource.'
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'Bad request.'
            ];
        }

        $response = new JsonResponse($data);
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
