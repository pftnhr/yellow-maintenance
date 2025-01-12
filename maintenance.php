<?php
// Maintenance extension, https://github.com/GiovanniSalmeri/yellow-maintenance

class YellowMaintenance {
    const VERSION = "0.8.20";
    public $yellow;         // access to API

    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("maintenanceIps", "");
        $this->yellow->language->setDefaults([
            "Language: de",
            "CoreError503Title: Dienst nicht verfügbar",
            "CoreError503Text: Wir sind wegen Wartungsarbeiten zeitweise außer Betrieb. [yellow error]",
            "MaintenanceDescription: Zeitweise nicht verfügbarer Inhalt",
            "MaintenancePageError: <a href=\"@url\">Bitte melde dich an</a> oder autorisiere die Adresse @ip.",
            "Language: en",
            "CoreError503Title: Service unavailable",
            "CoreError503Text: We are temporarily down for maintenance. [yellow error]",
            "MaintenanceDescription: Content temporarily unavailable",
            "MaintenancePageError: <a href=\"@url\">Log in</a> or authorise the address @ip.",
            "Language: es",
            "CoreError503Title: Servicio no disponible",
            "CoreError503Text: Estamos temporalmente en estado de mantenimiento. [yellow error]",
            "MaintenanceDescription: Contenido temporalmente no disponible",
            "MaintenancePageError: <a href=\"@url\">Por favor accede</a> o autorice la dirección @ip.",
            "Language: fr",
            "CoreError503Title: Service indisponible",
            "CoreError503Text: Nous sommes temporairement en état de maintenance. [yellow error]",
            "MaintenanceDescription: Contenu temporairement indisponible",
            "MaintenancePageError: <a href=\"@url\">Veuillez vous connecter</a> ou autoriser l'adresse @ip.",
            "Language: it",
            "CoreError503Title: Servizio non disponibile",
            "CoreError503Text: Siamo temporaneamente in stato di manutenzione. [yellow error]",
            "MaintenanceDescription: Contenuto temporaneamente non disponibile",
            "MaintenancePageError: <a href=\"@url\">Per favore accedi</a> o autorizza l'indirizzo @ip.",
            "Language: nl",
            "CoreError503Title: Dienst niet beschikbaar",
            "CoreError503Text: We zijn tijdelijk offline voor onderhoud. [yellow error]",
            "MaintenanceDescription: Tijdelijk onbeschikbare inhoud",
            "MaintenancePageError: <a href=\"@url\">Gelieve aan te melden</a> or het adres @ip toe te staan.",
            "Language: pt",
            "CoreError503Title: Serviço indisponível",
            "CoreError503Text: Estamos temporariamente em baixa para manutenção. [yellow error]",
            "MaintenanceDescription: Conteúdo temporariamente indisponível",
            "MaintenancePageError: <a href=\"@url\">Por favor, entre</a> ou autorize o endereço @ip.",
        ]);
    }

    // Handle page layout
    public function onParsePageLayout($page, $name) {
	$isMaintenanceIp = in_array($this->yellow->toolbox->getServer("REMOTE_ADDR"), array_merge(preg_split("/\s*,\s*/", $this->yellow->system->get("maintenanceIps")), [ "127.0.0.1", "::1" ]));
        if (($page->get("status")=="maintenance" || $this->yellow->system->get("status")=="maintenance") && $this->yellow->lookup->getRequestHandler()=="core" && !$isMaintenanceIp) {
            $page->set("description", $this->yellow->language->getTextHtml("maintenanceDescription"));
            $pageError = "";
            if ($this->yellow->extension->isExisting("edit")) {
                $pageError .= str_replace([ "@url", "@ip" ], [ $page->get("editPageUrl"), $this->yellow->toolbox->getServer("REMOTE_ADDR") ], $this->yellow->language->getText("maintenancePageError"));
            }
            $page->error(503, $pageError);
        }
    }
}
