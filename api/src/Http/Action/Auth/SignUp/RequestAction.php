<?php


namespace Api\Http\Action\Auth\SignUp;


use Api\Model\User\UseCase\SignUp\Confirm\Handler;
use Api\Model\User\UseCase\SignUp\Request\Command;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

class RequestAction implements RequestHandlerInterface
{
    private $handler;
    
    public function __construct(Handler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = json_decode($request->getBody()->getContents());

        $command = new Command();

        $command->email = $body['email'] ?? '';
        $command->password = $body['password'] ?? '';

        try {
            $this->handler->handle($command);
        } catch (\DomainException $exception) {
            return new JsonResponse([
                'error' => $exception->getMessage(),
            ], 400);
        }

        return new JsonResponse([
            'email' => $command->email,
        ], 201);
    }
}