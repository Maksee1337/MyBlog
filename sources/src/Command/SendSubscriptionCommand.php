<?php

namespace App\Command;

use App\Entity\News;
use App\Entity\Subscribers;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Helper\ProcessHelper;

#[AsCommand(
    name: 'send-subscription',
    description: 'Add a short description for your command',
)]

class SendSubscriptionCommand extends Command
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var EntityManagertity
     */
    private $em;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * SendSubscriptionCommand constructor.
     * @param string|null           $name
     * @param MailerInterface       $mailer
     * @param ContainerInterface    $container
     * @param UrlGeneratorInterface $router
     */
    public function __construct(
        string $name = null,
        MailerInterface $mailer,
        ContainerInterface $container,
        UrlGeneratorInterface $router
    ) {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->container = $container;
        $this->em = $container->get('doctrine')->getManager();
        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument('period', InputArgument::REQUIRED, 'Argument description');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $period = $input->getArgument('period');
        $em = $this->em;
        $mailer = $this->mailer;
        $emails = $em->getRepository(Subscribers::class)->findAll();
        $locate = $this->container->get('translator')->getLocale();

        $conn = $em->getConnection();
        $sql = 'SELECT id, short, author FROM news WHERE date_time >= NOW() - INTERVAL  '.$period.' DAY';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $posts = $stmt->fetchAll();

        $context = $this->router->getContext();
        $context->setBaseUrl(sprintf('%s://%s', $context->getScheme(), $context->getHost()));

        $message = sprintf('Hello, we have %s new posts.<br><br>', count($posts));
        foreach ($posts as $v) {
            $link = $this->router->generate('News_ShowPost', ['_locale' => $locate, 'post' => $v['id']]);
            $message .= sprintf('<a href="%s">%s</a><br>', $link, $v['short']);
        }

        $unsubscribeLink = $this->router->generate('User_UnSubscribe', ['_locale' => $locate]);
        $message .= sprintf('<br>To unsubscribe <a href="%s">click here.</a>', $unsubscribeLink);

        $progressBar = new ProgressBar($output, count($emails));

        $output->writeln('Sending '. count($emails). ' emails...');
        foreach ($emails as $v) {
            $progressBar->advance();

            $email = new Email();
            $email->from('burm.courses@gmail.com');
            $email->to($v->getEmail());
            $email->subject('New posts');
            $email->html($message);
            $mailer->send($email);
        }

        $progressBar->finish();
        $output->writeln('');
        $io->success(count($emails). ' emails sent...');
        return Command::SUCCESS;
    }
}
