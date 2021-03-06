<?php
declare(strict_types=1);

namespace App\Command;

use App\SymmetricCrypto;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RemoveOldEmail extends Command
{
    private SymmetricCrypto $crypto;
    private const DEFAULT_CONFIG = [
        'host' => '',
        'username' => '',
        'password' => '',
        'before' => 'today',
        'ssl' => true,
        'port' => 993,
        'ignore-certificate' => false,
        'boxes' => [],
    ];

    protected static $defaultName = 'remove-old-email';

    final public function __construct()
    {
        $this->crypto = new SymmetricCrypto;

        parent::__construct();
    }

    final protected function configure()
    {
        $this
            ->setDescription('Remove old mail.')
            ->setHelp('Remove old mail from accounts described in encrypted json file.')
            ->addArgument( 'password', InputArgument::REQUIRED, 'Decryption password');
    }

    final protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = json_decode($this->crypto->decrypt(
            file_get_contents(sprintf('%s/../../../env.json.enc', __DIR__)),
            $input->getArgument('password'),
        ), true);

        foreach($config['accounts'] as $account){
            $account += self::DEFAULT_CONFIG;
            foreach($account['boxes'] as $box){
                $mailbox = sprintf(
                    '{%s:%s%s%s}%s',
                    $account['host'],
                    $account['port'],
                    $account['ssl'] ? '/ssl' : '',
                    $account['ignore-certificate'] ? '/novalidate-cert' : '',
                    $box,
                );
                $imap = imap_open($mailbox, $account['username'], $account['password']);
                $beforeDate = date('Y-m-d', strtotime($account['before'] . ' ago'));
                $mailIds = imap_search($imap, sprintf('BEFORE "%s"', $beforeDate)) ?: [];
                foreach($mailIds as $mailId){
                    imap_delete($imap, $mailId);
                }
                imap_expunge($imap);
                imap_close($imap);
                echo sprintf('Deleted %d mails from %s from box %s%s', count($mailIds), $account['username'], $box, PHP_EOL);
            }
        }

        return Command::SUCCESS;
    }
}
