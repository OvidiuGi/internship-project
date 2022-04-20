<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateAccountCommand extends Command
{
    use LoggerAwareTrait;

    protected static $defaultName = 'app:create-account';

    protected static $defaultDescription = 'Creates a new account.';

    private string $plainPassword;

    private EntityManagerInterface $entityManager;

    private ValidatorInterface $validator;

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('firstName', InputArgument::REQUIRED, 'First Name.')
            ->addArgument('lastName', InputArgument::REQUIRED, 'Last Name.')
            ->addArgument('cnp', InputArgument::REQUIRED, 'CNP.')
            ->addArgument('email', InputArgument::REQUIRED, 'E-mail.')
            ->addOption(
                'role',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'User role',
                ['ROLE_USER']
            );
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new Question('Please enter the user\'s password: ');
        $question->setHidden(true);
        $this->plainPassword = $helper->ask($input, $output, $question);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user = new User();

        $user->firstName = $input->getArgument('firstName');
        $user->lastName = $input->getArgument('lastName');
        $user->cnp = $input->getArgument('cnp');
        $user->email = $input->getArgument('email');
        $user->setRoles($input->getOption('role'));
        $user->plainPassword = $this->plainPassword;
        $user->setPassword($this->passwordHasher->hashPassword($user, $this->plainPassword));
        $violationList = $this->validator->validate($user);
        if ($violationList->count() > 0) {
            foreach ($violationList as $violation) {
                $io->error($violation);
            }
            $this->logger->warning(
                'Failed creating account by command!',
                [
                    'userEmail' => $user->email,
                    'violations' => $violationList,
                    'commandName' => CreateAccountCommand::$defaultName
                ]
            );

            return self::FAILURE;
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->logger->info(
            'Successfully created account by command',
            [
                'userEmail' => $user->email,
                'commandName' => CreateAccountCommand::$defaultName
            ]
        );

        $io->success('Account was successfully created!');

        return Command::SUCCESS;
    }
}
