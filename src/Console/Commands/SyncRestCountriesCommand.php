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
        $anonymousConnector = strlen($connectorName) === 0;
        if ($anonymousConnector) {
            $connector = RestCountriesConnector::defineAnonymous();

        } else {
            $connector = RestCountriesConnector::get($connectorName);
        }

        if (!$connector) {
            $output->writeln("No connector. Aborted.");
            return -1;
        }

        $text = $input->getArgument('text');
        if (!in_array($text, ['official', 'common'])) {
            $output->writeln("Invalid text: {$text}");
        }

        $availableLanguages = Locale::getAvailableLangCodes();

        $fields = ['codes.alpha_2', 'codes.alpha_3', "names.{$text}", 'names.translations'];
        $fieldsStr = implode('%2C', $fields);

        $itemsPerPage = 100; // Free plan limit
        $requested = 0;
        $total = 254; // Amount of results received at 2026/07

        do {
            $q = "?pretty&limit={$itemsPerPage}&response_fields={$fieldsStr}";

            if ($requested > 0) {
                $q .= "&offset={$requested}";
            }

            $response = $connector->query($q);
            $results = \json_decode($response->result);
            $objects = $results->data->objects;
            $meta = $results->data->meta;

            $total = (int)$meta->total;
            $requested += (int)$meta->count;

            foreach ($objects as $result) {
                $isoCodeCCA2 = trim($result->codes->alpha_2);
                $isoCodeCCN3 = trim($result->codes->alpha_3);

                $country = LktCountry::getOne(LktCountry::getQueryBuilder()->andIsoCodeAlpha2Equal($isoCodeCCA2));
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

                    $name = $restLang ? $result->names->translations->{$restLang}->{$text} : $result->names->{$text};
                    $nameData[$language->value] = $name;
                }

                $country->setUpdatedAt(time())->setNameData($nameData)->save();
            }

        } while ($requested <= $total);

        return 1;
    }
}