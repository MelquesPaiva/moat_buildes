<?php

namespace Source\Api;

use Source\Models\Album as ModelsAlbum;

/**
 * @package Source\Api
 */
class Album extends AbstractApi
{
    /**
     * Album Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get the list of albums
     *
     * @return void
     */
    public function getAll(): void
    {
        $albumList = (new ModelsAlbum())->findAll();
        if (empty($albumList)) {
            $this->response->noContent();
            return;
        }

        $albumResult = array_map(function($album) {
            return $album->data();
        }, $albumList);

        $this->response->successful("The list of albums has been successfully recovered", $albumResult);
    }

    /**
     * Save a album
     *
     * @return void
     */
    public function save(): void
    {
        $album = new ModelsAlbum();
        $saveData = $this->jsonData();

        $album->artist = $saveData->artist ?? null;
        $album->year = $saveData->year ?? null;
        $album->album_name = $saveData->album_name ?? null;

        $albumSaved = $album->save();

        if ($album->fail()) {
            $this->response->internalError($album->message()->getMessage());
            return;
        }

        if (!$albumSaved) {
            $this->response->badRequest($album->message()->getMessage(), "album");
            return;
        }

        $this->response->successful("The album has been saved", (array) $album->data());
    }

    /**
     * Get a specific album by ID
     *
     * @param array $params
     * @return void
     */
    public function getById(array $params): void
    {
        $params = filter_var_array($params, FILTER_VALIDATE_INT);
        $id = $params['id'] ?? null;
        if (empty($id)) {
            $this->response->badRequest("The param ID is required", "id");
            return;
        }

        $album = (new ModelsAlbum())->findOneById($id);
        if (empty($album)) {
            $this->response->noContent();
            return;
        }

        $this->response->successful("The album has been successfully recovered", (array) $album->data());
    }

    /**
     * Update a album
     *
     * @param array $params
     * @return void
     */
    public function update(array $params): void
    {
        $params = filter_var_array($params, FILTER_VALIDATE_INT);
        $id = $params['id'] ?? null;
        if (empty($id)) {
            $this->response->badRequest("The param ID is required", "id");
            return;
        }

        $album = (new ModelsAlbum())->findOneById($id);
        if (empty($album)) {
            $this->response->badRequest("The album informed does not exist", "album");
            return;
        }

        $saveData = $this->jsonData();

        $album->artist = $saveData->artist ?? null;
        $album->year = $saveData->year ?? null;
        $album->album_name = $saveData->album_name ?? null;

        $albumSaved = $album->save();

        if ($album->fail()) {
            $this->response->internalError($album->message()->getMessage());
            return;
        }

        if (!$albumSaved) {
            $this->response->badRequest($album->message()->getMessage(), "album");
            return;
        }

        $this->response->successful("The album has been saved", (array) $album->data());
    }

    /**
     * Delete a album
     * 
     * @param array $params
     * @return void
     */
    public function delete(array $params): void
    {
        if ($this->user->role != 2) {
            $this->response->actionForbidden("You don't have enough permissions to delete a album");
            return;
        }

        $params = filter_var_array($params, FILTER_VALIDATE_INT);
        $id = $params['id'] ?? null;
        if (empty($id)) {
            $this->response->badRequest("The param ID is required", "id");
            return;
        }

        $album = (new ModelsAlbum())->findOneById($id);
        if (empty($album)) {
            $this->response->badRequest("The album informed does not exist", "album");
            return;
        }
        if (!$album->destroy()) {
            $this->response->internalError("A error occurred while deleting the album");
            return;
        }
        
        $this->response->successful("The album has been deleted successfully", ["id" => $id]);
    }
}
