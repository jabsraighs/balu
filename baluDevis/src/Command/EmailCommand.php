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

class EmailCommand extends Command
{
    protected static $defaultName = 'app:send-email-auto';
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

        // Iterate through entities and send emails
        foreach ($entities as $entity) {
            // Construct the email
            $email = (new Email())
                ->from('your_email@example.com')
                ->to('recipient@example.com')
                ->subject('Test Email')
                ->text('This is a test email sent from Symfony.');

            // Send the email using Symfony's Mailer component
            $this->mailer->send($email);

            $output->writeln('Email sent successfully to ' . $entity->getEmail());
        }

        return Command::SUCCESS;
    }
}

