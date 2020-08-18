<?php

declare(strict_types=1);

namespace ChrisSnyder2337\SyliusGeoFixtureGenerator;

use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Subdivision\Subdivision;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class GenerateGeoFixturesCommand extends Command
{
    protected function configure(): void
    {
        $this
          ->setName('sylius:generate_geo_yaml')
          ->setDescription('Generates fixture yaml for provinces and countries for use in Sylius.')
          ->addOption('suite', null, InputOption::VALUE_REQUIRED, 'Suite name to use (default: default)')
          ->addArgument(
            'countries',
            InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
            'Which countries (2 letter code) do you want to include (separate multiple countries with a space)?',
            []
          );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $countriesArg = $input->getArgument('countries');

        if (\count($countriesArg) > 0) {
            $countryCodes = $countriesArg;
        } else {
            $countryCodes = [];
            foreach ((new CountryRepository())->getAll() as $country) {
                $countryCodes[] = $country->getCountryCode();
            }
        }

        $provinceCodes = [];

        $subdivisionRepository = new SubdivisionRepository();
        foreach ($countryCodes as $countryCode) {
            /** @var Subdivision[] $provinces */
            $provinces = $subdivisionRepository->getAll([$countryCode]);

            if (! $provinces) {
                continue;
            }

            $provinceCodes[$countryCode] = [];
            foreach ($provinces as $province) {
                if ($province->getIsoCode()) {
                    $provinceCodes[$countryCode][$province->getIsoCode()] = $province->getName();
                }
            }
        }

        $fixtures = [
          'sylius_fixtures' => [
            $input->getOption('suite') ?: 'default' => [
              'fixtures' => [
                'geographical' => [
                  'options' => [
                    'countries' => $countryCodes,
                    'provinces' => $provinceCodes,
                  ],
                ],
              ],
            ],
          ],
        ];

        $yaml = Yaml::dump($fixtures, 10);

        $output->write($yaml, true, OutputInterface::OUTPUT_NORMAL);

        return 0;
    }
}
