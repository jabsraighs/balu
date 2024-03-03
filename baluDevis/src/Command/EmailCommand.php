<?php 
namespace App\Command;

use App\Entity\Invoice;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:invoice',
    description: 'Envoie email au status valider.',
    hidden: false,
    aliases: ['app:auto'])]
class EmailCommand extends Command
{
    protected static $defaultName = 'app:auto';
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
        $currentDate = new \DateTimeImmutable();

        // Retrieve entities from the database where date_due is similar to the current date
        $entities = $this->entityManager->getRepository(Invoice::class)
            ->findBy(['dueDate' => $currentDate, 'paymentStatus' => 'en cours']);

        // Iterate through entities and send emails
        foreach ($entities as $entity) {
            $from = $entity->getUserInvoice()->getEmail();
            $clientEmail = $entity->getClient();
            $to = $clientEmail->getEmail();
            
            // Construct the email
            $email = (new Email())
                ->from($from)
                ->to($to)
                ->subject('Test Email')
                ->text('This is a test email sent from Symfony.');

            // Send the email using Symfony's Mailer component
            $this->mailer->send($email);

            $output->writeln('Email sent successfully to ' . $to);
        }

        return Command::SUCCESS;
    }
}

