<?php

namespace App\Test\Controller;

use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InvoiceControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/invoice/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Invoice::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Invoice index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'invoice[createdAt]' => 'Testing',
            'invoice[dueDate]' => 'Testing',
            'invoice[paymentStatus]' => 'Testing',
            'invoice[totalAmount]' => 'Testing',
            'invoice[quote]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Invoice();
        $fixture->setCreatedAt('My Title');
        $fixture->setDueDate('My Title');
        $fixture->setPaymentStatus('My Title');
        $fixture->setTotalAmount('My Title');
        $fixture->setQuote('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Invoice');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Invoice();
        $fixture->setCreatedAt('Value');
        $fixture->setDueDate('Value');
        $fixture->setPaymentStatus('Value');
        $fixture->setTotalAmount('Value');
        $fixture->setQuote('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'invoice[createdAt]' => 'Something New',
            'invoice[dueDate]' => 'Something New',
            'invoice[paymentStatus]' => 'Something New',
            'invoice[totalAmount]' => 'Something New',
            'invoice[quote]' => 'Something New',
        ]);

        self::assertResponseRedirects('/invoice/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getDueDate());
        self::assertSame('Something New', $fixture[0]->getPaymentStatus());
        self::assertSame('Something New', $fixture[0]->getTotalAmount());
        self::assertSame('Something New', $fixture[0]->getQuote());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Invoice();
        $fixture->setCreatedAt('Value');
        $fixture->setDueDate('Value');
        $fixture->setPaymentStatus('Value');
        $fixture->setTotalAmount('Value');
        $fixture->setQuote('Value');

        $this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/invoice/');
        self::assertSame(0, $this->repository->count([]));
    }
}
