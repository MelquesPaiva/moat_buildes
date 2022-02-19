<?php

namespace Source\Api;

class Artist extends AbstractApi
{
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