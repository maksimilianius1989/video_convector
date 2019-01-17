<?php


namespace Api\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BodyParamsMiddleware implements MiddlewareInterface
{

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $contentType = $request->getHeaderLine('Content-Type');

        $parts = explode(';', $contentType);
        $mime = trim(array_shift($parts));

        if (preg_match('#[/+]json$#', $mime)) {
            $rawBody = $request->getBody()->getContents();
            $parsedBody = json_decode($rawBody, true);

            if (!empty($rawBody) && json_last_error()) {
                throw new \InvalidArgumentException('Error when persing JSON request body:' . json_last_error_msg());
            }

            $request = $request->withParsedBody($parsedBody);
        }

        return $handler->handle($request);
    }
}