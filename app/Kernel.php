<?php

declare(strict_types=1);

namespace App;

use DIContainer\CachedContainer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Throwable;

class Kernel
{
    protected CachedContainer $cachedContainer;
    protected ContainerBuilder $containerBuilder;

    public function __construct(
        private readonly RequestContext $requestContext,
        private readonly ArgumentResolver $argumentResolver,
    ) {
        $this->cachedContainer = new CachedContainer();
        $this->containerBuilder = WarmUp::warmUpRoutes();
    }

    public function handle(Request $request): Response
    {
        $context = $this->requestContext->fromRequest($request);
        $compiledRoutes = $this->containerBuilder->getParameter('routes_url_matcher');
        $matcher = $this->getCompiledUrlMatcher($compiledRoutes, $context);

        try {
            $request->attributes->add(
                $matcher->match(
                    $request->getPathInfo()
                )
            );

            $controller = $this->getContainerControllerResolver()->getController($request);
            $arguments = $this->argumentResolver->getArguments($request, $controller);

            return call_user_func_array($controller, $arguments);
        } catch (ResourceNotFoundException) {
            return $this->createResponse(
                ['status' => 'fail', 'message' => 'Page not found'],
                Response::HTTP_NOT_FOUND,
            );
        } catch (Throwable $e) {
            $content = [
                'status' => 'fail',
                'message' => 'Server error occurred. 500',
            ];

            if ($this->cachedContainer->getParameterBag()->get('environment') !== 'prod') {
                $content['trace'] = $e->getTrace();
            }

            return $this->createResponse($content, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function createResponse(array $content, int $status): Response
    {
        return new Response(
            json_encode($content, JSON_THROW_ON_ERROR),
            $status,
            ['Content-type' => 'application/json;charset=utf-8'],
        );
    }

    protected function getContainerControllerResolver(): ContainerControllerResolver
    {
        return new ContainerControllerResolver($this->cachedContainer);
    }

    protected function getCompiledUrlMatcher(array $compiledRoutes, RequestContext $context): CompiledUrlMatcher
    {
        return new CompiledUrlMatcher($compiledRoutes, $context);
    }
}
