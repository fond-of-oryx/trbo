<?php

namespace FondOfOryx\Service\Trbo\Api;

use FondOfOryx\Service\Trbo\Mapper\TrboMapperInterface;
use Generated\Shared\Transfer\TrboTransfer;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class TrboApi implements TrboApiInterface
{
    /**
     * @var string
     */
    public const HTTP_METHOD = 'post';

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * @var \FondOfOryx\Service\Trbo\Api\TrboApiConfigurationInterface
     */
    protected $trboApiConfiguration;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \FondOfOryx\Service\Trbo\Mapper\TrboMapperInterface
     */
    protected $trboMapper;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \GuzzleHttp\ClientInterface $client
     * @param \FondOfOryx\Service\Trbo\Api\TrboApiConfigurationInterface $trboApiConfiguration
     * @param \FondOfOryx\Service\Trbo\Mapper\TrboMapperInterface $trboMapper
     */
    public function __construct(
        LoggerInterface $logger,
        ClientInterface $client,
        TrboApiConfigurationInterface $trboApiConfiguration,
        TrboMapperInterface $trboMapper
    ) {
        $this->client = $client;
        $this->trboApiConfiguration = $trboApiConfiguration;
        $this->logger = $logger;
        $this->trboMapper = $trboMapper;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Generated\Shared\Transfer\TrboTransfer|null
     */
    public function requestData(Request $request): ?TrboTransfer
    {
        try {
            $result = $this->client->request(
                static::HTTP_METHOD,
                $this->trboApiConfiguration->getApiUrl(),
                $this->trboApiConfiguration->getConfiguration($request),
            );

            $data = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            return $this->trboMapper->mapApiResponseToTransfer($data);
        } catch (Throwable $exception) {
            $this->logger->info($exception->getMessage(), $exception->getTrace());
        }

        return null;
    }
}
