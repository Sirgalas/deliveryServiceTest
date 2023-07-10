<?php

declare(strict_types=1);

namespace App\Test\Functional;

use App\Exception\UnexpectedValueException;
use App\Cache\SimpleCacheBridge;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestAssertionsTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class Requester extends Assert
{
    use WebTestAssertionsTrait;

    public function __construct(
        private readonly KernelBrowser                                $client,
        private readonly RouterInterface                              $router,
        private readonly SimpleCacheBridge $storage,
    ) {
    }

    /**
     * @param class-string      $routeClass
     * @param string|false|null $token      false: *default bearer token*,
     *                                      string: "Bearer: <token>",
     *                                      null: *anonymous*
     *
     * @throws \JsonException
     */
    public function get(
        string $routeClass,
        array $routeParams = [],
        null | string | false $token = false,
        int $expectCode = Response::HTTP_OK
    ): Dto\Response {
        $path = $this->getPath($routeClass, $routeParams);
        $this->client->request('GET', $path, [], [], $this->headers($token));
        $response = $this->response();
        $this->assertResponseStatusCode($response, $expectCode);

        return $response;
    }

    /**
     * @param class-string      $routeClass
     * @param string|false|null $token      false: *default bearer token*,
     *                                      string: "Bearer: <token>",
     *                                      null: *anonymous*
     *
     * @throws \JsonException
     */
    public function post(
        string $routeClass,
        array $routeParams = [],
        array $content = [],
        array $files = [],
        null | string | false $token = false,
        int $expectCode = Response::HTTP_OK
    ): Dto\Response {
        $path = $this->getPath($routeClass, $routeParams);
        $content = json_encode($content, \JSON_THROW_ON_ERROR);

        $this->client->request('POST', $path, [], $files, $this->headers($token), $content);
        $response = $this->response();
        $this->assertResponseStatusCode($response, $expectCode);

        return $response;
    }

    /**
     * @param class-string      $routeClass
     * @param string|false|null $token      false: *default bearer token*,
     *                                      string: "Bearer: <token>",
     *                                      null: *anonymous*
     *
     * @throws \JsonException
     */
    public function delete(
        string $routeClass,
        array $routeParams = [],
        null | string | false $token = false,
        int $expectCode = Response::HTTP_OK
    ): Dto\Response {
        $path = $this->getPath($routeClass, $routeParams);
        $this->client->request('DELETE', $path, [], [], $this->headers($token));

        $response = $this->response();
        $this->assertResponseStatusCode($response, $expectCode);

        return $response;
    }

    /**
     * @param class-string $routeClass
     *
     * @throws \JsonException
     */
    public function upload(
        string $routeClass,
        array $routeParams = [],
        array $files = [],
        string $token = null,
        int $expectCode = Response::HTTP_OK
    ): Dto\Response {
        $response = $this->post(
            routeClass: $routeClass,
            routeParams: $routeParams,
            files: $files,
            token: $token,
            expectCode: $expectCode
        );

        if (\array_key_exists('id', $response->content)) {
            $content = $response->content;
            $response->content = [$content];
        }

        array_map(function ($file) {
            self::assertIsArray($file);
            self::assertArrayHasKey('id', $file);
            self::assertArrayHasKey('url', $file);
            self::assertIsString($file['id']);
            self::assertIsString($file['url']);

            return $file;
        }, $response->content);

        return $response;
    }


    /**
     * @param class-string $className
     */
    private function getPath(string $className, array $params = []): string
    {
        if (!class_exists($className) || !is_subclass_of($className, AbstractController::class)) {
            throw new UnexpectedValueException(AbstractController::class, $className);
        }

        return $this->router->generate($className, $params);
    }

    /**
     * @param string|false|null $token false: *default bearer token*,
     *                                 string: "Bearer: <token>",
     *                                 null: *anonymous*
     *
     * @return array<string, string>
     */
    private function headers(null | string | false $token = false, bool $json = true): array
    {
        $headers = [];

        if ($json) {
            $headers['CONTENT_TYPE'] = 'application/json';
            $headers['HTTP_ACCEPT'] = 'application/json';
        }

        if (false === $token) {
            $token = $this->bearer();
        }

        if (\is_string($token)) {
            $headers['HTTP_AUTHORIZATION'] = $token;
        }

        return $headers;
    }

    /**
     * @psalm-suppress MixedAssignment
     *
     * @throws \JsonException
     */
    private function response(): Dto\Response
    {
        $response = $this->client->getResponse();
        $data = [
            'code' => $response->getStatusCode(),
            'type' => $response->headers->get('content_type'),
            'headers' => $response->headers,
        ];

        $content = $response->getContent();
        if (\is_string($content) && \in_array($data['type'], ['application/json', 'application/vnd.api+json'], true)) {
            /** @var mixed */
            $content = json_decode(
                '' === $content
                    ? '{}'
                    : $content,
                true,
                512,
                \JSON_THROW_ON_ERROR
            );

            if (\is_array($content) && \array_key_exists('data', $content) && \is_array($content['data'])) {
                /** @var mixed */
                $content = $content['data'];
            }
        }

        $data['content'] = $content;

        return new Dto\Response($data);
    }

    private function assertResponseStatusCode(Dto\Response $response, int $expectCode = Response::HTTP_OK): void
    {
        if ($response->code !== $expectCode) {
            dd($response->content);
        }
        self::assertTrue($expectCode === $response->code, "expected code {$expectCode} !== {$response->code}");
    }
}