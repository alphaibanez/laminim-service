<?php

namespace Lkt\Console\Commands;

use Lkt\Connectors\RestCountriesConnector;
use Lkt\Instances\LktCountry;
use Lkt\Locale\Enums\LangCode;
use Lkt\Locale\Locale;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class SyncRestCountriesCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('lkt:sync:rest-countries')
            ->setDescription('Synchronize with Rest Countries API')
            ->setHelp('Synchronize with Rest Countries API')
            ->addArgument('text', InputArgument::REQUIRED, 'Determines which text should be imported as country name: official|common')
            ->addArgument('connector', InputArgument::OPTIONAL, 'Specify which connector shall be used. If zero (0), uses an anonymous connector in order to fetch public API', 0);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $connectorName = $input->getArgument('connector');
        $anonymousConnector = (int)$connectorName === 0;
        if ($anonymousConnector) {
            $connector = RestCountriesConnector::defineAnonymous();

        } else {
            $connector = RestCountriesConnector::get($connectorName);
        }

        if (!$connector) return -1;

        $text = $input->getArgument('text');
        if (!in_array($text, ['official', 'common'])) {
            $output->writeln("Invalid text: {$text}");
        }

        $availableLanguages = Locale::getAvailableLangCodes();

        $fields = ['cca2', 'ccn3', 'name', 'translations'];
        $fieldsStr = implode(',', $fields);

        $response = $connector->query("/v3.1/all?fields={$fieldsStr}");
        $results = \json_decode($response->result);
        foreach ($results as $result) {
            $isoCodeCCA2 = trim($result->cca2);
            $isoCodeCCN3 = trim($result->ccn3);

            $country = LktCountry::getOne(LktCountry::getQueryCaller()->andIsoCodeAlpha2Equal($isoCodeCCA2));
            if ($country && $country->syncExcluded()) continue;

            if (!$country) $country = LktCountry::getInstance()->setIsoCodeAlpha2($isoCodeCCA2)->setIsoCodeNumeric3($isoCodeCCN3);

            $nameData = $country->getNameData();

            foreach ($availableLanguages as $language) {
                $restLang = match ($language) {
                    LangCode::Spanish => 'spa',
                    LangCode::Turkish => 'tur',
                    LangCode::Japanese => 'jpn',
                    LangCode::Italian => 'ita',
                    LangCode::Russian => 'rus',
                    LangCode::English => '',
                    default => null
                };

                if ($restLang === null) continue;

                $name = $restLang ? $result->translations->{$restLang}->{$text} : $result->name->{$text};
                $nameData[$language->value] = $name;
            }

            $country->setUpdatedAt(time())->setNameData($nameData)->save();
        }

        return 1;
    }
}