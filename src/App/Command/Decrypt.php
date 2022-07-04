<?php
declare(strict_types=1);

namespace App\Command;

use App\SymmetricCrypto;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Decrypt extends Command
{
    private SymmetricCrypto $crypto;

    protected static $defaultName = 'decrypt';

    final public function __construct()
    {
        $this->crypto = new SymmetricCrypto;

        parent::__construct();
    }

    final protected function configure(): void
    {
        $this
            ->setDescription('Decrypt a file.')
            ->setHelp('Decrypt a file using a password.')
            ->addArgument('source', InputArgument::REQUIRED, 'Source path')
            ->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    final protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        $password = $input->getArgument('password');
        $decrypted = $this->crypto->decrypt(file_get_contents($source), $password);

        $output->write($decrypted, true);

        return Command::SUCCESS;
    }
}
