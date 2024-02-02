<?php 
// src/Command/SendEmailCommand.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Invoice; // Replace YourEntity with the actual entity name

class SendEmailCommand extends Command
{
    protected static $defaultName = 'command:send-auto';
    private $mailer;
    private $entityManager;

    public function __construct(MailerInterface $mailer, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setDescription('Send test email');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get current date
        $currentDate = new \DateTime();

        // Retrieve entities from the database where date_due is similar to the current date
        $entities = $this->entityManager->getRepository(Invoice::class)
            ->findBy(['created_at' => $currentDate]);

        foreach ($entities as $entity) {
        // Get the email from the entity
        $emailSender = $entity->getUserQuote->getEmail();
        $emailRecipient = $entity->getClient->getEmail();

        // Construct the email using the retrieved email address
        $email = (new Email())
            ->from($emailSender)
            ->to($emailRecipient)
            ->subject('Test Email /email command')
            ->text('This is a test email sent from Symfony.');
            // Send the email using Symfony's Mailer component
            $this->mailer->send($email);

            $output->writeln('Email sent successfully to ' . $entity->getEmail());
        }

        return Command::SUCCESS;
    }
}

