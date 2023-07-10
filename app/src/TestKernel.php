<?php

declare(strict_types=1);

namespace App;


use App\Doctrine\Dbal\Fetcher;
use App\Cache\SimpleCacheBridge;
use App\Exception\UnexpectedClassException;
use App\Exception\UnexpectedValueException;
use App\Test\Functional\Requester;
use App\Test\MailerClient;
use Doctrine\DBAL\Query\QueryBuilder;
use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\RouterInterface;

class TestKernel extends WebTestCase
{
    protected ?KernelBrowser $client = null;
    protected ?Fetcher $fetcher = null;
    protected ?SimpleCacheBridge $cache = null;
    protected ?Filesystem $fs = null;
    protected ?RouterInterface $router;
    protected ?MailerClient $mailer = null;

    /** Data generator. */
    public function dg(): Generator
    {
        return Factory::create();
    }

    public function requester(): Requester
    {
        if (null === $this->client || null === $this->router || null === $this->cache) {
            throw new UnexpectedValueException('KernelBrowser, RouterInterface, SimpleCacheBridge', null);
        }

        return new Requester($this->client, $this->router, $this->cache);
    }

    final protected function getQueryBuilder(): QueryBuilder
    {
        if ($this->fetcher instanceof Fetcher) {
            return $this->fetcher->getQueryBuilder();
        }

        throw new UnexpectedClassException(Fetcher::class, '');
    }

    /**
     * @psalm-suppress PropertyTypeCoercion
     */
    final protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->fetcher = static::$kernel->getContainer()->get(Fetcher::class);
        $this->cache = static::$kernel->getContainer()->get('test.cache');
        $this->router = static::$kernel->getContainer()->get('router');
        $this->mailer = new MailerClient();
        $this->fs = new Filesystem();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->client = null;
        $this->fetcher = null;
        $this->cache = null;
        $this->router = null;
        $this->mailer = null;
        $this->fs = null;
    }
}