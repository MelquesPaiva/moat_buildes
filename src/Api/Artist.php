<?php

namespace Source\Api;

/**
 * @package Source\Api
 */
class Artist extends AbstractApi
{
    /**
     * Artist Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /** 
     * @todo métodos usando curl
     * @route https://moat.ai/api/task/
     * @header Basic: ZGV2ZWxvcGVyOlpHVjJaV3h2Y0dWeQ==
     */
    public function getAll(): void
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://moat.ai/api/task/");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["Basic: ZGV2ZWxvcGVyOlpHVjJaV3h2Y0dWeQ=="]);

        $return = curl_exec($curl);
        curl_close($curl);
    
        $response = json_decode($return);

        var_dump($response);
    }

    /** 
     * @todo métodos usando curl
     * @route https://moat.ai/api/task/?artist_id=<artist_id>
     * @header Basic: ZGV2ZWxvcGVyOlpHVjJaV3h2Y0dWeQ==
     */
    public function getById(array $data): void
    {

    }
}