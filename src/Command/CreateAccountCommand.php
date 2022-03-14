<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Xml\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateAccountCommand extends Command
{
    protected static $defaultName = 'app:create-account';
    protected static $defaultDescription = 'Creates a new account.';

    private string $plainPassword;

    private EntityManagerInterface $entityManager;

    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) {
        $this->validator = $validator;
        $this->entityManager = $entityManager;

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
                ['ROLE_ADMIN']
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

        $firstName = $input->getArgument('firstName');
        $lastName = $input->getArgument('lastName');
        $cnp = $input->getArgument('cnp');
        $email = $input->getArgument('email');
        $roles = $input->getOption('role');

        $user = new User();

        $user->firstName = $firstName;
        $user->lastName = $lastName;
        $user->cnp = $cnp;
        $user->email = $email;
        $user->setRoles($roles);
        $user->password = $this->plainPassword;

        $violationList = $this->validator->validate($user);
        if ($violationList->count() > 0) {
            foreach ($violationList as $violation) {
                $io->error($violation);
            }

            return self::FAILURE;
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Account was successfully created!');

        return Command::SUCCESS;
    }
}
