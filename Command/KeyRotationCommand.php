<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2016 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Command;

use Jose\Object\StorableJWKInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class KeyRotationCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('spomky-labs:jose:rotate-keys')
            ->setDescription('Rotate key')
            ->addArgument(
                'key',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'ttl',
                InputArgument::OPTIONAL,
                '',
                3600*24*7
            )
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command will create a new client.

  <info>php %command.full_name%</info>
EOT
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $key_name = $input->getArgument('key');
        if (!$this->getContainer()->has($key_name)) {
            $output->writeln(sprintf('<error>The key "%s" does not exist</error>', $key_name));
            return 1;
        }
        $key = $this->getContainer()->get($key_name);
        if (!$key instanceof StorableJWKInterface) {
            $output->writeln(sprintf('<error>The key "%s" is not a storable key</error>', $key_name));
            return 2;
        }

        if (!file_exists($key->getFilename())) {
            $output->writeln(sprintf('The key "%s" does not exist and will be created.', $key_name));
            $key->getAll();
        } else {
            $ttl = $input->getArgument('ttl');
            $mtime = filemtime($key->getFilename());
            if ($mtime+$ttl <= time()) {
                $output->writeln(sprintf('The key "%s" exists but expired. It will be updated.', $key_name));
                unlink($key->getFilename());
                $key->getAll();
            } else {
                $output->writeln(sprintf('The key "%s" exists and is not expired.', $key_name));
            }
        }
    }
}
