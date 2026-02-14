<?php

namespace Lkt\Console\Commands;

use Lkt\Connectors\OpenExchangeRatesConnector;
use Lkt\Connectors\RestCountriesConnector;
use Lkt\Instances\LktCountry;
use Lkt\Instances\LktCurrency;
use Lkt\Locale\Enums\LangCode;
use Lkt\Locale\Locale;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class SyncOpenExchangeRatesCommand extends Command
{
    protected static $defaultName = 'lkt:sync:open-exchange-rates';

    protected function configure()
    {
        $this
            ->setDescription('Synchronize with Open Exchange Rates API')
            ->setHelp('Synchronize with Open Exchange Rates API')
            ->addArgument('connector', InputArgument::REQUIRED, 'Specify which connector shall be used');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $connectorName = $input->getArgument('connector');
        $connector = OpenExchangeRatesConnector::get($connectorName);

        if (!$connector) return -1;
        $availableLanguages = Locale::getAvailableLangCodes();
        $availableCurrencies = Locale::getAvailableCurrencyCodes();

        if (count($availableCurrencies) === 0) {
            $output->writeln("This app hasn't currencies");
            die();
        }

        $baseCurrency = Locale::$baseCurrency;
        $queryArgs = [];
        // Only for premium accounts
//        if ($baseCurrency) {
//            $queryArgs['base'] = $baseCurrency->value;
//        }

        $response = $connector->query('/api/latest.json', $queryArgs);
        $results = \json_decode($response->result);

        if ($response->info['http_code'] !== 200) {
            $output->writeln("There is an error!");
            $output->writeln("Message: {$results->message}");
            $output->writeln("Description: {$results->description}");
            die();
        }

        $rates = $results->rates;
        $baseValue = $rates->{$baseCurrency->value};

        foreach ($availableCurrencies as $availableCurrency) {
            $codeAlpha3 = $availableCurrency->value;

            $currency = LktCurrency::getOne(LktCurrency::getQueryCaller()->andIsoCodeAlpha3Equal($codeAlpha3));
            if ($currency && $currency->syncExcluded()) continue;

            if (!$currency) {
                $currency = LktCurrency::getInstance()->setIsoCodeAlpha3($codeAlpha3);

                $nameData = [];
                foreach ($availableLanguages as $language) {
                    $nameData[$language->value] = $codeAlpha3;
                }

                $currency->setNameData($nameData);
            }

            $rate = $rates->{$codeAlpha3};
            $rate /= $baseValue;
            $currency->setFactorToDefault($rate)->save();
        }

        return 1;
    }
}