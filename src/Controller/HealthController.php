<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HealthController extends AbstractController
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    /**
     * Health check complet (DB + filesystem)
     */
    #[Route('/health', name: 'health_check', methods: ['GET'])]
    public function health(): JsonResponse
    {
        $status = 'healthy';
        $httpCode = Response::HTTP_OK;
        $checks = [];

        // Check database connection
        try {
            $this->connection->executeQuery('SELECT 1');
            $checks['database'] = [
                'status' => 'healthy',
                'message' => 'Database connection successful'
            ];
        } catch (\Exception $e) {
            $status = 'unhealthy';
            $httpCode = Response::HTTP_SERVICE_UNAVAILABLE;
            $checks['database'] = [
                'status' => 'unhealthy',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }

        // Check filesystem (cache directory writable)
        $cacheDir = $this->getParameter('kernel.cache_dir');
        if (is_writable($cacheDir)) {
            $checks['filesystem'] = [
                'status' => 'healthy',
                'message' => 'Cache directory is writable'
            ];
        } else {
            $status = 'unhealthy';
            $httpCode = Response::HTTP_SERVICE_UNAVAILABLE;
            $checks['filesystem'] = [
                'status' => 'unhealthy',
                'message' => 'Cache directory is not writable'
            ];
        }

        // Check uploads directory
        $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
        if (is_dir($uploadsDir) && is_writable($uploadsDir)) {
            $checks['uploads'] = [
                'status' => 'healthy',
                'message' => 'Uploads directory is writable'
            ];
        } else {
            $checks['uploads'] = [
                'status' => 'warning',
                'message' => 'Uploads directory does not exist or is not writable'
            ];
        }

        return new JsonResponse([
            'status' => $status,
            'timestamp' => (new \DateTime())->format('c'),
            'checks' => $checks
        ], $httpCode);
    }

    /**
     * Liveness probe - Verification que le conteneur est vivant
     */
    #[Route('/health/liveness', name: 'health_liveness', methods: ['GET'])]
    public function liveness(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'alive',
            'timestamp' => (new \DateTime())->format('c')
        ], Response::HTTP_OK);
    }

    /**
     * Readiness probe - Verification que l'application est prete a servir
     */
    #[Route('/health/readiness', name: 'health_readiness', methods: ['GET'])]
    public function readiness(): JsonResponse
    {
        // Check database is ready
        try {
            $this->connection->executeQuery('SELECT 1');
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'not_ready',
                'reason' => 'Database not available',
                'timestamp' => (new \DateTime())->format('c')
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        // Check cache directory is ready
        $cacheDir = $this->getParameter('kernel.cache_dir');
        if (!is_writable($cacheDir)) {
            return new JsonResponse([
                'status' => 'not_ready',
                'reason' => 'Cache directory not writable',
                'timestamp' => (new \DateTime())->format('c')
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return new JsonResponse([
            'status' => 'ready',
            'timestamp' => (new \DateTime())->format('c')
        ], Response::HTTP_OK);
    }
}
