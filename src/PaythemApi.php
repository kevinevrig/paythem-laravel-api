<?php

namespace Paythem\Ptn;

use App\Http\Controllers\Controller;
use Paythem\Ptn\PTNAPI;

class PaythemApi extends Controller
{
    private object $paythem;
    private string $env;
    private int $appID;

    private string $endpoint;

    public function __construct( string $environment = '', int $appID = 2848, string $iv = '', array $user_credentials = [] )
    {
        $this->env      = $environment;
        $this->appID    = $appID;
        $this->paythem  = new PTNAPI( $this->env, $this->appID );

        $this->paythem->PUBLIC_KEY  = $user_credentials['PUBLIC_KEY'];
        $this->paythem->PRIVATE_KEY = $user_credentials['PRIVATE_KEY'];
        $this->paythem->USERNAME    = $user_credentials['USERNAME'];
        $this->paythem->PASSWORD    = $user_credentials['PASSWORD'];
        $this->paythem->SERVER_URI  = 'https://vvs'.$this->env.'.paythem.net/API';
    }

    /**
     * Get Vouchers from Paythem
     *
     * @param bool $json
     * @param bool $debug
     * @return null
     */
    public function getVouchers( bool $json = false, bool $debug = false )
    {
        return $this->apiCall( 'get_Vouchers', $json, $debug );
    }

    /**
     * Get Account Balance from Paythem
     *
     * @param bool $json
     * @param bool $debug
     * @return null
     */
    public function getAccountBalance( bool $json = false, bool $debug = false )
    {
        return $this->apiCall( 'get_AccountBalance', $json, $debug );
    }

    /**
     * Get Product List from Paythem
     *
     * @param bool $json
     * @param bool $debug
     * @return null
     */
    public function getProductList( bool $json = false, bool $debug = false )
    {
        return $this->apiCall( 'get_ProductList', $json, $debug );
    }

    /**
     * Get Brand List from Paythem
     *
     * @param bool $json
     * @param bool $debug
     * @return null
     */
    public function getBrandList( bool $json = false, bool $debug = false )
    {
        return $this->apiCall( 'get_BrandList', $json, $debug );
    }

    /**
     * Get OEM List from Paythem
     *
     * @param bool $json
     * @param bool $debug
     * @return null
     */
    public function getOEMList( bool $json = false, bool $debug = false )
    {
        return $this->apiCall( 'get_OEMList', $json, $debug );
    }

    /**
     * To make API Calls
     *
     * @param string $endpoint
     * @param bool $json
     * @param bool $debug
     * @return void
     */
    private function apiCall( string $endpoint, bool $json = false, bool $debug = false )
    {
        $this->paythem->FUNCTION = $endpoint;

        $response = $this->paythem->callAPI( $debug );

        if( $json === false ):
            return $response;
        else:
            return json_encode($response);
        endif;
    }
}
