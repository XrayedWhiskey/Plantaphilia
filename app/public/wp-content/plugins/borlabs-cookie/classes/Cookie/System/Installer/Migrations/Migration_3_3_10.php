<?php
/*
 *  Copyright (c) 2026 Borlabs GmbH. All rights reserved.
 *  This file may not be redistributed in whole or significant part.
 *  Content of this file is protected by international copyright laws.
 *
 *  ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 *  @copyright Borlabs GmbH, https://borlabs.io
 */

declare(strict_types=1);

namespace Borlabs\Cookie\System\Installer\Migrations;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Localization\DefaultLocalizationStrings;
use Borlabs\Cookie\System\Config\DialogLocalization;
use Borlabs\Cookie\System\Config\Traits\PackageAutoUpdateTimeHelperTrait;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Option\Option;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Script\UpdateJavaScriptConfigFileJobService;
use Borlabs\Cookie\System\Style\StyleBuilder;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

class Migration_3_3_10
{
    use PackageAutoUpdateTimeHelperTrait;

    private Container $container;

    private $correctedDialogStrings = [
        'de' => [
            'iabTcfDescriptionLegInt' => 'Einige unserer <a href="#" role="button" data-borlabs-cookie-actions="vendors">{{ totalVendors }} Partner</a> verarbeiten Ihre Daten (jederzeit widerrufbar) auf der Grundlage eines <a href="#" role="button" data-borlabs-cookie-actions="leg-int">berechtigten Interesses</a>.',
            'iabTcfDescriptionMoreInformation' => 'Weitere Informationen über die Verwendung Ihrer Daten und über unsere Partner finden Sie unter <a href="#" role="button" data-borlabs-cookie-actions="preferences">Einstellungen</a> oder in unserer Datenschutzerklärung.',
            'iabTcfDescriptionRevoke' => 'Wir können bestimmte Inhalte nicht ohne Ihre Einwilligung anzeigen. Sie können Ihre Auswahl jederzeit unter <a href="#" role="button" data-borlabs-cookie-actions="preferences">Einstellungen</a> widerrufen oder anpassen. Ihre Auswahl wird nur auf dieses Angebot angewendet.',
            'legalInformationDescriptionRevoke' => 'Sie können Ihre Auswahl jederzeit unter <a href="#" role="button" data-borlabs-cookie-actions="preferences">Einstellungen</a> widerrufen oder anpassen.',
        ],
        'dk' => [
            'iabTcfDescriptionLegInt' => 'Nogle af vores <a href="#" role="button" data-borlabs-cookie-actions="vendors">{{ totalVendors }} partnere</a> behandler dine data (kan til enhver tid tilbagekaldes) baseret på <a href="#" role="button" data-borlabs-cookie-actions="leg-int">legitime interesser</a>.',
            'iabTcfDescriptionMoreInformation' => 'Du kan finde flere oplysninger om brugen af dine data og om vores partnere under <a href="#" role="button" data-borlabs-cookie-actions="preferences">Indstillinger</a> eller i vores privatlivspolitik.',
            'iabTcfDescriptionRevoke' => 'Vi kan ikke vise bestemt indhold uden dit samtykke. Du kan til enhver tid tilbagekalde eller justere dit valg under <a href="#" role="button" data-borlabs-cookie-actions="preferences">Indstillinger</a>. Dit valg vil kun gælde for dette tilbud.',
            'legalInformationDescriptionRevoke' => 'Du kan til enhver tid tilbagekalde eller justere dit valg under <a href="#" role="button" data-borlabs-cookie-actions="preferences">Indstillinger</a>.',
        ],
        'es' => [
            'iabTcfDescriptionLegInt' => 'Algunos de nuestros <a href="#" role="button" data-borlabs-cookie-actions="vendors">{{ totalVendors }} socios</a> tratan tus datos (revocables en cualquier momento) sobre la base de un <a href="#" role="button" data-borlabs-cookie-actions="leg-int">interés legítimo</a>.',
            'iabTcfDescriptionMoreInformation' => 'Puedes encontrar más información sobre el uso de tus datos y sobre nuestros socios en <a href="#" role="button" data-borlabs-cookie-actions="preferences">Ajustes</a> o en nuestra política de privacidad.',
            'iabTcfDescriptionRevoke' => 'No podemos mostrar determinados contenidos sin tu consentimiento. Puedes revocar o ajustar tu selección en cualquier momento en <a href="#" role="button" data-borlabs-cookie-actions="preferences">Ajustes</a>. TSu selección solo se aplicará a esta oferta.',
            'legalInformationDescriptionRevoke' => 'Puedes revocar o ajustar tu selección en cualquier momento en <a href="#" role="button" data-borlabs-cookie-actions="preferences">Ajustes</a>.',
        ],
        'fr' => [
            'iabTcfDescriptionLegInt' => 'Certains de nos <a href="#" role="button" data-borlabs-cookie-actions="vendors">Partenaires {{ totalVendors }}</a> traitent vos données (révocables à tout moment) sur la base de l\'<a href="#" role="button" data-borlabs-cookie-actions="leg-int">Intérêt légitime</a>.',
            'iabTcfDescriptionMoreInformation' => 'Vous trouverez plus d\'informations sur l\'utilisation de vos données et sur nos partenaires dans la rubrique <a href="#" role="button" data-borlabs-cookie-actions="preferences">Paramètres</a> ou dans notre politique de confidentialité.',
            'iabTcfDescriptionRevoke' => 'Nous ne pouvons pas afficher certains consentements sans votre consentement. Vous pouvez révoquer ou modifier votre sélection à tout moment sous <a href="#" role="button" data-borlabs-cookie-actions="preferences">Paramètres</a>. Votre sélection ne s\'appliquera qu\'à cette offre.',
            'legalInformationDescriptionRevoke' => 'Vous pouvez révoquer ou modifier votre sélection à tout moment dans la rubrique <a href="#" role="button" data-borlabs-cookie-actions="preferences">Paramètres</a>.',
        ],
        'it' => [
            'iabTcfDescriptionLegInt' => 'Alcuni dei nostri <a href="#" role="button" data-borlabs-cookie-actions="vendors">{{ totalVendors }} partner</a> trattano i tuoi dati (revocabile in qualsiasi momento) sulla base di un <a href="#" role="button" data-borlabs-cookie-actions="leg-int">interesse legittimo</a>.',
            'iabTcfDescriptionMoreInformation' => 'Puoi trovare maggiori informazioni sull\'utilizzo dei tuoi dati e sui nostri partner alla voce <a href="#" role="button" data-borlabs-cookie-actions="preferences">Impostazioni</a> o nella nostra informativa sulla privacy.',
            'iabTcfDescriptionRevoke' => 'Non possiamo mostrare determinati contenuti senza il tuo consenso. Puoi revocare o modificare la tua selezione in qualsiasi momento alla voce <a href="#" role="button" data-borlabs-cookie-actions="preferences">Impostazioni</a>. La tua selezione sarà applicata solo a questa offerta.',
            'legalInformationDescriptionRevoke' => 'Puoi revocare o modificare la selezione in qualsiasi momento nelle <a href="#" role="button" data-borlabs-cookie-actions="preferences">Impostazioni</a>.',
        ],
        'nl' => [
            'iabTcfDescriptionLegInt' => 'Sommige van onze <a href="#" role="button" data-borlabs-cookie-actions="vendors">{{ totalVendors }} partners</a> verwerken uw gegevens (op elk moment herroepbaar) op basis van <a href="#" role="button" data-borlabs-cookie-actions="leg-int">legitiem belang</a>.',
            'iabTcfDescriptionMoreInformation' => 'Meer informatie over het gebruik van uw gegevens en over onze partners vindt u onder <a href="#" role="button" data-borlabs-cookie-actions="preferences">Instellingen</a> of in ons privacybeleid.',
            'iabTcfDescriptionRevoke' => 'We kunnen bepaalde inhoud niet tonen zonder uw toestemming. U kunt uw keuze op elk moment onder <a href="#" role="button" data-borlabs-cookie-actions="preferences">Instellingen</a> intrekken of aanpassen. Uw keuze wordt alleen toegepast op deze aanbieding.',
            'legalInformationDescriptionRevoke' => 'U kunt uw keuze altijd intrekken of aanpassen via <a href="#" role="button" data-borlabs-cookie-actions="preferences">Instellingen</a>.',
        ],
        'pl' => [
            'iabTcfDescriptionLegInt' => 'Niektórzy z naszych <a href="#" role="button" data-borlabs-cookie-actions="vendors">{{ totalVendors }}</a> partnerów przetwarzają Twoje dane (z możliwością odwołania w dowolnym momencie) w oparciu o <a href="#" role="button" data-borlabs-cookie-actions="leg-int">uzasadniony interes</a>.',
            'iabTcfDescriptionMoreInformation' => 'Więcej informacji na temat wykorzystania Twoich danych i na temat naszych partnerów znajdziesz w obszarze <a href="#" role="button" data-borlabs-cookie-actions="preferences">Ustawienia</a> lub w naszych zasadach ochrony danych osobowych.',
            'iabTcfDescriptionRevoke' => 'Nie możemy wyświetlać określonych treści bez Twojej zgody. Możesz wycofać lub zmienić swój wybór w dowolnym momencie w <a href="#" role="button" data-borlabs-cookie-actions="preferences">Ustawieniach</a>. Twój wybór zostanie zastosowany tylko do tej oferty.',
            'legalInformationDescriptionRevoke' => 'Swój wybór możesz wycofać lub zmienić w dowolnym momencie w <a href="#" role="button" data-borlabs-cookie-actions="preferences">Ustawieniach</a>.',
        ],
    ];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run()
    {
        $dialogLocalization = $this->container->get(DialogLocalization::class);
        $language = $this->container->get(Language::class);
        $option = $this->container->get(Option::class);
        $updateJavaScriptConfigFileJobService = $this->container->get(UpdateJavaScriptConfigFileJobService::class);
        $wpFunction = $this->container->get(WpFunction::class);
        $configuredLanguages = [];

        // In a multisite network, each site can have a different locale.
        $localeOption = $option->getThirdPartyOption('WPLANG', '');
        $defaultLanguageCode = $language->determineLanguageCodeLength(is_string($localeOption->value) && strlen($localeOption->value) >= 2 ? $localeOption->value : BORLABS_COOKIE_DEFAULT_LANGUAGE);
        $configuredLanguages[$defaultLanguageCode] = $defaultLanguageCode;

        // Retrieve all available languages when a multilingual plugin is active
        $availableLanguages = $language->getLanguageList();

        foreach ($availableLanguages->list as $languageData) {
            $configuredLanguages[$languageData->key] = $languageData->key;
        }

        // Update JavaScript configuration
        foreach ($configuredLanguages as $languageCode) {
            if ($languageCode !== 'en') {
                $dialogLocalizationSaveStatus = $language->runInLanguageContext($languageCode, function () use ($dialogLocalization, $languageCode) {
                    $defaultLocalizationStrings = DefaultLocalizationStrings::get()['dialog'];
                    $localization = $dialogLocalization->load($languageCode);
                    $localization->iabTcfDescriptionLegInt = str_replace(' aria-expanded="false"', '', $this->correctedDialogStrings[$languageCode]['iabTcfDescriptionLegInt'] ?? $defaultLocalizationStrings['iabTcfDescriptionLegInt']);
                    $localization->iabTcfDescriptionMoreInformation = str_replace(' aria-expanded="false"', '', $this->correctedDialogStrings[$languageCode]['iabTcfDescriptionMoreInformation'] ?? $defaultLocalizationStrings['iabTcfDescriptionMoreInformation']);
                    $localization->iabTcfDescriptionRevoke = str_replace('', ' aria-expanded="false"', $this->correctedDialogStrings[$languageCode]['iabTcfDescriptionRevoke'] ?? $defaultLocalizationStrings['iabTcfDescriptionRevoke']);
                    $localization->legalInformationDescriptionRevoke = str_replace(' aria-expanded="false"', '', $this->correctedDialogStrings[$languageCode]['legalInformationDescriptionRevoke'] ?? $defaultLocalizationStrings['legalInformationDescriptionRevoke']);

                    return $dialogLocalization->save($localization, $languageCode);
                });
            } else {
                $dialogLocalizationSaveStatus = true;
            }

            $this->container->get(Log::class)->info(
                'Dialog localization config ({{ language }}) updated: {{ status }}',
                [
                    'language' => $languageCode,
                    'status' => $dialogLocalizationSaveStatus ? 'Yes' : 'No',
                ],
            );

            $status = $this->container->get(ScriptConfigBuilder::class)->updateJavaScriptConfigFileAndIncrementConfigVersion(
                $languageCode,
            );

            $this->container->get(Log::class)->info(
                'JavaScript config ({{ language }}) file updated: {{ status }}',
                [
                    'language' => $languageCode,
                    'status' => $status ? 'Yes' : 'No',
                ],
            );

            // Update CSS file
            $status = $this->container->get(StyleBuilder::class)->updateCssFileAndIncrementStyleVersion(
                $wpFunction->getCurrentBlogId(),
                $languageCode,
            );

            $this->container->get(Log::class)->info(
                'CSS file ({{ language }}) updated: {{ status }}',
                [
                    'blogId' => $wpFunction->getCurrentBlogId(),
                    'language' => $languageCode,
                    'status' => $status ? 'Yes' : 'No',
                ],
            );

            $updateJavaScriptConfigFileJobService->updateJob($languageCode);
        }

        // Prior to version 3.3.9, the DefaultLocalizationStrings class is already loaded, so the localization strings cannot be updated during the upgrade process.
        $this->container->get(ThirdPartyCacheClearerManager::class)->clearCache();
    }
}
