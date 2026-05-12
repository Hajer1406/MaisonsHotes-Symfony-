<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:test-password')]
class TestPasswordCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Test password hashing and verification')
            ->addArgument('username', InputArgument::REQUIRED, 'Username to test')
            ->addArgument('newPassword', InputArgument::REQUIRED, 'New password to set');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');
        $newPassword = $input->getArgument('newPassword');

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user) {
            $output->writeln('<error>User not found: ' . $username . '</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>Found user:</info> ' . $user->getUsername());
        $output->writeln('<info>Current password hash:</info> ' . substr($user->getPassword(), 0, 30) . '...');

        // Hash new password
        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $output->writeln('<info>New password hash:</info> ' . substr($hashedPassword, 0, 30) . '...');

        // Update password
        $user->setPassword($hashedPassword);
        $this->entityManager->flush();

        $output->writeln('<comment>Password updated in database!</comment>');

        // Verify the password
        $isValid = $this->passwordHasher->isPasswordValid($user, $newPassword);
        $output->writeln('<info>Password verification:</info> ' . ($isValid ? '<fg=green>VALID ✓</>' : '<fg=red>INVALID ✗</>'));

        return Command::SUCCESS;
    }
}
