<?php
/*
Plugin Name: FAN Courier+
Plugin URI: http://www.fancourier.ro
Description: Modul de livrare FAN Curier refactorizat
Version: 1.3.6
Author: FAN Courier
Author URI: http://fancourier.ro
License: GPL2
*/
if (!defined('ABSPATH')) {
    exit; //Exit if accessed directly (for safety)
}

//varibile globale pentru tip serviciu si link order
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    $dir = plugin_dir_path(__FILE__);
    function fan_courier_init()
    {
        class WC_FAN_Courier extends WC_Shipping_Method
        {
            public $tip_serv;
            function __construct()
            {
                global $woocommerce;
                unset($woocommerce->session->subtotal);
                $this->type               = isset($_POST['payment_method']) ? addslashes($_POST['payment_method']) : '';
                $this->id                 = 'fan_courier';
                $this->method_title       = 'FAN Courier';
                $this->method_description = __('Stimate client, puteti obtine informatii pentru configurare la adresa de email: <a href="mailto:selfawb@fancourier.ro">selfawb@fancourier.ro</a><br>
                                   Va multumim pentru ca folositi serviciile FAN Courier.', 'woocommerce');
                $this->title              = 'FAN Courier';
                $this->enabled            = "yes";
                $this->init();
            }
            function init_form_fields()
            {
                $this->form_fields = array(
                    'enabled' => array(
                        'title' => __('Activare/Dezactivare', 'woocommerce'),
                        'type' => 'checkbox',
                        'label' => __('A se utiliza modulul FAN Courier ', 'woocommerce')
                    ),
                    'FAN_security' => array(
                        'title' => __('Securitate: ', 'woocommerce', 'ceva'),
                        'type' => 'title'
                    ),
                    'FAN_clientID' => array(
                        'title' => __('ID Client *', 'woocommerce'),
                        'type' => 'text',
                        'default' => __('', 'woocommerce'),
                        'description' => __('Client ID oferit de FAN Courier', 'woocommerce'),
                        'desc_tip' => true,
                        'required' => true
                    ),
                    'FAN_clientAccount' => array(
                        'title' => __('Cont utilizator *', 'woocommerce'),
                        'type' => 'text',
                        'default' => __('', 'woocommerce'),
                        'description' => __('Cont utilizator selfAWB', 'woocommerce'),
                        'desc_tip' => true
                    ),
                    'FAN_password' => array(
                        'title' => __('Parola *', 'woocommerce'),
                        'type' => 'text',
                        'default' => __('', 'woocommerce'),
                        'description' => __('Parola cont selfAWB', 'woocommerce'),
                        'desc_tip' => true
                    ),
                    'FAN_confirmAWB' => array(
                        'title' => __('Confirmare AWB de catre Admin', 'woocommerce'),
                        'type' => 'select',
                        'label' => __('', 'woocommerce'),
                        'default' => 'no',
                        'css' => 'width:350px;',
                        'options' => array(
                            'no' => __('No', 'woocommerce'),
                            'yes' => __('Yes', 'woocommerce')
                        )
                    ),
                    'FAN_AWBoptions' => array(
                        'title' => __('Optiuni AWB: ', 'woocommerce'),
                        'type' => 'title'
                    ),
                    'FAN_parcelShipping' => array(
                        'title' => __('Expediere colete', 'woocommerce'),
                        'type' => 'select',
                        'label' => __('', 'woocommerce'),
                        'default' => 'no',
                        'css' => 'width:350px;',
                        'options' => array(
                            'no' => __('No', 'woocommerce'),
                            'yes' => __('Yes', 'woocommerce')
                        )
                    ),
                    'FAN_numberOfParcels' => array(
                        'title' => __('Numar pachete / AWB *', 'woocommerce'),
                        'type' => 'price',
                        'default' => '1',
                        'description' => __('Introduceti un numar intreg. Exemplu: 1 -daca expediati un pachet/ AWB ', 'woocommerce'),
                        'desc_tip' => true
                    ),
                    'FAN_paymentAtDestination' => array(
                        'title' => __('Plata AWB la destinatie', 'woocommerce'),
                        'type' => 'select',
                        'default' => 'no',
                        'css' => 'width:350px;',
                        'options' => array(
                            'no' => __('No', 'woocommerce'),
                            'yes' => __('Yes', 'woocommerce')
                        )
                    ),
                    'FAN_priceWithoutVAT' => array(
                        'title' => __('Afisare pret fara TVA', 'woocommerce'),
                        'type' => 'select',
                        'default' => 'no',
                        'css' => 'width:350px;',
                        'options' => array(
                            'no' => __('No', 'woocommerce'),
                            'yes' => __('Yes', 'woocommerce')
                        )
                    ),
                    'FAN_priceForExtraKm' => array(
                        'title' => __('Afisare pret doar Km suplimentari', 'woocommerce'),
                        'type' => 'select',
                        'default' => 'no',
                        'description' => __('In cazul acestei optiuni este necesar sa se seteze <strong>Plata AWB la destinatie - NU <strong> si <strong>Adaugare taxa transp. la ramburs - DA</strong>', 'woocommerce'),
                        'desc_tip' => true,
                        'css' => 'width:350px;',
                        'options' => array(
                            'no' => __('No', 'woocommerce'),
                            'yes' => __('Yes', 'woocommerce')
                        )
                    ),
                    'FAN_hideShippingRate' => array(
                        'title' => __('Ascundere taxa transport', 'woocommerce'),
                        'type' => 'select',
                        'default' => 'no',
                        'css' => 'width:350px;',
                        'options' => array(
                            'no' => __('No', 'woocommerce'),
                            'yes' => __('Yes', 'woocommerce')
                        )
                    ),
                    'FAN_freeShippingMinimumAmount' => array(
                        'title' => __('Suma minima transport gratuit', 'woocommerce'),
                        'type' => 'price'
                    ),
                    'FAN_bucFixedAmount' => array(
                        'title' => __('Valoare fixa pentru transport in Bucuresti', 'woocommerce'),
                        'type' => 'price'
                    ),
                    'FAN_countryFixedAmount' => array(
                        'title' => __('Valoare fixa pentru transport in tara', 'woocommerce'),
                        'type' => 'price'
                    ),
                    'FAN_reimbursementOptions' => array(
                        'title' => __('Optiuni ramburs: ', 'woocommerce'),
                        'type' => 'title'
                    ),
                    'FAN_askForRbsGoodsValue' => array(
                        'title' => __('Solicitare ramburs valoare marfa', 'woocommerce'),
                        'type' => 'select',
                        'default' => 'yes',
                        'css' => 'width:350px;',
                        'options' => array(
                            'no' => __('No', 'woocommerce'),
                            'yes' => __('Yes', 'woocommerce')
                        )
                    ),
                    'FAN_addShipTaxToRbs' => array(
                        'title' => __('Adaugare taxa transp. la ramburs', 'woocommerce'),
                        'type' => 'select',
                        'default' => 'yes',
                        'css' => 'width:350px;',
                        'options' => array(
                            'no' => __('No', 'woocommerce'),
                            'yes' => __('Yes', 'woocommerce')
                        )
                    ),
                    'FAN_askForRbsInBankAccount' => array(
                        'title' => __('Solicitare ramburs in cont bancar', 'woocommerce'),
                        'type' => 'select',
                        'default' => 'yes',
                        'css' => 'width:350px;',
                        'options' => array(
                            'no' => __('No', 'woocommerce'),
                            'yes' => __('Yes', 'woocommerce')
                        )
                    ),
                    'FAN_rbsPaymentAtDestination' => array(
                        'title' => __('Plata ramburs la destinatie', 'woocommerce'),
                        'type' => 'select',
                        'default' => 'no',
                        'description' => __('Nu se aplica pentru serviciile de tip Cont Colector', 'woocommerce'),
                        'desc_tip' => true,
                        'css' => 'width:350px;',
                        'options' => array(
                            'no' => __('No', 'woocommerce'),
                            'yes' => __('Yes', 'woocommerce')
                        )
                    ),
                    'FAN_insurance' => array(
                        'title' => __('Asigurare: ', 'woocommerce'),
                        'type' => 'title'
                    ),
                    'FAN_askForShipInsurance' => array(
                        'title' => __('Solicitare asigurare de transport', 'woocommerce'),
                        'type' => 'select',
                        'default' => 'no',
                        'css' => 'width:350px;',
                        'options' => array(
                            'no' => __('No', 'woocommerce'),
                            'yes' => __('Yes', 'woocommerce')
                        )
                    ),
                    'FAN_includeProductsCode' => array(
                        'title' => __('Include cod produse la continut', 'woocommerce'),
                        'type' => 'select',
                        'default' => 'no',
                        'css' => 'width:350px;',
                        'options' => array(
                            'no' => __('No', 'woocommerce'),
                            'yes' => __('Yes', 'woocommerce')
                        )
                    ),
                    'FAN_observations' => array(
                        'title' => __('Observatii si note: ', 'woocommerce'),
                        'type' => 'title'
                    ),
                    'FAN_obsOnAWB' => array(
                        'title' => __('Observatii (imprimare pe AWB)', 'woocommerce'),
                        'type' => 'text',
                        'default' => __('', 'woocommerce')
                    ),
                    'FAN_contactPerson' => array(
                        'title' => __('Persoana de contact', 'woocommerce'),
                        'type' => 'text',
                        'default' => __('', 'woocommerce')
                    ),
                    'FAN_availableServices' => array(
                        'title' => __('Servicii specifice disponibile: ', 'woocommerce'),
                        'type' => 'title'
                    ),
                    'FAN_redCodeOption' => array(
                        'title'         => __('Afisare optiune RedCode', 'woocommerce'),
                        'type'             => 'select',
                        'default'         => 'no',
                        'css'             => 'width:350px;',
                        'options'        => array(
                            'no'         => __('No', 'woocommerce'),
                            'yes'         => __('Yes', 'woocommerce')
                        ),
                    ),
                    'FAN_expressLocoOption' => array(
                        'title'         => __('Afisare optiune ExpressLoco', 'woocommerce'),
                        'type'             => 'select',
                        'default'         => 'no',
                        'css'             => 'width:350px;',
                        'options'        => array(
                            'no'         => __('No', 'woocommerce'),
                            'yes'         => __('Yes', 'woocommerce')
                        ),
                    ),
                    'FAN_servicesOptions' => array(
                        'title' => __('Optiuni servicii: ', 'woocommerce'),
                        'type' => 'title'
                    ),
                    'FAN_openOnDelivery' => array(
                        'title' => __('Deschidere la livrare', 'woocommerce'),
                        'type' => 'select',
                        'default' => 'no',
                        'css' => 'width:350px;',
                        'description' => __('In cadrul acestei optiuni este necesar sa se seteze <strong>Plata AWB la destinatie - NU <strong>', 'woocommerce'),
                        'desc_tip' => true,
                        'options' => array(
                            'no' => __('No', 'woocommerce'),
                            'yes' => __('Yes', 'woocommerce')
                        )
                    ),
                    'FAN_epod' => array(
                        'title' => __('Utilizare optiune ePOD', 'woocommerce'),
                        'type' => 'select',
                        'default' => 'no',
                        'css' => 'width:350px;',
                        'desc_tip' => false,
                        'options' => array(
                            'no' => __('No', 'woocommerce'),
                            'yes' => __('Yes', 'woocommerce')
                        )
                    )
                );
            }
            function init()
            {
                // Load the settings API
                $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
                $this->init_settings(); // This is part of the settings API. Loads settings you previously init.
                // Save settings in admin if you have any defined
                add_action('woocommerce_update_options_shipping_' . $this->id, array(
                    $this,
                    'process_admin_options'
                ));
            }
            public function admin_options()
            {
                $url = plugins_url();
                echo '<h3>' . $this->method_title . ' - Romania</h3>
                      <p>' . $this->method_description . '</p>
                    <table class="form-table">';
                $this->generate_settings_html();
                echo '</table>';
            }
            public function request($script, $data = [])
            {
                $FAN_clientId                  = $this->get_option('FAN_clientID');
                $FAN_clientAccount             = $this->get_option('FAN_clientAccount');
                $FAN_password                  = $this->get_option('FAN_password');
                
                $data = $data + [
                    'username'    => $FAN_clientAccount,
                    'user_pass'   => $FAN_password,
                    'client_id'   => $FAN_clientId,
                ]; 
                
                $url = 'https://www.selfawb.ro/' . $script;
                $c   = curl_init($url);
                curl_setopt($c, CURLOPT_POST, true);
                curl_setopt($c, CURLOPT_POSTFIELDS, $data);
                curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($c);
                curl_close($c);
                return $result;
            }
            public function calculate_shipping($package = array())
            {
                global $woocommerce;
                // ------------ campuri modul START ------------------
                $FAN_clientId                  = $this->get_option('FAN_clientID');
                $FAN_clientAccount             = $this->get_option('FAN_clientAccount'); 
                $FAN_password                  = $this->get_option('FAN_password');
                $FAN_confirmAWB                = $this->get_option('FAN_confirmAWB');
                $FAN_parcelShipping            = $this->get_option('FAN_parcelShipping');
                $FAN_numberOfParcels           = $this->get_option('FAN_numberOfParcels');
                $FAN_paymentAtDestination      = $this->get_option('FAN_paymentAtDestination');
                $FAN_priceWithoutVAT           = $this->get_option('FAN_priceWithoutVAT');
                $FAN_priceForExtraKm           = $this->get_option('FAN_priceForExtraKm');
                $FAN_hideShippingRate          = $this->get_option('FAN_hideShippingRate');
                $FAN_freeShippingMinimumAmount = $this->get_option('FAN_freeShippingMinimumAmount');
                $FAN_bucFixedAmount            = $this->get_option('FAN_bucFixedAmount');
                $FAN_countryFixedAmount        = $this->get_option('FAN_countryFixedAmount');
                $FAN_askForRbsGoodsValue       = $this->get_option('FAN_askForRbsGoodsValue');
                $FAN_addShipTaxToRbs           = $this->get_option('FAN_addShipTaxToRbs');
                $FAN_askForRbsInBankAccount    = $this->get_option('FAN_askForRbsInBankAccount');
                $FAN_rbsPaymentAtDestination   = $this->get_option('FAN_rbsPaymentAtDestination');
                $FAN_askForShipInsurance       = $this->get_option('FAN_askForShipInsurance');
                $FAN_includeProductsCode       = $this->get_option('FAN_includeProductsCode');
                $FAN_obsOnAWB                  = $this->get_option('FAN_obsOnAWB');
                $FAN_contactPerson             = $this->get_option('FAN_contactPerson');
                $FAN_openOnDelivery            = $this->get_option('FAN_openOnDelivery');
                $FAN_epod                      = $this->get_option('FAN_epod');
                $FAN_expressLocoOption         = 'no';
                $FAN_redCodeOption             = 'no';
                // -------------campuri modul STOP ----------------------
                $adresaDestinatar              = $package['destination']['address'];
                $adresaDestinatar_2            = $package['destination']['address_2'];
                $orasDestinatar                = remove_accents($package['destination']['city']);
                $judetDestinatar               = $package['destination']['state'];
                $codPostal                     = $package['destination']['postcode'];
                $totalWeight                   = round($woocommerce->cart->cart_contents_weight);
                $cartTotal                     = number_format((float) ($woocommerce->cart->cart_contents_total + $woocommerce->cart->tax_total), 0, '.', '');
                $formmmatedCartTotal           = number_format(round((float) $cartTotal, 2), 2, '.', '');
                $billing_first_name            = $woocommerce->session->get('billing_first_name');
                $billing_last_name             = $woocommerce->session->get('billing_last_name');
                $billing_company               = $woocommerce->session->get('billing_company');
                $billing_email                 = $woocommerce->session->get('billing_email');
                $billing_phone                 = $woocommerce->session->get('billing_phone');
                $shipping_first_name           = $woocommerce->session->get('shipping_first_name');
                $shipping_last_name            = $woocommerce->session->get('shipping_last_name');
                $shipping_to_differen_address  = $woocommerce->session->get('ship_to_different_address');
                if ($shipping_to_differen_address) {
                    $billing_first_name = $shipping_first_name;
                    $billing_last_name  = $shipping_last_name;
                }
                $observatii                    = $FAN_obsOnAWB;
                //
                $judete                        = array(
                    "AB" => "Alba",
                    "AR" => "Arad",
                    "AG" => "Arges",
                    "BC" => "Bacau",
                    "BH" => "Bihor",
                    "BN" => "Bistrita-Nasaud",
                    "BT" => "Botosani",
                    "BR" => "Braila",
                    "BV" => "Brasov",
                    "B" => "Bucuresti",
                    "BZ" => "Buzau",
                    "CL" => "Calarasi",
                    "CS" => "Caras-Severin",
                    "CJ" => "Cluj",
                    "CT" => "Constanta",
                    "CV" => "Covasna",
                    "DB" => "Dambovita",
                    "DJ" => "Dolj",
                    "GL" => "Galati",
                    "GR" => "Giurgiu",
                    "GJ" => "Gorj",
                    "HR" => "Harghita",
                    "HD" => "Hunedoara",
                    "IL" => "Ialomita",
                    "IS" => "Iasi",
                    "IF" => "Ilfov",
                    "MM" => "Maramures",
                    "MH" => "Mehedinti",
                    "MS" => "Mures",
                    "NT" => "Neamt",
                    "OT" => "Olt",
                    "PH" => "Prahova",
                    "SJ" => "Salaj",
                    "SM" => "Satu Mare",
                    "SB" => "Sibiu",
                    "SV" => "Suceava",
                    "TR" => "Teleorman",
                    "TM" => "Timis",
                    "TL" => "Tulcea",
                    "VL" => "Valcea",
                    "VS" => "Vaslui",
                    "VN" => "Vrancea"
                );
                foreach ($judete as $key => $value) {
                    if ($key == $package['destination']['state']) {
                        $judetDestinatar = $value;
                    }
                }
                //------------------------ Conditii FAN START----------------------------
                $is_enabled = $this->enabled;
                //echo $is_enabled;exit;
                if ($is_enabled == 'yes') { // daca este activat plugin FAN si ne aflam in pagina de checkout
                    $min_gratuit            = $FAN_freeShippingMinimumAmount;
                    $valoare_fixa           = $FAN_countryFixedAmount;
                    $valoare_fixa_bucuresti = $FAN_bucFixedAmount;
                    $localitate_dest        = $orasDestinatar;
                    $judet_dest             = $judetDestinatar;
                    if (is_numeric($min_gratuit))
                        $min_gratuit = $min_gratuit + 0;
                    else
                        $min_gratuit = 0 + 0;
                    if (is_numeric($valoare_fixa))
                        $valoare_fixa = $valoare_fixa + 0;
                    else
                        $valoare_fixa = 0 + 0;
                    //sfarsit valoare fixa
                    if (is_numeric($valoare_fixa_bucuresti))
                        $valoare_fixa_bucuresti = $valoare_fixa_bucuresti + 0;
                    else
                        $valoare_fixa_bucuresti = 0 + 0;
                    //sfarsit valoare fixa
                    //$link_standard="";
                    if ($FAN_parcelShipping == 'yes') { // expediere colete
                        $plic = "0";
                        if (is_numeric($FAN_numberOfParcels)) { // numar pachere/AWB
                            $colet = $FAN_numberOfParcels;
                        } else {
                            $colet = 1;
                        }
                    } else {
                        $colet = "0";
                        if (is_numeric($FAN_numberOfParcels)) {
                            $plic = $FAN_numberOfParcels;
                        } else {
                            $plic = 1;
                        }
                    }
                    if ($FAN_addShipTaxToRbs == 'yes') { // adaugare taxa transport la ramburs
                        $totalrb = "1";
                    } else {
                        $totalrb = "0";
                    }
                    if ($FAN_askForShipInsurance == 'yes') { // solicitare asigurare de transport
                        $valoaredeclarata = $cartTotal;
                    } else {
                        $valoaredeclarata = 0;
                    }
                    $greutate = $totalWeight;
                    if ($greutate > 1) {
                        $plic = 0;
                        if (is_numeric($FAN_numberOfParcels)) {
                            $colet = $FAN_numberOfParcels;
                        } else {
                            $colet = 1;
                        }
                    }
                    if (round((float) $totalWeight, 0) > 5) { // daca greutate > 5, nu face REDCODE
                        $redcode = false;
                    }
                    $lungime  = 0;
                    $latime   = 0;
                    $inaltime = 0;
                    if ($FAN_paymentAtDestination == 'yes') { // plata AWB la destinatie
                        $plata_expeditiei = "destinatar";
                    } else {
                        $plata_expeditiei = "expeditor";
                    }
                    $rambursare               = '';
                    $rambursare_number        = 0 + 0;
                    $plata_expeditiei_ramburs = "";
                    if ((strtolower($localitate_dest) == "bucuresti") && is_numeric($valoare_fixa_bucuresti)) { // valoare fixa transport in Bucuresti
                        $valoare_fixa = $valoare_fixa_bucuresti;
                    }
                    //----------------------- Solicita Ramburs valoare marfa START -----------------------------
                    if ($FAN_askForRbsGoodsValue == 'yes') { //solicitare ramburs valoare marfa
                        if ($FAN_askForRbsInBankAccount == 'yes') { //solicitare ramburs in cont bancar
                            $rambursare        = number_format(round((float) $cartTotal, 2), 2, '.', '');
                            $rambursare_number = round((float) $cartTotal, 2) + 0;
                            if ($min_gratuit < $rambursare_number && $min_gratuit != 0) { // suma minima transport gratuit
                                $totalrb = "0";
                            }
                            if ($FAN_rbsPaymentAtDestination == 'yes') { // plata AWB la destinatie
                                $plata_expeditiei_ramburs = "destinatar";
                            } else {
                                $plata_expeditiei_ramburs = "expeditor";
                            }
                        } else {
                            $rambursare        = (string) number_format(round((float) $cartTotal, 2), 2, '.', '') . " LEI";
                            $rambursare_number = round((float) $cartTotal, 2) + 0;
                            if ($min_gratuit < $rambursare_number && $min_gratuit != 0) { // suma minima transport gratuit
                                $totalrb = "0";
                            }
                            if ($FAN_rbsPaymentAtDestination == 'yes') { // plata AWB la destinatie
                                $plata_expeditiei_ramburs = "destinatar";
                            } else {
                                $plata_expeditiei_ramburs = "expeditor";
                            }
                        }
                    } else {
                        $rambursare_number = round((float) $cartTotal, 2) + 0;
                    }
                    //------------------------------- Solicita Ramburs valoare marfa STOP -----------------------------
                    if ($min_gratuit < $rambursare_number && $min_gratuit != 0) //cand min gratuit mai mic ca ramburs trec automat plata la expeditor
                        {
                        $plata_expeditiei = "expeditor";
                    }
                    if ($judet_dest == "Satu-Mare") {
                        $judet_dest = "Satu Mare";
                    }
                    if ($judet_dest == "Dimbovita") {
                        $judet_dest = "Dambovita";
                    }
                    $continut = '';
                    foreach ($package['contents'] as $values) {
                        if ($values['data']->needs_shipping()) {
                            $continut = $continut . $values['quantity'] . " X " . $values['data']->get_title() . ", "; // populez variabila $continut cu produsele care necesita livrare
                            
                        }
                    }
                    //---------- Preluare adresa, nume, telefon, email START --------------
                    if (!empty($billing_company)) {
                        $nume_destinatar  = $billing_company;
                        $persoana_contact = $billing_first_name . " " . $billing_last_name;
                    } else {
                        $nume_destinatar  = $billing_first_name . " " . $billing_last_name;
                        $persoana_contact = $billing_first_name . " " . $billing_last_name;
                        if (trim($persoana_contact) == trim($persoana_contact)) {
                            $persoana_contact = '';
                        }
                    }
                    $telefon        = $billing_phone;
                    $email          = $billing_email;
                    $strada         = $adresaDestinatar;
                    $strada         = rawurlencode($strada);
                    $temp_adress    = $adresaDestinatar_2;
                    $temp_adress    = rawurlencode($temp_adress);
                    $strada         = $strada . ", " . $temp_adress;
                    $this->tip_serv = $strada;
                    $postalcode     = str_pad($codPostal, 6, "0");
                    if ($FAN_confirmAWB == "yes") {
                        $onlyadm = 1;
                    } else {
                        $onlyadm = 0;
                    }
                    if ($FAN_priceWithoutVAT == "yes") {
                        $fara_tva = 1;
                    } else {
                        $fara_tva = 0;
                    }
                    if ($FAN_priceForExtraKm == "yes") {
                        $doar_km = 1;
                    } else {
                        $doar_km = 0;
                    }
                    $optiuni = '';
                    if ($FAN_openOnDelivery == "yes") {
                        $optiuni .= "A";
                    }
                    if ($FAN_epod == "yes") {
                        $optiuni .= "X";
                    }
                    //---------- Preluare adresa, nume, telefon, email STOP --------------
                    $page = $this->request('order.php', ['return' => 'services']);
                    
                    $servicii_data = explode("\n", ltrim(rtrim($page)));
                    foreach ($servicii_data as $tip_serviciu_info) {
                        $tip_serviciu_info = str_replace('"', '', $tip_serviciu_info);
                        $tip_serviciu      = explode(",", $tip_serviciu_info) + [null, null, null, null, null];

                        $page = $this->request('order.php', [
                            'plata_expeditiei'        => $plata_expeditiei,
                            'tip_serviciu'            => $tip_serviciu[0],
                            'localitate_dest'         => $localitate_dest,
                            'judet_dest'              => $judet_dest,
                            'plic'                    => $plic,
                            'colet'                   => $colet,
                            'greutate'                => $greutate,
                            'lungime'                 => $lungime,
                            'latime'                  => $latime,
                            'inaltime'                => $inaltime,
                            'valoare_declarata'       => $valoaredeclarata,
                            'plata_ramburs'           => $plata_expeditiei_ramburs,
                            'ramburs'                 => $rambursare,
                            'pers_contact_expeditor'  => $FAN_contactPerson,
                            'observatii'              => $observatii,
                            'continut'                => $continut,
                            'nume_destinatar'         => $nume_destinatar,
                            'persoana_contact'        => $persoana_contact,
                            'telefon'                 => $telefon,
                            'email'                   => $email,
                            'strada'                  => $strada,
                            'postalcode'              => $postalcode,
                            'totalrb'                 => $totalrb,
                            'admin'                   => $onlyadm,
                            'fara_tva'                => $fara_tva,
                            'suma_fixa'               => $valoare_fixa,
                            'doar_km'                 => $doar_km,
                            'optiuni'                 => $optiuni,
                        ]);
                        
                        $price = explode("|||", $page);
                        $add_shipping_option = false;
                        if (($FAN_askForRbsInBankAccount == "no" || $FAN_askForRbsGoodsValue == "no")) {
                            if (
                                $tip_serviciu[1] == 0 && 
                                (
                                    ($tip_serviciu[2] == 0 && $tip_serviciu[3] == 0) ||
                                    ($tip_serviciu[2] == 1 && $FAN_redCodeOption == "yes") ||
                                    ($tip_serviciu[3] == 1 && $FAN_expressLocoOption == "yes")
                                )){
                                $add_shipping_option = true;
                            }
                        } else{
                            if (
                                $tip_serviciu[1] == 1 &&
                                (
                                    ($tip_serviciu[2] == 0 && $tip_serviciu[3] == 0) ||
                                    ($tip_serviciu[2] == 1 && $FAN_redCodeOption == 'yes') ||
                                    ($tip_serviciu[3] == 1 && $FAN_expressLocoOption == 'yes')
                                )
                            ){
                                $add_shipping_option = true;
                            }
                        }
                        if ($add_shipping_option) {
                            if (isset($price[1])) {
                                if (($FAN_hideShippingRate == "no") && ($min_gratuit > $rambursare_number || $min_gratuit == 0)) {
                                    $price_standard = $price[0];
                                } else
                                    $price_standard = 0;
                                $link_standard = $price[1];
                            } else {
                                $price_standard = "";
                                $message        = $price[0];
                                $messageType    = "error";
                                if (
                                    !wc_has_notice($message, $messageType) &&
                                    isset($judet_dest) && 
                                    trim($judet_dest) != "" 
                                    && isset($localitate_dest) && 
                                    trim($localitate_dest) != ""
                                    ) {
                                    wc_add_notice($message, $messageType);
                                }
                            }
                            if (is_numeric($price_standard) && $link_standard != "") { // daca am adresa corecta afisez TIP SERVICIU si PRET
                                $args = array(
                                    'id'        => $this->id,
                                    'label'     => "FAN Courier",
                                    'cost'      => $price_standard,
                                    'taxes'     => false,
                                    'meta_data' => array(
                                        'link_id'       => $link_standard,
                                        'tip_serviciu'  => $tip_serviciu[0],
                                    ),
                                );
                                $this->add_rate($args);
                            } else { // daca NU am adresa corecta afisez mesaj eroare
                                if ($tip_serviciu[2] == 0 && $tip_serviciu[3] == 0) {
                                    $args = array();
                                    $this->add_rate($args);
                                }
                            }
                        }
                    }
                } //------------------------ Conditii FAN STOP----------------------------
            }
        }
    }
    add_filter('woocommerce_checkout_fields', 'suprascriere_propr_campuri');
    function suprascriere_propr_campuri($fields)
    {
        $fields['billing']['billing_state']['class'][]  = 'update_totals_on_change';
        $fields['billing']['billing_city']['class'][]   = 'update_totals_on_change';
        $fields['billing']['billing_phone']['class'][]  = 'update_totals_on_change';
        return $fields;
    }
    add_action('woocommerce_shipping_init', 'fan_courier_init');
    function add_fan_courier_method($methods)
    {
        $methods[] = 'WC_FAN_Courier';
        return $methods;
    }
    add_filter('woocommerce_shipping_methods', 'add_fan_courier_method');
    // overwrite fancourier label admin only
    function change_fancourier_label($label, $method){
        if (current_user_can('administrator') && 'fan_courier' === $method->method_id) {
            $link_id           = $method->meta_data['link_id'];
            $tip_serviciu      = $method->meta_data['tip_serviciu'];
            $label .= "<br><a href=\"http://www.selfawb.ro/order.php?order_id=$link_id\" target=\"_blank\"><u>Debug - $tip_serviciu</u></a>";
        }
        return $label;
    }
    add_filter('woocommerce_cart_shipping_method_full_label', 'change_fancourier_label', 10, 3);

    //-----------------------------FAN start-------------------------
    add_filter('woocommerce_checkout_fields', 'custom_wc_checkout_fields');
    function custom_wc_checkout_fields($fields)
    {
        $fields['billing']['billing_state']['required']   = true;
        $fields['shipping']['shipping_state']['required'] = true;
        $fields['billing']['billing_phone']['required'] = true;

        return $fields;
    }
    add_action('woocommerce_checkout_update_order_review', 'get_customer_details');
    function get_customer_details($post_data)
    {
        $update_shipping_awb = true;
        $details = array(
            'billing_first_name',
            'billing_last_name',
            'billing_company',
            'billing_email',
            'billing_phone',
            'shipping_first_name',
            'shipping_last_name',
        );
        $post    = array();
        $vars    = explode('&', $post_data);
        foreach ($vars as $k => $value) {
            $v           = explode('=', urldecode($value));
            $post[$v[0]] = $v[1];
        }
        foreach ($details as $key) {
            if(isset($post[$key])){
                WC()->session->set($key, $post[$key]);
                if (empty($post[$key]) && !in_array($key,  array('billing_company', 'shipping_first_name', 'shipping_last_name'))){
                    $update_shipping_awb = false;
                }
            }
        }
        if(isset($post['ship_to_different_address'])){
            WC()->session->set('ship_to_different_address', true);
        }else{
            WC()->session->set('ship_to_different_address', false);
        }
        if(isset($post['tip_facturare'])){
            if($post['tip_facturare'] == 'pers-fiz'){
                WC()->session->set('billing_company', '');
            }
        }
        if($update_shipping_awb){
            foreach ( WC()->cart->get_shipping_packages() as $package_key => $package ){
                WC()->session->set( 'shipping_for_package_' . $package_key, true);
            }
        }
    }
    
    //-----------------------------FAN stop------------------------
    
}
