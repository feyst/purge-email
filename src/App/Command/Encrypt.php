<?php
declare(strict_types=1);

namespace App\Command;

use App\SymmetricCrypto;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Encrypt extends Command
{
    private SymmetricCrypto $crypto;

    protected static $defaultName = 'encrypt';

    final public function __construct()
    {
        $this->crypto = new SymmetricCrypto;

        parent::__construct();
    }

    final protected function configure(): void
    {
        $this
            ->setDescription('Encrypt a file.')
            ->setHelp('Encrypt a file using a password.')
            ->addArgument('source', InputArgument::REQUIRED, 'Source path')
            ->addArgument('destination', InputArgument::REQUIRED, 'Destination path')
            ->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    final protected function execute(InputInterface $input, OutputInterface $output)
    {
        $destination = $input->getArgument('destination');
        $source = $input->getArgument('source');
        $password = $input->getArgument('password');
        file_put_contents($destination, $this->crypto->encrypt(file_get_contents($source), $password));
        $output->writeln('Successfully processed');

        return Command::SUCCESS;
    }
}
