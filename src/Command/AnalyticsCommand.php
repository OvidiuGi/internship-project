<?php

namespace App\Command;

use App\Analytics\LogParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AnalyticsCommand extends Command
{
    protected static $defaultName = 'app:generate-analytics';

    protected static $defaultDescription = 'Generates analytics';

    private LogParser $parser;

    public function __construct(LogParser $parser)
    {
        $this->parser = $parser;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $analytics = $this->parser->parseLogs();
        $table = new Table($output);
        $table
            ->setHeaders(['Username','Nr. Logins'])
            ->setRow(1, [new TableSeparator()]);
        $array = [];
        foreach ($analytics->getApiLogins() as $apiLogin) {

            /** @var string $username */
            $username = $apiLogin->context['email'];
            $array[$username] = $analytics->getNumberApiLoginsForUsername($username);
            $array = \array_unique($array);
        }

        \arsort($array);
        foreach ($array as $value) {
            $table->addRows([
                [\array_key_first($array), $value]
            ]);
        }

        $table->render();

        $io->success('Analytics rendered successfully!');

        return Command::SUCCESS;
    }
}
