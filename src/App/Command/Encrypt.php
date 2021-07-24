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
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
            ->addOption(
                'opslimit',
                null,
                InputArgument::OPTIONAL,
                sprintf('Limit number of cpus (default: %d)', SODIUM_CRYPTO_PWHASH_OPSLIMIT_SENSITIVE),
                SODIUM_CRYPTO_PWHASH_OPSLIMIT_SENSITIVE,
            )
            ->addOption(
                'memlimit',
                null,
                InputArgument::OPTIONAL,
                sprintf('Limit amount of memory (default: %d)', SODIUM_CRYPTO_PWHASH_MEMLIMIT_SENSITIVE),
                SODIUM_CRYPTO_PWHASH_MEMLIMIT_SENSITIVE,
            );
    }

    final protected function execute(InputInterface $input, OutputInterface $output)
    {
        $destination = $input->getArgument('destination');
        $source = $input->getArgument('source');
        $password = $input->getArgument('password');
        $opslimit = (int)$input->getOption('opslimit');
        $memlimit = (int)$input->getOption('memlimit');
        file_put_contents($destination, $this->crypto->encrypt(
            file_get_contents($source),
            $password,
            $opslimit,
            $memlimit,
        ));
        $output->writeln('Successfully processed');

        return Command::SUCCESS;
    }
}
