# Sylius Geo Fixture Generator

Command to generate yaml for geographical regions that can be used within sylius fixtures.

## Install

```bash
git clone chrissnyder2337/sylius-geo-fixture-generator
cd  sylius-geo-fixture-generator
composer install
```

## Use:

Generate yaml for all countries:

```bash
sylius:generate_geo_yaml 
```

Generate yaml for only a subset of countries (United States, Canada, France, and Italy):

```bash
./console sylius:generate_geo_yaml US CA FR IT
```

Output results to a file:
```bash
./console sylius:generate_geo_yaml US CA FR IT > sylius_fixtures.yaml
```


See all options:

```bash
 ./console sylius:generate_geo_yaml --help
```
