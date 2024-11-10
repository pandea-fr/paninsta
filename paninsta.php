<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Paninsta extends Module
{
    public function __construct()
    {
        $this->name = 'paninsta';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Pandea.fr';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        
        $this->bootstrap = true;
        $this->configToken = 'PANINSTA_TOKEN';
        $this->configPhotoOnly = 'PANINSTA_PHOTO_ONLY';
        $this->configPhotoMax = 'PANINSTA_PHOTO_MAX';
        parent::__construct();

        $this->displayName = $this->trans('Instagram', [], 'Modules.Paninsta.Admin');
        $this->description = $this->trans('Galerie photo instagram', [], 'Modules.Paninsta.Admin');

        $this->confirmUninstall = $this->trans('Etes vous sur de vouloir déinstaller ce module ?', [], 'Modules.Paninsta.Admin');

        
        if (!Configuration::get($this->configToken)) {
            $this->warning = $this->trans('No name provided', [], 'Modules.Paninsta.Admin');
        }
    }

    public function install()
    {

        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return (
            parent::install() &&
            $this->registerHook('displayHome') &&
            Configuration::updateValue($this->configToken, 'past instagram token here')  &&
            Configuration::updateValue($this->configPhotoOnly, true)  &&
            Configuration::updateValue($this->configPhotoMax, 3)
        ); 
    }

    public function uninstall()
    {
        return (
            parent::uninstall() && 
            $this->unregisterHook('displayHome') &&
            Configuration::deleteByName($this->configToken) &&
            Configuration::deleteByName($this->configPhotoOnly) &&
            Configuration::deleteByName($this->configPhotoMax)
        );
    }

    /**
     * This method handles the module's configuration page
     * @return string The page's HTML content 
     */
    public function getContent()
    {
        $confirmation = null;

        if (Tools::isSubmit('submitPaninstaGallery')) {
            Configuration::updateValue($this->configToken, Tools::getValue($this->configToken));
            Configuration::updateValue($this->configPhotoOnly, Tools::getValue($this->configPhotoOnly));
            Configuration::updateValue($this->configPhotoMax, Tools::getValue($this->configPhotoMax));
            $confirmation = 'ok';
        }

        $this->context->smarty->assign([
            'paninsta_token' => Configuration::get($this->configToken),
            'paninsta_photo_only' => Configuration::get($this->configPhotoOnly),
            'paninsta_photo_max' => Configuration::get($this->configPhotoMax),
            'form_action' => $_SERVER['REQUEST_URI'],
            'config_name_token' => $this->configToken,
            'config_name_photo_only' => $this->configPhotoOnly,
            'config_name_photo_max' => $this->configPhotoMax,
            'confirmation' => $confirmation
        ]);

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }


    private function getInstagramPhotos()
    {
        $accessToken = Configuration::get($this->configToken); // Récupération du jeton d'accès
        $limit = Configuration::get($this->configPhotoMax)*2; // Limite du nombre de photos à récupérer
        $photoOnly = Configuration::get($this->configPhotoOnly); // Ne prendre que les photos
        // URL de l'API Instagram pour récupérer les photos
        $url = "https://graph.instagram.com/me/media?fields=id,caption,media_url,permalink&access_token={$accessToken}&limit={$limit}";
        $photos = []; // Tableau pour stocker les photos
        // Utilisation de file_get_contents pour récupérer la réponse de l'API
        $response = file_get_contents($url);
        // Vérification si une réponse a été reçue
        if ($response) {
            // Décodage de la réponse JSON
            $data = json_decode($response, true);
            // Vérification si la clé "data" est présente dans la réponse
            if (isset($data['data'])) {
                // if $data['media_url'] contient le text "/reel/" alors on ne l'ajoute pas aux photos
                foreach ($data['data'] as $key => $value) {
                    if ($photoOnly && strpos($value['permalink'], '/reel/') === false) {
                        $photos[] = $value;
                    } elseif (!$photoOnly) {
                        $photos[] = $value;
                    }
                }
            }
            // on ne prend que les X premières photos
            $photos = array_slice($photos, 0, Configuration::get($this->configPhotoMax));
        }
        return $photos; // Retourne le tableau des photos
    }
    

    public function hookDisplayHome($params)
    {
        $this->context->controller->addCSS($this->_path . 'views/css/paninsta_gallery.css');
        $this->context->smarty->assign('paninsta_photos', $this->getInstagramPhotos());
        return $this->display(__FILE__, 'views/templates/front/paninsta_gallery.tpl');
    }


}
