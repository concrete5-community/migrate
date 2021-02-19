<?php

namespace A3020\Migrate\Controller;

use A3020\Migrate\Payload\Structure;
use A3020\Migrate\Payload\Records;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;

class Endpoint extends \Concrete\Core\Controller\Controller implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var Repository
     */
    private $config;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    public function __construct(
        Repository $config,
        ResponseFactory $responseFactory,
        Request $request)
    {
        parent::__construct();

        $this->config = $config;
        $this->responseFactory = $responseFactory;
        $this->request = $request;
    }

    public function receive()
    {
        if (!$this->request->isPost()) {
            // Only accept POST requests
            return $this->responseFactory->json([
                'error' => 'Method not Allowed.',
            ],Response::HTTP_METHOD_NOT_ALLOWED);
        }

        // Check if pulling is allowed
        if ((bool) $this->config->get('migrate::settings.allow_pull', false) === false) {
            return $this->responseFactory->json([
                'error' => 'Unauthorized. Pulling is disabled.',
            ],Response::HTTP_UNAUTHORIZED);
        }

        // Check if the authorization token is correct
        $token = $this->request->request->get('token');
        if ($this->config->get('migrate::auth.token') !== $token) {
            return $this->responseFactory->json([
                'error' => 'Unauthorized.',
            ],Response::HTTP_UNAUTHORIZED);
        }

        // Determine what information is requested
        switch ($this->request->request->get('method', 'structure')) {
            case 'structure':
                /** @var Structure $structure */
                $structure = $this->app->make(Structure::class);

                $result = $structure->get();
            break;
            case 'records':
                $options = [
                    'table' => $this->request->request->get('table'),
                    'start_at' => (int) $this->request->request->get('start_at', 0),
                ];

                if ($this->request->request->has('max_size')) {
                    $options['max_size'] = (int) $this->request->request->get('max_size');
                }

                /** @var Records $records */
                $records = $this->app->make(Records::class);

                $result = $records->get($options);
            break;
            default:
                return $this->responseFactory->json([
                'error' => 'Method not Allowed.',
            ],Response::HTTP_METHOD_NOT_ALLOWED);
        }

        return $this->responseFactory->json($result);
    }
}
