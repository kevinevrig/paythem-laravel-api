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

    public array $apiParameters;
    public string $reference_id;

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

        $this->apiParameters = array();
        $this->reference_id = '';
    }

    /**
     * Make API Calls
     *
     * @param string $endpoint
     * @param bool $json
     * @param bool $debug
     * @return mixed
     */
    private function apiCall( string $endpoint, bool $json = false, bool $debug = false ): mixed
    {
        $this->paythem->FUNCTION = $endpoint;
        $this->paythem->PARAMETERS = $this->apiParameters;

        $response = $this->paythem->callAPI( $debug );

        if( $json === false ):
            return $response;
        else:
            return json_encode($response);
        endif;
    }

    /**
     * Get a full list of OEMs in the VVS system.
     *
     * @param bool $json
     * @param bool $debug
     * @return mixed
     */
    public function getOEMList( bool $json = false, bool $debug = false ): mixed
    {
        return $this->apiCall( 'get_OEMList', $json, $debug );
    }

    /**
     * Get a full list of brands by OEMs in the VVS system.
     *
     * @param bool $json
     * @param bool $debug
     * @return mixed
     */
    public function getBrandList( bool $json = false, bool $debug = false ): mixed
    {
        return $this->apiCall( 'get_BrandList', $json, $debug );
    }

    /**
     * Get a full list of active products in the VVS system by brand by OEM.
     * Recommended to perform once a day.
     *
     * @param bool $json
     * @param bool $debug
     * @return mixed
     */
    public function getProductList( bool $json = false, bool $debug = false ): mixed
    {
        return $this->apiCall( 'get_ProductList', $json, $debug );
    }

    /**
     * Get the company’s current account balance.
     * Balance is returned on each sales transaction.
     * Recommended to perform every 15 minutes.
     *
     * @param bool $json
     * @param bool $debug
     * @return mixed
     */
    public function getAccountBalance( bool $json = false, bool $debug = false ): mixed
    {
        return $this->apiCall( 'get_AccountBalance', $json, $debug );
    }

    /**
     * Get a specific product’s availability.
     * Recommended to perform before each sale with selling product’s ID.
     *
     * @param int $product_id
     * @param bool $json
     * @param bool $debug
     * @return mixed
     */
    public function isAvailable( int $product_id, bool $json = false, bool $debug = false ): mixed
    {
        $this->apiParameters = array(
            "PRODUCT_ID" => $product_id
        );
        return $this->apiCall( 'get_ProductAvailability', $json, $debug );
    }

    /**
     * Get all products’ availability.
     * Recommended to perform every 15 minutes.
     *
     * @param bool $json
     * @param bool $debug
     * @return mixed
     */
    public function getAllAvailableProducts( bool $json = false, bool $debug = false ): mixed
    {
        return $this->apiCall( 'get_AllProductAvailability', $json, $debug );
    }

    /**
     * Get a single or multiple vouchers from VVS.
     * It generates a transaction on VVS which can later be queried.
     *
     * @param int $product_id
     * @param int $quantity
     * @param bool $json
     * @param bool $debug
     * @return mixed
     */
    public function buyVouchers( int $product_id, int $quantity, bool $json = false, bool $debug = false ): mixed
    {
        $this->apiParameters = array(
            'PRODUCT_ID'    => $product_id,
            'QUANTITY'      => $quantity,
            'REFERENCE_ID'  => $this->reference_id
        );

        return $this->apiCall( 'get_Vouchers', $json, $debug );
    }

    /**
     * Get all vouchers purchased on the last sale
     *
     * @param bool $json
     * @param bool $debug
     * @return mixed
     */
    public function getLastSale( bool $json = false, bool $debug = false ): mixed
    {
        return $this->apiCall( 'get_LastSale', $json, $debug );
    }

    /**
     * Get all vouchers purchased during a specific period for API user.
     *
     * TODO: SaleTransactions : 30 days calculation remaining
     *
     * @param string $from_date
     * @param string $to_date
     * @param bool $json
     * @param bool $debug
     * @return mixed
     */
    public function getSaleTransactions( string $from_date, string $to_date, bool $json = false, bool $debug = false ): mixed
    {
        $this->apiParameters = array(
            'FROM_DATE' => date("Y-m-d", strtotime($from_date)),
            'TO_DATE'   => date("Y-m-d", strtotime($to_date))
        );

        return $this->apiCall( 'get_SalesTransaction_ByDateRange', $json, $debug );
    }

    /**
     * Get all financial transactions (deposits, credits, reversals, purchases, etc) for company.
     *
     * TODO: FinancialTransaction : 30 days calculation remaining
     *
     * @param string $from_date
     * @param string $to_date
     * @param bool $json
     * @param bool $debug
     * @return mixed
     */
    public function getFinancialTransaction( string $from_date, string $to_date, bool $json = false, bool $debug = false ): mixed
    {
        $this->apiParameters = array(
            'FROM_DATE' => date("Y-m-d", strtotime($from_date)),
            'TO_DATE'   => date("Y-m-d", strtotime($to_date))
        );

        return $this->apiCall( 'get_FinancialTransaction_ByDateRange', $json, $debug );
    }

    /**
     * Retrieves all voucher formats for products.
     *
     * NOTE: This function is not available in development phase.
     *
     * @param bool $json
     * @param bool $debug
     * @return mixed
     */
    public function getProductFormats( bool $json = false, bool $debug = false ): mixed
    {
        return $this->apiCall( 'get_ProductFormats', $json, $debug );
    }
}
